<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MIZAN3G | Cultural Translation Audit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="mizan-shell">
<a class="skip-link" href="#main-content">Skip to main content</a>
<div class="mizan-layout" data-mizan-workspace>
    <aside class="mizan-sidebar" aria-label="Primary navigation">
        <div class="brand"><div class="brand-mark" aria-hidden="true">M</div><div><strong>MIZAN3G</strong><span>Cultural Translation Audit</span></div></div>
        <nav class="side-nav"><a class="nav-item active" href="#projects">Projects</a><a class="nav-item" href="#documents">Documents</a><a class="nav-item" href="#inventory">Cultural inventory</a><a class="nav-item" href="{{ route('workspace.screen', 'workstation') }}">Workstation</a><a class="nav-item" href="{{ route('workspace.screen', 'audits') }}">Audit runs</a><a class="nav-item" href="{{ route('workspace.screen', 'expert-reviews') }}">Expert reviews</a><a class="nav-item" href="{{ route('workspace.screen', 'results') }}">Results</a><a class="nav-item" href="{{ route('workspace.screen', 'reports') }}">Reports</a><a class="nav-item" href="{{ route('workspace.screen', 'settings') }}">Settings</a></nav>
        <div class="sidebar-note"><span class="note-dot"></span><div><strong>Research mode</strong><small>Provenance required</small></div></div>
        <div class="sidebar-user"><div class="avatar" data-user-initial>?</div><div><strong data-user-name>Loading…</strong><span>Research workspace</span></div><form method="POST" action="{{ route('logout') }}">@csrf <button type="submit" aria-label="Sign out">Sign out</button></form></div>
    </aside>
    <main id="main-content" class="mizan-main">
        <header class="topbar"><button class="mobile-menu" type="button" aria-label="Open navigation">Menu</button><div class="breadcrumb"><span>Research workspace</span><b>/</b><strong data-breadcrumb>Projects</strong></div></header>
        <section id="projects" class="dashboard-intro"><div><p class="eyebrow">Research workspace</p><h1>Build a traceable <em>audit.</em></h1><p class="intro-copy">Create a project, preserve source text as a version, and review cultural terms with their context.</p></div></section>
        <p class="workspace-message" data-workspace-message role="status" hidden></p>
        <section class="dashboard-grid">
            <section class="panel" aria-labelledby="projects-heading"><div class="panel-heading"><h2 id="projects-heading">Your projects</h2><button class="secondary-action" type="button" data-toggle="project-form">New project</button></div>
                <form class="workspace-form" data-project-form hidden><label>Organisation <select name="organisation_id" required data-organisations></select></label><label>Project name <input name="name" required maxlength="255"></label><label>Source language <input name="source_language" value="ms" required maxlength="16"></label><label>Target language <input name="target_language" value="ar" required maxlength="16"></label><label>Objective <textarea name="objective" maxlength="10000"></textarea></label><button class="primary-action" type="submit">Create project</button></form>
                <div class="empty-state" data-projects><p>Loading projects…</p></div>
            </section>
            <aside class="panel integrity-panel"><p class="eyebrow">Research integrity</p><h2>Data remains attributable.</h2><p>Every document revision receives its own immutable version and cultural terms remain attached to that version.</p><ul class="integrity-list"><li><span>✓</span>Organisation-scoped access</li><li><span>✓</span>Versioned source text</li><li><span>✓</span>Human-confirmed terms</li></ul></aside>
        </section>
        <section id="documents" class="panel workspace-section" hidden data-documents-section><div class="panel-heading"><h2>Source documents</h2><button class="secondary-action" type="button" data-toggle="document-form">Add document</button></div><form class="workspace-form" data-document-form hidden><label>Title <input name="title" required maxlength="255"></label><label>Source language <input name="source_language" value="ms" required maxlength="16"></label><label>Source text <textarea name="text_content" required minlength="1"></textarea></label><label>Author <input name="author" maxlength="255"></label><button class="primary-action" type="submit">Save document</button></form><div data-documents-list></div></section>
        <section id="inventory" class="panel workspace-section" hidden data-inventory-section><div class="panel-heading"><h2>Cultural inventory</h2><button class="secondary-action" type="button" data-toggle="term-form">Add cultural term</button></div><form class="workspace-form" data-term-form hidden><label>Source phrase <input name="source_phrase" required maxlength="1000"></label><label>Source sentence <textarea name="source_sentence" maxlength="10000"></textarea></label><label>Source context <textarea name="source_context" maxlength="50000"></textarea></label><label>Category <select name="category_id" required data-categories></select></label><label>Subcategory <select name="subcategory_id" data-subcategories><option value="">No subcategory</option></select></label><label>Cultural significance <textarea name="cultural_significance" maxlength="10000"></textarea></label><label class="check-label"><input name="selected_for_audit" type="checkbox" value="1"> Select for audit</label><button class="primary-action" type="submit">Save term</button></form><div data-terms-list></div></section>
    </main>
</div>
</body>
</html>
