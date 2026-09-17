<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MIZAN3G | Cultural Translation Audit</title>
    @vite(['resources/css/app.css'])
</head>
<body class="mizan-shell">
<a class="skip-link" href="#main-content">Skip to main content</a>
<div class="mizan-layout">
    <aside class="mizan-sidebar" aria-label="Primary navigation">
        <div class="brand"><div class="brand-mark" aria-hidden="true">M</div><div><strong>MIZAN3G</strong><span>Cultural Translation Audit</span></div></div>
        <nav class="side-nav"><a class="nav-item active" href="{{ route('dashboard') }}">Home</a><a class="nav-item" href="{{ route('workspace.screen', 'workstation') }}">Workstation</a><a class="nav-item" href="{{ route('workspace.screen', 'settings') }}">Settings</a></nav>
        <div class="sidebar-note"><span class="note-dot"></span><div><strong>Research mode</strong><small>Provenance required</small></div></div>
        <div class="sidebar-user"><div class="avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div><div><strong>{{ auth()->user()->name }}</strong><span>Research workspace</span></div><form method="POST" action="{{ route('logout') }}">@csrf <button type="submit" aria-label="Sign out">Sign out</button></form></div>
    </aside>
    <main id="main-content" class="mizan-main home-page">
        <header class="topbar"><div class="breadcrumb"><span>Research workspace</span><b>/</b><strong>Home</strong></div></header>
        <article class="home-article">
            <header class="home-hero"><p class="eyebrow">About MIZAN3G</p><p class="home-date">Cultural translation research platform</p><h1>Translation should carry <em>culture</em>, not leave it behind.</h1><p class="home-lead">MIZAN3G is a research workspace for examining how AI translation systems handle culturally significant Malay language, practices, objects, beliefs, and social meaning.</p></header>
            <section class="home-story" aria-labelledby="purpose-heading"><div><p class="eyebrow">The purpose</p><h2 id="purpose-heading">A clearer way to study cultural fidelity.</h2></div><p>Literal accuracy is not enough when a text contains a ceremony, a food tradition, a kinship term, or a community practice. MIZAN3G helps researchers compare translations, identify where cultural meaning shifts, and document the evidence behind each finding.</p></section>
            <section class="home-notes" aria-label="What MIZAN3G does"><article><span>01</span><h2>Read the source closely</h2><p>Start with Malay source text and identify the cultural terms that need careful interpretation.</p></article><article><span>02</span><h2>Compare translations</h2><p>Use the workstation to produce and review AI-assisted translations across defined procedures.</p></article><article><span>03</span><h2>Keep the evidence</h2><p>Record cultural-term decisions, model settings, and research context for reproducible review.</p></article></section>
            <section class="home-story home-story--reverse" aria-labelledby="method-heading"><div><p class="eyebrow">The method</p><h2 id="method-heading">Built for careful comparison, not automatic judgement.</h2></div><p>The platform supports a structured audit of cultural fidelity and translation instability. Human researchers remain responsible for reviewing terms, assessing context, and interpreting results. AI suggestions are starting points, not final conclusions.</p></section>
            <aside class="home-principles" aria-labelledby="principles-heading"><p class="eyebrow">Research principles</p><h2 id="principles-heading">What guides the work</h2><ul><li>Culture is context, not a footnote.</li><li>Source text and translation decisions should remain traceable.</li><li>Human review is essential for culturally meaningful claims.</li><li>Results describe a study context; they do not rank AI systems universally.</li></ul></aside>
            <footer class="home-cta"><div><p class="eyebrow">Begin a reading session</p><h2>Explore a translation with its cultural context.</h2></div><a class="primary-action" href="{{ route('workspace.screen', 'workstation') }}">Open workstation <span aria-hidden="true">→</span></a></footer>
        </article>
    </main>
</div>
</body>
</html>
