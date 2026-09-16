let analysis = null;

window.addEventListener('mizan3g:analysis-complete', (event) => {
    analysis = event.detail;
    showButton();
});

window.addEventListener('mizan3g:analysis-cleared', () => {
    analysis = null;
    document.querySelector('[data-live-score-button]')?.remove();
    document.querySelector('[data-live-score-results]')?.remove();
});

function showButton() {
    if (!analysis?.terms?.length || document.querySelector('[data-live-score-button]')) return;
    const actions = document.querySelector('.translator-actions');
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'secondary-action';
    button.dataset.liveScoreButton = '';
    button.textContent = 'Analyse for scoring';
    actions.prepend(button);

    button.addEventListener('click', async () => {
        button.disabled = true;
        button.textContent = 'Scoring PA / PB / PC…';
        let failed = false;
        try {
            const source = document.querySelector('[data-source-text]');
            const sourceLanguage = document.querySelector('[data-source-language]');
            const targetLanguage = document.querySelector('[data-target-language]');
            const model = document.querySelector('[data-analysis-model]');
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const response = await fetch('/api/v1/workstation/score', {
                method: 'POST', credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ source_text: source.value, source_language: sourceLanguage.value, target_language: targetLanguage.value, analysis_model: model.value, terms: analysis.terms }),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Scoring failed.');
            render(result);
        } catch (error) {
            failed = true;
            button.textContent = error.message || 'Scoring failed. Please try again.';
        } finally {
            button.disabled = false;
            if (!failed) button.textContent = 'Analyse for scoring';
        }
    });
}

function render(result) {
    let panel = document.querySelector('[data-live-score-results]');
    if (!panel) {
        panel = document.createElement('section');
        panel.className = 'cultural-points cultural-points-panel';
        panel.dataset.liveScoreResults = '';
        document.querySelector('.translator-actions').before(panel);
    }
    panel.replaceChildren();
    const title = document.createElement('div');
    title.innerHTML = '<p>Live cultural scoring</p><small>Temporary · not saved</small>';
    panel.append(title);
    const summary = document.createElement('div');
    summary.className = 'live-score-summary';
    summary.textContent = `${result.ratings.length} cultural points · ${result.eligible_terms} fully rated · SKB ${result.skb ?? '—'} · IKG ${result.ikg ?? '—'}`;
    panel.append(summary);
    const table = document.createElement('table');
    table.className = 'live-score-table';
    table.innerHTML = '<thead><tr><th>Cultural point</th><th>PA Arabic</th><th>PB Arabic</th><th>PC Arabic</th><th>PA</th><th>PB</th><th>PC</th></tr></thead>';
    const body = document.createElement('tbody');
    result.ratings.forEach((item) => {
        const row = document.createElement('tr');
        [item.source_phrase, item.procedure_phrases.PA, item.procedure_phrases.PB, item.procedure_phrases.PC, item.PA, item.PB, item.PC].forEach((value) => {
            const cell = document.createElement('td');
            cell.textContent = value ?? 'Not identified';
            row.append(cell);
        });
        body.append(row);
    });
    table.append(body);
    panel.append(table);
}
