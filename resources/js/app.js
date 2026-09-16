const workspace = document.querySelector('[data-mizan-workspace]');

if (workspace) {
    const state = { user: null, project: null, document: null, categories: [] };
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const message = document.querySelector('[data-workspace-message]');
    const projectsList = document.querySelector('[data-projects]');
    const documentsSection = document.querySelector('[data-documents-section]');
    const inventorySection = document.querySelector('[data-inventory-section]');
    const documentsList = document.querySelector('[data-documents-list]');
    const termsList = document.querySelector('[data-terms-list]');

    const request = async (path, options = {}) => {
        const response = await fetch(`/api/v1${path}`, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token, ...(options.body ? { 'Content-Type': 'application/json' } : {}), ...options.headers },
            ...options,
        });
        if (response.status === 204) return null;
        const body = await response.json();
        if (!response.ok) throw new Error(body.message || Object.values(body.errors || {}).flat().join(' ') || 'The request could not be completed.');
        return body;
    };

    const notify = (text, error = false) => {
        message.textContent = text;
        message.hidden = false;
        message.classList.toggle('is-error', error);
    };

    const element = (tag, text, className) => {
        const node = document.createElement(tag);
        if (text) node.textContent = text;
        if (className) node.className = className;
        return node;
    };

    const setOptions = (select, values, placeholder) => {
        select.replaceChildren();
        if (placeholder !== undefined) select.append(new Option(placeholder, ''));
        values.forEach((value) => select.append(new Option(value.name, value.id)));
    };

    const renderProjects = (projects) => {
        projectsList.replaceChildren();
        projectsList.className = 'workspace-list';
        if (!projects.length) {
            projectsList.append(element('p', 'No projects yet. Create the first research project.'));
            return;
        }
        projects.forEach((project) => {
            const button = element('button'); button.type = 'button';
            button.setAttribute('aria-current', String(state.project?.id === project.id));
            button.append(element('strong', project.name));
            button.append(element('small', `${project.organisation.name} · ${project.documents_count} document${project.documents_count === 1 ? '' : 's'}`));
            button.addEventListener('click', () => selectProject(project));
            projectsList.append(button);
        });
    };

    const analyseDocument = async (sourceDocument, trigger = null) => {
        const currentVersion = sourceDocument.versions.reduce((latest, version) => version.version_number > latest.version_number ? version : latest, sourceDocument.versions[0]);
        if (trigger) { trigger.disabled = true; trigger.textContent = 'Analysing…'; }
        notify('AI is analysing the document for cultural terms. This may take a few seconds.');
        try {
            const analysis = await request(`/document-versions/${currentVersion.id}/analyze-cultural-terms`, { method: 'POST', body: JSON.stringify({}) });
            notify(`${analysis.proposed_terms} AI-proposed cultural terms are ready for researcher review.`);
            await selectDocument(sourceDocument);
        } catch (error) {
            notify(`AI analysis could not run: ${error.message}`, true);
        } finally {
            if (trigger) { trigger.disabled = false; trigger.textContent = 'Analyse cultural terms'; }
        }
    };

    const renderDocuments = (documents) => {
        documentsList.replaceChildren(); documentsList.className = 'workspace-list';
        if (!documents.length) { documentsList.append(element('p', 'No documents in this project yet.')); return; }
        documents.forEach((document) => {
            const button = element('button'); button.type = 'button';
            button.setAttribute('aria-current', String(state.document?.id === document.id));
            button.append(element('strong', document.title));
            button.append(element('small', `${document.versions.length} version${document.versions.length === 1 ? '' : 's'} · ${document.source_language}`));
            button.addEventListener('click', () => selectDocument(document));
            documentsList.append(button);
            const analyse = element('button', 'Analyse cultural terms', 'secondary-action');
            analyse.type = 'button';
            analyse.addEventListener('click', () => analyseDocument(document, analyse));
            documentsList.append(analyse);
        });
    };

    const renderTerms = (terms) => {
        termsList.replaceChildren();
        if (!terms.length) { termsList.append(element('p', 'No cultural terms have been recorded for this version.')); return; }
        terms.forEach((term) => {
            const row = element('article', null, 'term-row');
            row.append(element('strong', `${term.source_phrase} — ${term.category.name}`));
            if (term.source_context) row.append(element('p', term.source_context));
            if (term.selected_for_audit) row.append(element('small', 'Selected for audit'));
            termsList.append(row);
        });
    };

    const selectProject = async (project) => {
        state.project = project; state.document = null;
        documentsSection.hidden = false; inventorySection.hidden = true;
        document.querySelector('[data-breadcrumb]').textContent = project.name;
        renderProjects(await request('/projects'));
        const documents = await request(`/projects/${project.id}/documents`);
        renderDocuments(documents);
        if (documents[0]) await selectDocument(documents[0]);
    };

    const selectDocument = async (document) => {
        state.document = document; inventorySection.hidden = false;
        renderDocuments(await request(`/projects/${state.project.id}/documents`));
        const currentVersion = document.versions.reduce((latest, version) => version.version_number > latest.version_number ? version : latest, document.versions[0]);
        renderTerms(await request(`/document-versions/${currentVersion.id}/cultural-terms`));
    };

    document.querySelectorAll('[data-toggle]').forEach((button) => button.addEventListener('click', () => {
        const form = document.querySelector(`[data-${button.dataset.toggle}]`);
        form.hidden = !form.hidden;
    }));

    document.querySelector('[data-project-form]').addEventListener('submit', async (event) => {
        event.preventDefault(); const form = event.currentTarget; const data = Object.fromEntries(new FormData(form));
        try { const project = await request(`/organisations/${data.organisation_id}/projects`, { method: 'POST', body: JSON.stringify(data) }); form.reset(); form.hidden = true; notify('Project created.'); await selectProject(project); } catch (error) { notify(error.message, true); }
    });

    document.querySelector('[data-document-form]').addEventListener('submit', async (event) => {
        event.preventDefault(); const form = event.currentTarget; const data = Object.fromEntries(new FormData(form));
        try { const document = await request(`/projects/${state.project.id}/documents`, { method: 'POST', body: JSON.stringify(data) }); form.reset(); form.hidden = true; await selectDocument(document); await analyseDocument(document); } catch (error) { notify(error.message, true); }
    });

    document.querySelector('[data-term-form]').addEventListener('submit', async (event) => {
        event.preventDefault(); const form = event.currentTarget; const data = Object.fromEntries(new FormData(form));
        data.selected_for_audit = form.elements.selected_for_audit.checked;
        if (!data.subcategory_id) delete data.subcategory_id;
        const currentVersion = state.document.versions.reduce((latest, version) => version.version_number > latest.version_number ? version : latest, state.document.versions[0]);
        try { await request(`/document-versions/${currentVersion.id}/cultural-terms`, { method: 'POST', body: JSON.stringify(data) }); form.reset(); form.hidden = true; notify('Cultural term recorded.'); renderTerms(await request(`/document-versions/${currentVersion.id}/cultural-terms`)); } catch (error) { notify(error.message, true); }
    });

    const categorySelect = document.querySelector('[data-categories]');
    categorySelect.addEventListener('change', () => {
        const category = state.categories.find((item) => item.id === Number(categorySelect.value));
        setOptions(document.querySelector('[data-subcategories]'), category?.subcategories || [], 'No subcategory');
    });

    (async () => {
        try {
            const [user, categories, projects] = await Promise.all([request('/auth/me'), request('/taxonomy/cultural-categories'), request('/projects')]);
            state.user = user; state.categories = categories;
            document.querySelector('[data-user-name]').textContent = user.name;
            document.querySelector('[data-user-initial]').textContent = user.name.slice(0, 1).toUpperCase();
            setOptions(document.querySelector('[data-organisations]'), user.organisations);
            setOptions(categorySelect, categories, 'Choose a category');
            renderProjects(projects);
            if (projects[0]) await selectProject(projects[0]);
        } catch (error) { notify(error.message, true); }
    })();
}

const auditWorkspace = document.querySelector('[data-audit-workspace]');
if (auditWorkspace) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const state = { project: null, draft: null, documents: [], prompts: [], models: [] };
    const note = document.querySelector('[data-audit-message]');
    const api = async (path, options = {}) => {
        const response = await fetch(`/api/v1${path}`, { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.body ? { 'Content-Type': 'application/json' } : {}) }, ...options });
        const data = response.status === 204 ? null : await response.json();
        if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'Request failed.');
        return data;
    };
    const say = (text, bad = false) => { note.textContent = text; note.hidden = false; note.classList.toggle('is-error', bad); };
    const add = (parent, tag, text) => { const item = document.createElement(tag); item.textContent = text; parent.append(item); return item; };
    const selected = (name) => [...document.querySelectorAll(`[data-audit-${name}] input:checked`)].map((input) => Number(input.value));
    const checkList = (host, items, label, key = 'id') => { host.replaceChildren(); items.forEach((item) => { const wrap = document.createElement('label'); wrap.className = 'check-label'; const box = document.createElement('input'); box.type = 'checkbox'; box.value = item[key]; box.checked = true; wrap.append(box, document.createTextNode(` ${label(item)}`)); host.append(wrap); }); };
    const loadDocuments = async () => { state.documents = await api(`/projects/${state.project.id}/documents`); const select = document.querySelector('[data-audit-version]'); select.replaceChildren(); state.documents.forEach((doc) => doc.versions.forEach((version) => select.append(new Option(`${doc.title} · version ${version.version_number}`, version.id)))); await loadTerms(); };
    const loadTerms = async () => { const version = document.querySelector('[data-audit-version]').value; if (!version) return; const terms = await api(`/document-versions/${version}/cultural-terms`); checkList(document.querySelector('[data-audit-terms]'), terms.filter((term) => term.selected_for_audit), (term) => `${term.source_phrase} (${term.category.name})`); };
    const loadGenerations = async (audit) => { const host = document.querySelector('[data-generation-list]'); host.replaceChildren(); const generations = await api(`/audits/${audit.id}/generations`); if (!generations.length) add(host, 'p', 'No generation jobs exist for this audit.'); generations.forEach((generation) => add(host, 'p', `${generation.model.model_name_snapshot} · ${generation.prompt.code} — ${generation.status}`)); };
    const loadAuditHistory = async () => { const audits = await api(`/projects/${state.project.id}/audits`); const host = document.querySelector('[data-audit-list]'); host.replaceChildren(); audits.forEach((audit) => { const button = document.createElement('button'); button.type = 'button'; button.textContent = `${audit.name} — ${audit.status} · ${audit.terms_count} terms · ${audit.models_count} models`; button.addEventListener('click', async () => { try { if (audit.status === 'READY_TO_GENERATE') { const result = await api(`/audits/${audit.id}/generations`, { method: 'POST' }); say(`${result.queued} generation job(s) queued.`); } await loadGenerations(audit); } catch (error) { say(error.message, true); } }); host.append(button); }); };
    const loadConfig = async () => { state.models = await api(`/projects/${state.project.id}/model-configurations`); state.prompts = await api('/prompt-versions'); checkList(document.querySelector('[data-audit-models]'), state.models, (model) => `${model.display_name} (${model.provider})`); checkList(document.querySelector('[data-audit-prompts]'), state.prompts, (prompt) => `${prompt.template.code} v${prompt.version_number}`); };
    document.querySelector('[data-audit-project]').addEventListener('change', async (event) => { state.project = { id: event.target.value }; try { await loadDocuments(); await loadAuditHistory(); } catch (error) { say(error.message, true); } });
    document.querySelector('[data-audit-version]').addEventListener('change', () => loadTerms().catch((error) => say(error.message, true)));
    document.querySelector('[data-audit-form]').addEventListener('submit', async (event) => { event.preventDefault(); const data = Object.fromEntries(new FormData(event.currentTarget)); data.blind_expert_review = event.currentTarget.elements.blind_expert_review.checked; try { state.draft = await api(`/projects/${state.project.id}/audits`, { method: 'POST', body: JSON.stringify(data) }); document.querySelector('[data-audit-config]').hidden = false; await loadConfig(); await loadAuditHistory(); say('Draft audit created. Add models and freeze the selected configuration.'); } catch (error) { say(error.message, true); } });
    document.querySelector('[data-model-form]').addEventListener('submit', async (event) => { event.preventDefault(); try { await api(`/projects/${state.project.id}/model-configurations`, { method: 'POST', body: JSON.stringify(Object.fromEntries(new FormData(event.currentTarget))) }); event.currentTarget.reset(); await loadConfig(); say('Model configuration added.'); } catch (error) { say(error.message, true); } });
    document.querySelector('[data-freeze-audit]').addEventListener('click', async () => { if (!state.draft) return; try { await api(`/audits/${state.draft.id}/freeze`, { method: 'POST', body: JSON.stringify({ term_ids: selected('terms'), model_configuration_ids: selected('models'), prompt_version_ids: selected('prompts') }) }); await loadAuditHistory(); say('Audit frozen. Its source, terms, prompts, and model settings are now reproducible.'); } catch (error) { say(error.message, true); } });
    (async () => { try { const projects = await api('/projects'); const select = document.querySelector('[data-audit-project]'); projects.forEach((project) => select.append(new Option(project.name, project.id))); if (projects[0]) { state.project = projects[0]; select.value = projects[0].id; await loadDocuments(); await loadAuditHistory(); } } catch (error) { say(error.message, true); } })();
}

const resultsWorkspace = document.querySelector('[data-results-workspace]');
if (resultsWorkspace) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const projectSelect = document.querySelector('[data-results-project]');
    const auditSelect = document.querySelector('[data-results-audit]');
    const list = document.querySelector('[data-results-list]');
    const note = document.querySelector('[data-results-message]');
    const api = async (path) => { const response = await fetch(`/api/v1${path}`, { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf } }); const body = await response.json(); if (!response.ok) throw new Error(body.message || 'Could not load results.'); return body; };
    const message = (text) => { note.textContent = text; note.hidden = false; };
    const showScores = async () => { list.replaceChildren(); if (!auditSelect.value) return; try { const scores = await api(`/audits/${auditSelect.value}/scores`); if (!scores.length) { list.textContent = 'No researcher-confirmed, submitted ratings are available yet.'; return; } scores.forEach((score) => { const row = document.createElement('article'); row.className = 'term-row'; const title = document.createElement('strong'); title.textContent = score.name; const detail = document.createElement('p'); detail.textContent = `SKB: ${score.skb ? score.skb.score.toFixed(3) : 'Not available'} · IKG: ${score.ikg ? score.ikg.score.toFixed(3) : 'Not available'}`; const metadata = document.createElement('small'); metadata.textContent = score.skb ? `${score.skb.evaluated_units} evaluated units; ${score.skb.missing_units} missing` : 'No submitted ratings'; row.append(title, detail, metadata); list.append(row); }); } catch (error) { message(error.message); } };
    const loadAudits = async () => { auditSelect.replaceChildren(); const audits = await api(`/projects/${projectSelect.value}/audits`); audits.filter((audit) => audit.status !== 'DRAFT').forEach((audit) => auditSelect.append(new Option(`${audit.name} (${audit.status})`, audit.id))); await showScores(); };
    projectSelect.addEventListener('change', () => loadAudits().catch((error) => message(error.message)));
    auditSelect.addEventListener('change', showScores);
    (async () => { try { const projects = await api('/projects'); projects.forEach((project) => projectSelect.append(new Option(project.name, project.id))); if (projects[0]) { projectSelect.value = projects[0].id; await loadAudits(); } } catch (error) { message(error.message); } })();
}

const reportsWorkspace = document.querySelector('[data-reports-workspace]');
if (reportsWorkspace) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const project = document.querySelector('[data-reports-project]'); const audit = document.querySelector('[data-reports-audit]');
    const api = async (path) => { const response = await fetch(`/api/v1${path}`, { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf } }); if (!response.ok) throw new Error('Could not load reports.'); return response.json(); };
    const links = () => { document.querySelector('[data-pdf-report]').href = audit.value ? `/api/v1/audits/${audit.value}/report.pdf` : '#'; document.querySelector('[data-package-report]').href = audit.value ? `/api/v1/audits/${audit.value}/reproducibility-package` : '#'; };
    const load = async () => { audit.replaceChildren(); const audits = await api(`/projects/${project.value}/audits`); audits.filter((item) => item.status !== 'DRAFT').forEach((item) => audit.append(new Option(item.name, item.id))); links(); };
    project.addEventListener('change', () => load().catch(() => {})); audit.addEventListener('change', links);
    (async () => { const projects = await api('/projects'); projects.forEach((item) => project.append(new Option(item.name, item.id))); if (projects[0]) { project.value = projects[0].id; await load(); } })();
}

const translationWorkstation = document.querySelector('[data-translation-workstation]');
if (translationWorkstation) {
    const source = translationWorkstation.querySelector('[data-source-text]');
    const count = translationWorkstation.querySelector('[data-source-count]');
    const sourceLanguage = translationWorkstation.querySelector('[data-source-language]');
    const targetLanguage = translationWorkstation.querySelector('[data-target-language]');
    const analysisModel = translationWorkstation.querySelector('[data-analysis-model]');
    const sourceHeading = translationWorkstation.querySelector('#source-text-heading');
    const targetHeading = translationWorkstation.querySelector('#output-text-heading');
    const outputStatus = translationWorkstation.querySelector('[data-output-status]');
    const output = translationWorkstation.querySelector('[data-output-content]');
    const outputEmpty = translationWorkstation.querySelector('[data-output-empty]');
    const culturalPoints = translationWorkstation.querySelector('[data-cultural-points]');
    const culturalList = translationWorkstation.querySelector('[data-cultural-list]');
    const analyse = translationWorkstation.querySelector('[data-run-analysis]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const languageName = (select) => select.options[select.selectedIndex].text;
    const clearOutput = () => { output.textContent = ''; output.hidden = true; outputEmpty.hidden = false; culturalList.replaceChildren(); culturalPoints.hidden = true; window.dispatchEvent(new Event('mizan3g:analysis-cleared')); };
    const refresh = () => { count.textContent = `${source.value.length.toLocaleString()} / 5,000`; sourceHeading.textContent = languageName(sourceLanguage); targetHeading.textContent = languageName(targetLanguage); if (!source.value.trim()) { outputStatus.textContent = 'Awaiting source text'; clearOutput(); } else { outputStatus.textContent = 'Ready for analysis'; } };
    source.addEventListener('input', refresh); sourceLanguage.addEventListener('change', refresh); targetLanguage.addEventListener('change', refresh);
    translationWorkstation.querySelector('[data-clear-source]').addEventListener('click', () => { source.value = ''; refresh(); source.focus(); });
    translationWorkstation.querySelector('[data-swap-languages]').addEventListener('click', () => { const current = sourceLanguage.value; sourceLanguage.value = targetLanguage.value; targetLanguage.value = current; refresh(); });
    translationWorkstation.querySelector('[data-paste-source]').addEventListener('click', async () => { try { source.value = await navigator.clipboard.readText(); refresh(); } catch { source.focus(); } });
    translationWorkstation.querySelector('[data-copy-output]').addEventListener('click', async () => { if (output.textContent.trim()) await navigator.clipboard.writeText(output.textContent.trim()); });
    analyse.addEventListener('click', async () => {
        if (!source.value.trim()) { outputStatus.textContent = 'Add source text before analysis.'; return; }
        analyse.disabled = true; analyse.textContent = 'Analysing…'; outputStatus.textContent = 'Gemini is translating and identifying cultural points…';
        try {
            const sentences = source.value.match(/[^.!?]+[.!?]+|[^.!?]+$/g)?.map((sentence) => sentence.trim()).filter(Boolean) || [source.value.trim()];
            const results = [];
            for (const [index, sentence] of sentences.entries()) {
                outputStatus.textContent = `Analysing sentence ${index + 1} of ${sentences.length}…`;
                const response = await fetch('/api/v1/workstation/analyze', { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ source_text: sentence, source_language: sourceLanguage.value, target_language: targetLanguage.value, analysis_model: analysisModel.value }) });
                const result = await response.json(); if (!response.ok) throw new Error(result.message || `Sentence ${index + 1} could not be completed.`);
                results.push(result);
            }
            const termsByPhrase = new Map(); results.flatMap((result) => result.terms || []).forEach((term) => termsByPhrase.set(term.source_phrase.toLocaleLowerCase(), term));
            const notices = results.map((result) => result.cultural_notice).filter(Boolean);
            const data = { translation: results.map((result) => result.translation).join(' '), terms: [...termsByPhrase.values()], cultural_notice: notices.length ? `${notices.length} sentence${notices.length === 1 ? '' : 's'} could not produce structured cultural proposals.` : null };
            window.dispatchEvent(new CustomEvent('mizan3g:analysis-complete', { detail: data }));
            output.textContent = data.translation; output.hidden = false; outputEmpty.hidden = true; culturalList.replaceChildren();
            data.terms.forEach((term) => { const item = document.createElement('article'); const title = document.createElement('strong'); title.textContent = term.translated_phrase; const category = document.createElement('span'); category.textContent = `${term.category_name} · Source: ${term.source_phrase}`; const detail = document.createElement('p'); detail.textContent = term.cultural_significance; item.append(title, category, detail); culturalList.append(item); });
            if (data.cultural_notice) { const notice = document.createElement('p'); notice.className = 'cultural-notice'; notice.textContent = data.cultural_notice; culturalList.append(notice); }
            culturalPoints.hidden = data.terms.length === 0 && !data.cultural_notice; outputStatus.textContent = data.cultural_notice || (data.terms.length ? `${data.terms.length} cultural point${data.terms.length === 1 ? '' : 's'} proposed for review` : 'Translation complete; no cultural points proposed.');
        } catch (error) { outputStatus.textContent = error.message; } finally { analyse.disabled = false; analyse.innerHTML = 'Analyse cultural context <span>→</span>'; }
    });
    refresh();
}

const settingsWorkspace = document.querySelector('[data-settings-workspace]');
if (settingsWorkspace) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const projectSelect = settingsWorkspace.querySelector('[data-settings-project]');
    const form = settingsWorkspace.querySelector('[data-settings-form]');
    const list = settingsWorkspace.querySelector('[data-settings-list]');
    const message = settingsWorkspace.querySelector('[data-settings-message]');
    const api = async (path, options = {}) => {
        const response = await fetch(`/api/v1${path}`, { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.body ? { 'Content-Type': 'application/json' } : {}) }, ...options });
        const data = response.status === 204 ? null : await response.json();
        if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'The request could not be completed.');
        return data;
    };
    const say = (text, error = false) => { message.textContent = text; message.hidden = false; message.classList.toggle('is-error', error); };
    const render = (models) => {
        list.replaceChildren();
        if (!models.length) { list.textContent = 'No configurations for this project yet.'; return; }
        models.forEach((model) => { const row = document.createElement('article'); row.className = 'term-row'; const title = document.createElement('strong'); title.textContent = `${model.display_name} · ${model.provider}`; const details = document.createElement('p'); details.textContent = `Model: ${model.provider_model_id} | Environment: ${model.execution_environment} | Temperature: ${model.temperature ?? 'default'} | Top P: ${model.top_p ?? 'default'} | Max tokens: ${model.max_tokens ?? 'default'} | Seed: ${model.seed ?? 'none'}`; row.append(title, details); if (model.notes) { const notes = document.createElement('small'); notes.textContent = model.notes; row.append(notes); } list.append(row); });
    };
    const renderPrompts = (prompts) => {
        const catalog = settingsWorkspace.querySelector('[data-prompt-catalog]'); catalog.replaceChildren();
        prompts.forEach((prompt) => { const row = document.createElement('article'); row.className = 'term-row'; const title = document.createElement('strong'); title.textContent = prompt.template.code + ' v' + prompt.version_number + ' — ' + prompt.template.name; const detail = document.createElement('p'); detail.textContent = prompt.prompt_body; const status = document.createElement('small'); status.textContent = prompt.locked_at ? 'Locked ' + new Date(prompt.locked_at).toLocaleString() : 'Active · ' + prompt.template.orientation + ' orientation'; row.append(title, detail, status); catalog.append(row); });
    };
    const load = async () => { if (!projectSelect.value) return; render(await api(`/projects/${projectSelect.value}/model-configurations`)); };
    projectSelect.addEventListener('change', () => load().catch((error) => say(error.message, true)));
    form.addEventListener('submit', async (event) => {
        event.preventDefault(); const data = Object.fromEntries(new FormData(form)); const projectId = data.project_id; delete data.project_id;
        ['temperature', 'top_p', 'max_tokens', 'seed'].forEach((key) => { if (!data[key]) delete data[key]; });
        try { if (data.parameters_json) data.parameters_json = JSON.parse(data.parameters_json); else delete data.parameters_json; } catch { say('Extra parameters must be valid JSON.', true); return; }
        try { await api(`/projects/${projectId}/model-configurations`, { method: 'POST', body: JSON.stringify(data) }); form.reset(); projectSelect.value = projectId; say('Model configuration saved. Existing frozen audits are unchanged.'); await load(); } catch (error) { say(error.message, true); }
    });
    (async () => { try { const [user, projects, prompts] = await Promise.all([api('/auth/me'), api('/projects'), api('/prompt-versions/catalog')]); const userName = settingsWorkspace.querySelector('[data-settings-user-name]'); const userInitial = settingsWorkspace.querySelector('[data-settings-user-initial]'); if (userName) userName.textContent = user.name; if (userInitial) userInitial.textContent = user.name.slice(0, 1).toUpperCase(); renderPrompts(prompts); projects.forEach((project) => projectSelect.append(new Option(project.name, project.id))); if (projects[0]) { projectSelect.value = projects[0].id; await load(); } } catch (error) { say(error.message, true); } })();
}
