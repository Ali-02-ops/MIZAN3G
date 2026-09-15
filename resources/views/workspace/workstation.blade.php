<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>Workstation · MIZAN3G</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="mizan-shell">
<a class="skip-link" href="#main-content">Skip to main content</a>
<div class="mizan-layout">
    <aside class="mizan-sidebar" aria-label="Primary navigation">
        <div class="brand"><div class="brand-mark" aria-hidden="true">M</div><div><strong>MIZAN3G</strong><span>Cultural Translation Audit</span></div></div>
        <nav class="side-nav"><a class="nav-item" href="{{ route('dashboard') }}#projects">Projects</a><a class="nav-item" href="{{ route('dashboard') }}#documents">Documents</a><a class="nav-item" href="{{ route('dashboard') }}#inventory">Cultural inventory</a><a class="nav-item active" href="{{ route('workspace.screen', 'workstation') }}">Workstation</a><a class="nav-item" href="{{ route('workspace.screen', 'audits') }}">Audit runs</a><a class="nav-item" href="{{ route('workspace.screen', 'expert-reviews') }}">Expert reviews</a><a class="nav-item" href="{{ route('workspace.screen', 'results') }}">Results</a><a class="nav-item" href="{{ route('workspace.screen', 'reports') }}">Reports</a><a class="nav-item" href="{{ route('workspace.screen', 'settings') }}">Settings</a></nav>
        <div class="sidebar-note"><span class="note-dot"></span><div><strong>Research mode</strong><small>Provenance required</small></div></div>
        <div class="sidebar-user"><div class="avatar" data-user-initial>?</div><div><strong data-user-name>Loading…</strong><span>Research workspace</span></div><form method="POST" action="{{ route('logout') }}">@csrf <button type="submit" aria-label="Sign out">Sign out</button></form></div>
    </aside>
<main id="main-content" class="mizan-main workstation-page" data-translation-workstation>
    <header class="topbar"><div class="breadcrumb"><a href="{{ route('dashboard') }}">Workspace</a><b>/</b><strong>Workstation</strong></div></header>
    <section class="workstation-intro">
        <div><p class="eyebrow">Translation workspace</p><h1>Read the cultural <em>context.</em></h1><p>Enter source text on the left. The right panel will show an analysed translation and cultural terms requiring researcher review.</p></div>
        <div class="workstation-status"><span></span><div><strong>Draft session</strong><small>Not saved to a project</small></div></div>
    </section>
    <section class="translator-shell" aria-label="Translation workstation">
        <div class="translator-toolbar"><label>Source language <select data-source-language><option value="ms" selected>Malay</option><option value="ar">Arabic</option><option value="en">English</option><option value="id">Indonesian</option></select></label><button type="button" class="swap-language" data-swap-languages aria-label="Swap source and target languages">⇄</button><label>Target language <select data-target-language><option value="ar" selected>Arabic</option><option value="ms">Malay</option><option value="en">English</option><option value="id">Indonesian</option></select></label><span class="toolbar-divider"></span><label>AI model <select data-analysis-model><option value="qwen" selected>Qwen3 8B · local</option><option value="gemini">Gemini 3.5 Flash</option></select></label><button type="button" class="text-action" data-clear-source>Clear</button></div>
        <div class="translator-panes">
            <section class="translator-pane source-pane" aria-labelledby="source-text-heading"><div class="pane-heading"><div><p>Source text</p><h2 id="source-text-heading">Malay</h2></div><small data-source-count>0 / 5,000</small></div><textarea data-source-text maxlength="5000" placeholder="Paste or write a sentence to analyse its translation and cultural context…" aria-label="Source text"></textarea><div class="pane-footer"><span>Text stays in this draft until you choose to save it.</span><button type="button" class="icon-text-button" data-paste-source>Paste</button></div></section>
            <section class="translator-pane output-pane" aria-labelledby="output-text-heading"><div class="pane-heading"><div><p>Analysed output</p><h2 id="output-text-heading">Arabic</h2></div><button type="button" class="icon-text-button" data-copy-output>Copy</button></div><div class="output-empty" data-output-empty><span class="output-mark">✦</span><strong>Your analysed translation will appear here.</strong><p>Run the analysis after entering source text. Cultural terms will be highlighted for review.</p></div><div class="output-content" data-output-content hidden></div><div class="pane-footer"><span data-output-status>Awaiting source text</span><span>Researcher review required</span></div></section>
        </div>
        <section class="cultural-points cultural-points-panel" data-cultural-points hidden><div><p>Cultural points</p><small>AI proposals · review required</small></div><div data-cultural-list></div></section>
        <div class="translator-actions"><div><strong>Ready to analyse?</strong><span>The analysis connection will be added in the next implementation step.</span></div><button type="button" class="primary-action" data-run-analysis>Analyse cultural context <span>→</span></button></div>
    </section>
    <section class="workstation-guidance"><article><span>01</span><div><h2>Write or paste</h2><p>Draft your source sentence without creating a permanent document first.</p></div></article><article><span>02</span><div><h2>Review proposals</h2><p>Inspect the translation and each AI-proposed cultural term.</p></div></article><article><span>03</span><div><h2>Save to audit</h2><p>Later, attach the reviewed result to a versioned project document.</p></div></article></section>
</main>
</div>
</body>
</html>
