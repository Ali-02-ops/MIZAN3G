# MIZAN3G — Three-Day Development Plan and Delivery Tracker

## Purpose

This is the execution plan for an auditable, web-first MIZAN3G MVP. It operationalises the full product specification in `MIZAN3G_FULL_SYSTEM_PLAN.md` and is the working tracker for the implementation period.

**Timebox:** 3 working days (14–16 September 2026, Asia/Singapore)  
**Delivery target:** a tested Laravel web/API MVP that supports the complete core audit path using manual/imported generation output. Provider API integration is included only after this reliable path is working.  
**Authoritative product specification:** `MIZAN3G_FULL_SYSTEM_PLAN.md`

## Delivery decision

The full long-term MVP described in the product plan cannot responsibly be completed end-to-end, including Claude/Gemini integrations, report generation, and Flutter, in three days from an empty repository. This plan therefore delivers the smallest scientifically valid vertical slice first:

```text
Organisation → Project → Versioned source document → Cultural terms
→ PA/PB/PC audit freeze → imported/full translations → confirmed term outputs
→ submitted researcher ratings → SKB + IKG → auditable results
```

The implementation must never sacrifice scientific safeguards in order to add surface features.

## Scope boundaries

### Committed in this three-day build

- Laravel application foundation, PostgreSQL/Redis Docker services, testing and code-quality tooling.
- Authentication, organisation membership and server-side role/policy enforcement.
- Projects, immutable source-document versions, categories/subcategories, cultural terms.
- Versioned PA/PB/PC prompt templates and immutable audit snapshots.
- Explicit audit state transitions and a manual/imported-output generation workflow.
- Human-confirmed term outputs, researcher ratings, SKB and IKG score calculations.
- Scientific-integrity, access-control and scoring tests.
- A web-first audit workflow and score/provenance views.

### Conditional only if the committed path is complete ahead of schedule

- One provider integration behind `TranslationProviderInterface` (not both providers).
- Queue-driven extraction suggestions.
- Basic PDF report.

### Deferred after the timebox

- Flutter application.
- Claude and Gemini production integrations as a pair.
- Multi-expert consensus/adjudication, full agreement analytics, sensitivity analysis.
- XLSX/JSON reproducibility exports and advanced report builder.
- Repeated-run research mode and all Version 1.1/2 work.

## Non-negotiable rules

1. Laravel is the only home for scoring and scientific logic.
2. SKB and IKG are always separate metrics; IKG derives from rating equality across PA/PB/PC, never text similarity.
3. Audits snapshot document version, selected terms, prompt versions, model configuration, and scoring mode at freeze time.
4. Submitted records and historical generation attempts are never silently overwritten or deleted.
5. Unconfirmed extraction and draft ratings are excluded from scientific scores.
6. Organisation isolation and policies protect every server-side route; UI filtering is not an access-control mechanism.
7. API secrets stay server-side and are never committed, logged, or returned to clients.

## Database naming convention

Every table created for this application, including Laravel support tables, uses the `mizan3g_` prefix. Database identifiers are kept lowercase for portability across SQLite, PostgreSQL and MySQL while retaining the required MIZAN3G prefix.

Examples:

```text
mizan3g_users
mizan3g_organisations
mizan3g_organisation_user
mizan3g_projects
mizan3g_source_documents
mizan3g_source_document_versions
mizan3g_cultural_categories
mizan3g_cultural_terms
mizan3g_prompt_templates
mizan3g_prompt_versions
mizan3g_audits
mizan3g_audit_terms
mizan3g_audit_prompts
mizan3g_audit_models
mizan3g_generations
mizan3g_term_outputs
mizan3g_ratings
mizan3g_score_snapshots
mizan3g_reports
mizan3g_audit_logs
```

This convention also applies to framework/support tables created for MIZAN3G, for example `mizan3g_password_reset_tokens`, `mizan3g_sessions`, `mizan3g_cache`, `mizan3g_jobs`, `mizan3g_job_batches`, `mizan3g_failed_jobs`, and `mizan3g_personal_access_tokens`.

**Implementation rule:** every Eloquent model declares its `$table` explicitly; every foreign key, pivot table, relation query, migration, and test fixture uses the prefixed name. We will not reuse or alter school-management-system tables.

## Phase tracker

| # | Phase | Target day | Status | Exit criterion |
|---|---|---:|---|---|
| 1 | Foundation and secure application shell | Day 1 AM | Complete | App boots, database/tests/tooling work |
| 2 | Identity, organisations and projects | Day 1 AM–PM | Complete | Users are isolated to authorised organisations |
| 3 | Source corpus and cultural inventory | Day 1 PM | Complete | Versioned document and terms can be created |
| 4 | Prompt versioning and audit freeze | Day 2 AM | Complete | Frozen audit has immutable snapshots |
| 5 | Generation records and imported-output workflow | Day 2 PM | Not started | PA/PB/PC full translations are traceable |
| 6 | Extraction confirmation and researcher review | Day 2 PM | Not started | Confirmed units have submitted ratings |
| 7 | Scoring, provenance and results | Day 3 AM | Not started | Correct SKB/IKG drill down is available |
| 8 | Hardening, acceptance run and handoff | Day 3 PM | Not started | Tests pass and a demo audit completes |

---

## Phase 1 — Foundation and secure application shell

**Goal:** create a repeatable Laravel development environment and baseline application architecture.

**Inputs:** empty repository; the full system plan; local PHP/Composer/Docker availability.  
**Outputs:** Laravel application, Docker Compose services (app, PostgreSQL, Redis, queue), `.env.example`, test database configuration, Pint and PHPUnit/Pest setup, CI-ready scripts, base layout.

**Implementation tasks:**

1. Create the Laravel project and initialise Git-safe ignore rules.
2. Configure PostgreSQL as the primary database and Redis for cache/queue; make local services reproducible through Docker Compose.
3. Install/configure Sanctum, authentication routes, queue connection, factories and test runner.
4. Establish `app/Domain`, `app/Enums`, `app/Policies`, `app/Services`, `app/Http/Controllers/{Web,Api}`, and `app/Http/Requests` boundaries.
5. Add health/readiness checks and a minimal authenticated application shell.
6. Document required environment variables without committing actual values.

**Data / trust boundaries:** credentials and database connections are environment-only; browser session and mobile/API authentication are separated; queue jobs must not expose exceptions or secrets to end users.

**Tests and checks:** application boot; migration against PostgreSQL; authentication smoke test; queue configuration smoke test; Pint; dependency audit where lockfiles exist; confirm `.env` is ignored and untracked.

**Acceptance criteria:** a new developer can configure from `.env.example`, run migrations/tests, sign in, and see no credentials in version control.

**Risks / decisions:** if Docker or local dependencies are unavailable, record the blocker and use the available local execution route; do not substitute SQLite for production schema assumptions without noting it.

---

## Phase 2 — Identity, organisations and projects

**Goal:** establish multi-tenant ownership and the first protected domain aggregate.

**Inputs:** authenticated user; organisation roles (Super Admin, Organisation Admin, Researcher, Expert Reviewer, Observer).  
**Outputs:** organisation/membership/project migrations, models, factories, policies, web/API endpoints, and project screens.

**Implementation tasks:**

1. Implement `organisations`, `organisation_user`, and `projects` with UUIDs or non-guessable public route keys where appropriate.
2. Add role enum/membership checks and policies for organisation and project actions.
3. Allow authorised organisation users to create, list, view and update projects; prevent privilege assignment outside authorised administration paths.
4. Scope every project query through the current user's organisation membership.
5. Add audit-log service primitives for material domain events.

**Permissions:** Super Admin manages global defaults; Organisation Admin manages membership/projects; Researcher creates and operates authorised projects; Expert Reviewer only accesses assignments; Observer is read-only.

**Tests and checks:** unauthenticated redirects/401 responses; cross-organisation access denial; observer write denial; researcher authorised project creation; mass-assignment resistance.

**Acceptance criteria:** two organisations can coexist, and no user can read or mutate another organisation's projects through guessed URLs or API IDs.

---

## Phase 3 — Source corpus and cultural inventory

**Goal:** support the source evidence and selected cultural terms used by an audit.

**Inputs:** project, text pasted by a researcher, default Ghazala framework.  
**Outputs:** versioned documents, categories/subcategories seed data, cultural terms and selection workflow.

**Implementation tasks:**

1. Build `source_documents` and immutable `source_document_versions`; store content hash, metadata and text content.
2. Support pasted text first. Treat file upload (TXT/DOCX/PDF) as conditional scope after the core path is proven, because robust extraction and validation are not safe to rush.
3. Seed all eight Ghazala categories and starter subcategories; preserve framework/configuration fields for future extension.
4. Implement cultural terms with phrase, context, offsets, category/subcategory, significance and selection reason.
5. Provide balanced/flexible selection mode warnings; never imply incomplete balance is a scientific failure unless strict mode is enabled.

**Data integrity:** source content used by an audit cannot be mutated; new content creates a new version. Term offsets are validated against the exact document version.

**Tests and checks:** content-hash generation; version increments; used-version edit creates a new record; organisation scoping; term offset/category validation; category seed verification.

**Acceptance criteria:** a researcher can paste a Malay source text, add classified terms, select terms for audit, and retain the original evidence intact.

---

## Phase 4 — Prompt versioning and audit freeze

**Goal:** create the reproducible experimental configuration before any output is generated.

**Inputs:** document version, selected terms, PA/PB/PC prompt versions, model configuration, scoring mode.  
**Outputs:** audit state machine; prompt version tables/seeds; frozen audit snapshots.

**Implementation tasks:**

1. Implement prompt template/version models and seed PA (SOURCE), PB (TARGET) and PC (TARGET); UI copy must never call PC neutral.
2. Enforce append-only prompt versions after use/lock; edits create a version that supersedes the original.
3. Implement audit, audit terms, audit prompts, and audit models with snapshot fields prescribed in the product plan.
4. Implement allowed audit transitions from DRAFT through READY_TO_GENERATE; reject invalid state changes.
5. On freeze, make an atomic snapshot of document content, term classifications, prompt body/version, model/provider/parameters and scoring mode.
6. Implement cloning as the only path for configuration changes after freeze.

**Permissions:** only authorised project researchers/admins may configure or freeze; all freeze actions are audit logged.

**Tests and checks:** frozen audit cannot mutate underlying scientific configuration; cloned audit remains editable and links new snapshots; all three required prompt codes are present once; cross-project references are rejected.

**Acceptance criteria:** a frozen audit fully explains what will be measured and remains reproducible if project data later changes.

---

## Phase 5 — Generation records and imported-output workflow

**Goal:** preserve complete full-text output provenance without prematurely coupling the core to vendor APIs.

**Inputs:** frozen audit; model configuration with `IMPORTED_OUTPUT` environment; one full translation for each model × PA/PB/PC combination.  
**Outputs:** generation matrix, attempt history, raw/parsed output records, progress state.

**Implementation tasks:**

1. Create generation records for every audit-model/prompt combination using attempt and replicate numbers correctly.
2. Build a secure researcher-only import form for raw response and translated text; bind it to the frozen source/prompt/model snapshot.
3. Store original raw response separately from parsed translation/analysis. Validate size, encoding and ownership.
4. Implement generation statuses and error history; retry creates a new attempt and preserves failures.
5. Display the PA/PB/PC matrix and prevent generation against unfrozen audits.
6. Define `TranslationProviderInterface`, request/result DTOs and factory contracts, but defer a live vendor SDK unless ahead of schedule.

**Data integrity:** one generation represents one full text, never one term. Do not erase failed attempts or replace raw response data.

**Tests and checks:** 2 models × 3 prompts creates six full generation records; imported output is scoped and immutable after review begins; retry increments attempt; one failed generation does not mark all others failed.

**Acceptance criteria:** a user can produce a complete, inspectable generation matrix using imported provider/web output.

---

## Phase 6 — Extraction confirmation and researcher review

**Goal:** make the term-level units human-verified before ratings become scientific evidence.

**Inputs:** completed full-text generations and frozen audit terms.  
**Outputs:** term outputs, confirmation states, submitted researcher ratings and drift annotations.

**Implementation tasks:**

1. Create a term output for each generation × audit term; make target expression/context, transliteration, omission state and notes editable before confirmation.
2. Start with manual term extraction. AI-assisted extraction is conditional; its suggestions are never automatically confirmed.
3. Build the side-by-side review view organised by term and PA/PB/PC output.
4. Implement rating values strictly as 0 Inaccurate, 1 Less Accurate, and 2 Accurate, plus multiple drift types and rationale.
5. Separate draft from submitted ratings; lock submitted researcher ratings except through an explicitly logged correction/version process.
6. Add a minimal expert-assignment data model only if it does not compromise core completion; expert scoring modes remain unavailable until review is implemented correctly.

**Tests and checks:** unconfirmed outputs cannot score; draft ratings cannot score; rating values constrained to enum; drift pivot records validate; researcher cannot review another organisation's audit.

**Acceptance criteria:** a researcher can confirm every term output, submit justified ratings, and compare prompt treatments with full context.

---

## Phase 7 — Scoring, provenance and results

**Goal:** calculate transparent, testable MIZAN3G metrics from submitted ratings.

**Inputs:** confirmed term outputs and submitted rating records; selected scoring mode.  
**Outputs:** `RatingResolver`, `SKBCalculator`, `IKGCalculator`, score metadata/snapshots, web/API results.

**Implementation tasks:**

1. Implement domain services and typed result DTOs in `app/Domain/Scoring`; controllers only authorise, validate, invoke services and return views/resources.
2. Implement researcher-compatible scoring for the initial vertical slice, with explicit metadata. Do not label it `VERIFIED_ONLY` in the absence of submitted expert ratings.
3. Calculate SKB as `sum(ratings) / (2 × evaluated units)` at model, prompt, category and term scopes.
4. Calculate IKG per model/term group only where all PA/PB/PC submitted ratings are present: unstable if any ratings differ; `unstable / eligible`.
5. Persist a score snapshot containing numerator/denominator, evaluated/eligible/unstable/missing/imputed counts and calculation metadata.
6. Build results tables and a simple two-axis (SKB x-axis, IKG y-axis) view with links to contributing units.

**Tests and checks:** documented SKB fixture `[2,2,1,0] = 0.625`; documented IKG fixture `[[2,2,2],[2,1,2],[0,0,0]] = 1/3`; missing PA/PB/PC excludes term from IKG; no ratings returns null; draft/unconfirmed data excluded; score metadata is correct.

**Acceptance criteria:** each displayed scientific number can be traced through rating → term output → generation → frozen audit configuration.

---

## Phase 8 — Hardening, acceptance run and handoff

**Goal:** demonstrate a reliable end-to-end audit and leave an accurate development handoff.

**Inputs:** all completed phases; a non-copyrighted demo Malay text and synthetic/imported translations.  
**Outputs:** passing test suite, formatted code, demo audit, security checks, README/runbook and updated tracker.

**Implementation tasks:**

1. Run a complete demo audit: create organisation/project/document/terms; freeze PA/PB/PC; import six outputs; confirm terms; submit ratings; inspect SKB/IKG.
2. Verify every scientific integrity condition relevant to delivered features.
3. Run migrations fresh, tests, formatting and available dependency audits.
4. Inspect tracked files for `.env`, credentials, logs, databases and uploads; ensure report/export paths are access-controlled if present.
5. Update README with local setup, queue worker instructions, demo workflow, known limitations and next-phase backlog.
6. Mark the tracker status and record any deferred requirements with reasons.

**Tests and checks:** clean-room migration/test run; policy test suite; scoring tests; manual acceptance checklist; `composer audit --locked` and `npm audit --omit=dev` when applicable.

**Acceptance criteria:** another developer can run the app, repeat the demo, verify the score arithmetic, and understand exactly what is and is not included.

---

## Day-by-day execution schedule

| Day | Primary outcome | Checkpoint |
|---|---|---|
| Day 1 | Phases 1–3 | Secure multi-tenant project can hold a versioned source document and selected classified terms. |
| Day 2 | Phases 4–6 | Frozen audit can retain imported PA/PB/PC full translations and submitted researcher ratings. |
| Day 3 | Phases 7–8 | Correct SKB/IKG, provenance, automated tests, demo audit and handoff documentation. |

## Daily control loop

At the end of each day:

1. Update this tracker’s status and note the completed commit/PR reference if applicable.
2. Run the relevant tests and formatters.
3. Compare delivered work to the phase exit criterion, not just a task checklist.
4. Move incomplete optional work out of the critical path before starting new features.
5. Record a concise blocker/decision under the relevant phase.

## Phase completion gate

Every phase must complete the following sequence before work begins on the next phase:

1. Meet the phase exit criterion and run the relevant tests, formatter, and security checks.
2. Update this tracker with the completed status and any material implementation decision.
3. Create a focused Git commit for the completed phase.
4. Push that commit to `origin/main` and verify the working tree is clean.
5. Start the following phase.

## Immediate next action

Complete Phase 2: add Sanctum/API authentication and protected organisation/project endpoints to the tested multi-tenant schema.

## Implementation log

### 14 September 2026 — Phase 1 complete / Phase 2 started

- Laravel 13.31.0 is bootstrapped with PHP 8.5.9.
- Local development uses the separate `database/database.sqlite` file; the school-management-system database is not used or modified.
- The migration ledger and all current Laravel support/domain tables use the `mizan3g_` prefix.
- Applied and tested: users, organisations, membership roles, projects, audit-log base table, role-aware organisation/project policies, and cross-organisation isolation tests.
- Environment note: Docker Desktop was not running, so PostgreSQL integration is deferred; the schema is written using portable Laravel migration primitives.

### 14 September 2026 — Phase 3 complete

- Added prefixed source-document, source-document-version, cultural-category, cultural-subcategory, and cultural-term tables and explicit model mappings.
- Added `DocumentVersionService`, which creates append-only, SHA-256-hashed source versions and advances the document's current-version pointer without altering historical text.
- Seeded all eight Ghazala categories and starter subcategories idempotently.
- Added protected API routes for creating and viewing documents/versions and listing, creating, and updating cultural terms.
- Verified the Phase 3 integrity tests, full test suite, formatter, and dependency audit before the phase commit.

### 14 September 2026 — Phase 4 complete

- Added versioned PA/PB/PC prompt templates, model configurations, and immutable audit snapshot records.
- Implemented atomic audit freeze validation and snapshotting, including required PA/PB/PC prompts, selected terms, project models, prompt locking, and state transition to `READY_TO_GENERATE`.
