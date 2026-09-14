# MIZAN3G Web + Mobile Application
## Full Product Goal, Domain Logic, Architecture, Data Model, API, Workflow, and Implementation Plan for Codex

> **Primary purpose of this document:**  
> Give Codex enough product, research, domain, architecture, database, workflow, scoring, and implementation context to build MIZAN3G correctly without having to infer the system's purpose or invent hidden logic.

---

# 1. Project Identity

## 1.1 Product Name

**MIZAN3G**

MIZAN3G is a research and professional audit platform for detecting **covert cultural drift** in AI-generated translation.

The initial research use case is:

**Malay → Arabic translation**

The application is based on the MIZAN3G research instrument, which evaluates whether a generative AI model:

1. preserves the cultural meaning of a source text, and
2. remains stable when the translation prompt changes direction.

---

# 2. Core Problem

Modern generative AI can produce translations that are:

- fluent,
- grammatically correct,
- convincing,
- natural to the target reader,

while still being culturally inaccurate.

The dangerous case is not a translation that looks obviously wrong.

The dangerous case is:

> A translation that looks linguistically correct, but quietly changes the cultural, historical, ideological, religious, social, or contextual meaning of the source text.

Examples of possible cultural drift:

- replacing a Malay cultural concept with an Arab cultural equivalent that is not historically equivalent,
- weakening ideological meaning,
- adding a historical claim that does not exist in the source,
- generalising a culturally specific institution,
- translating a culturally loaded term into a neutral term,
- over-domesticating source culture for the target audience,
- preserving surface meaning while losing cultural function.

MIZAN3G exists to detect these hidden problems.

---

# 3. Main Objective

Build a reproducible digital audit platform that allows researchers, translators, students, reviewers, and expert evaluators to:

1. create a research project,
2. upload or paste a source text,
3. identify cultural elements,
4. classify those elements,
5. select terms for audit,
6. configure one or more AI models,
7. run three different prompt orientations,
8. preserve every full AI output,
9. extract term-level translations,
10. evaluate cultural fidelity,
11. compare prompt stability,
12. request expert validation,
13. calculate MIZAN3G scores,
14. compare models and cultural categories,
15. generate transparent reports,
16. preserve an auditable research trail.

---

# 4. Main Product Goal

The system must answer:

> **Does an AI model preserve the cultural meaning of a source text, and does that preservation remain stable when the prompt orientation changes?**

The application must distinguish between four possible conditions:

| Fidelity | Stability | Interpretation |
|---|---|---|
| High | High | Strong translation performance |
| High | Low | Good average output but sensitive to prompting |
| Low | High | Consistently weak cultural fidelity |
| Low | Low | Weak and unstable output |

This distinction is central to the project.

The system must **never reduce MIZAN3G into only one score**.

---

# 5. Technology Stack

## Web

- Laravel
- Blade for initial web UI
- Alpine.js for lightweight interactivity
- Optional later migration to Vue or React where necessary

## Backend/API

- Laravel REST API
- Laravel Sanctum
- Laravel Queue
- Laravel Events/Notifications
- Laravel Policies

## Database

Preferred:

- PostgreSQL

Acceptable:

- MySQL

## Cache / Queues

Recommended:

- Redis

## Mobile

Use:

- **Flutter**

Reason:

Flutter allows one codebase for:

- Android
- iOS

The Flutter app must use the Laravel REST API.

Do **not** duplicate business/scoring logic inside Flutter.

---

# 6. High-Level Architecture

```text
                    ┌───────────────────────────────┐
                    │           Laravel             │
                    │                               │
                    │  Authentication               │
                    │  Project Management           │
                    │  Document Management          │
                    │  Cultural Term Management     │
                    │  Prompt Management            │
                    │  AI Provider Integration      │
                    │  MIZAN3G Scoring Engine       │
                    │  Expert Validation            │
                    │  Reporting                    │
                    │  Audit Logging                │
                    └──────────────┬────────────────┘
                                   │
                           REST API / JSON
                     ┌─────────────┴─────────────┐
                     │                           │
             ┌───────▼────────┐          ┌──────▼────────┐
             │ Laravel Web UI │          │ Flutter App   │
             │ Desktop/Web    │          │ Android + iOS │
             └────────────────┘          └───────────────┘
```

Laravel is the **single source of truth**.

All important logic lives in Laravel.

---

# 7. Product Principles

Codex must follow these principles.

## 7.1 Audit-First

This system is not primarily a normal translation application.

It is an **audit system**.

Every output must remain traceable.

---

## 7.2 Reproducibility

For every generated result store:

- project,
- source document,
- source document version,
- selected cultural terms,
- prompt,
- prompt version,
- AI provider,
- AI model,
- generation settings,
- raw request,
- raw response,
- generated translation,
- term extraction,
- reviewer rating,
- expert rating,
- scoring mode,
- generated scores,
- timestamps.

---

## 7.3 Immutability After Audit Freeze

Once an audit begins, freeze:

- source document version,
- selected terms,
- prompt versions,
- model configuration,
- scoring configuration.

Do not silently update historical audit data.

If a researcher wants changes:

- clone the audit,
- create a new version.

---

## 7.4 No Hidden Logic

Do not put scientific logic inside:

- Blade files,
- controllers,
- Flutter widgets,
- database views,
- JavaScript calculations.

All scientific logic must be explicit in Laravel domain/services.

---

## 7.5 Model Neutrality

Do not build the application around only Claude or Gemini.

Use a provider abstraction.

Potential providers:

- Anthropic
- Google Gemini
- OpenAI
- manual imported output
- future providers

---

## 7.6 Human Review Remains Visible

The system must distinguish:

- researcher rating,
- expert rating,
- consensus rating,
- adjudicated rating,
- imputed rating,
- missing rating.

Never silently merge them.

---

# 8. Research Foundation

MIZAN3G evaluates cultural translation using three differently oriented prompts.

The logic is:

```text
Source Text
    ↓
Cultural Terms
    ↓
Same AI Model
    ↓
PA
PB
PC
    ↓
Compare outputs
    ↓
Evaluate fidelity
    ↓
Evaluate stability
```

The important research assumption is:

> If a model genuinely handles a cultural reference robustly, its treatment should remain reasonably stable when prompt orientation changes.

If output changes significantly across prompts, that instability becomes a diagnostic signal.

---

# 9. Cultural Framework

Initial framework:

**Ghazala cultural categories**

Seed the following eight categories:

1. Religion
2. Social
3. Material
4. Ecology
5. Literary
6. Linguistic
7. Mental and Emotional
8. Political

Each category may contain subcategories.

Examples:

```text
Religion
- worship
- belief
- religious principle

Political
- organisation
- political concept
- ideology
- historical institution

Literary
- metaphor
- simile
- personification

Linguistic
- proverb
- dialect
- idiom
```

Make categories configurable for future research, but preserve system defaults.

---

# 10. Core Prompt Design

The MIZAN3G instrument uses three prompt conditions.

Prompts must be stored in the database and versioned.

Do not hard-code full prompts in service classes.

---

# 11. PA — Source Culture Preservation

Code:

```text
PA
```

Purpose:

- preserve Malay cultural identity,
- avoid excessive adaptation to Arab culture,
- prioritise source-culture meaning.

Orientation:

```text
SOURCE
```

Conceptually:

```text
Source culture preservation / foreignisation
```

---

# 12. PB — Domestication

Code:

```text
PB
```

Purpose:

- adapt translation to Arab reader expectations,
- prioritise natural target-culture readability.

Orientation:

```text
TARGET
```

This prompt intentionally creates cultural adaptation pressure.

That pressure is useful because MIZAN3G observes whether cultural meaning shifts.

---

# 13. PC — Macro Translation

Code:

```text
PC
```

Purpose:

- translate at whole-text level,
- prioritise strategic coherence,
- produce natural Arabic.

Orientation:

```text
TARGET
```

Important:

PC is **not a neutral control**.

The application documentation and UI must not incorrectly label it neutral.

---

# 14. Prompt Versioning

Prompt table must support versions.

Example:

```text
PA v1
PA v2
PB v1
PC v1
```

If a prompt is edited after it has been used:

create a new version.

Never update an already-used prompt body.

Suggested fields:

```text
id
prompt_template_id
version_number
prompt_body
content_hash
created_by
created_at
locked_at
supersedes_id
```

---

# 15. Unit of Analysis

This distinction is extremely important.

The AI receives the **whole source text**.

Then the system evaluates selected terms inside the full translation.

Example:

```text
One source story
24 selected terms

Claude:
PA = 1 full generation
PB = 1 full generation
PC = 1 full generation

Gemini:
PA = 1 full generation
PB = 1 full generation
PC = 1 full generation
```

That means:

```text
2 models × 3 prompts = 6 full text generations
```

Then:

```text
24 terms × 2 models × 3 prompts = 144 term-output evaluation units
```

Do not call the 144 units "144 generations".

They are term-level evaluation units extracted from six full generations.

---

# 16. Three-Level Rating Rubric

Each term output receives one cultural fidelity rating.

---

## 16.1 Accurate

Value:

```text
2
```

Enum:

```text
ACCURATE
```

Meaning:

- source reference preserved,
- cultural function preserved,
- target reader receives meaning equivalent to source reader.

---

## 16.2 Less Accurate

Value:

```text
1
```

Enum:

```text
LESS_ACCURATE
```

Meaning:

- general meaning remains understandable,
- some cultural / historical / emotional / ideological content is reduced,
- source reference not completely replaced.

---

## 16.3 Inaccurate

Value:

```text
0
```

Enum:

```text
INACCURATE
```

Meaning:

- source reference replaced,
- omitted,
- distorted,
- or unsupported information added.

---

# 17. Drift Annotation

Ratings and drift labels are related but not identical.

Recommended drift types:

```text
NONE
REDUCTION
LOSS
REPLACEMENT
GENERALISATION
ADDITION
IDEOLOGICAL_SHIFT
HISTORICAL_DISTORTION
REFERENTIAL_ERROR
OVER_DOMESTICATION
OVER_FOREIGNISATION
CULTURAL_DISTINCTION_LOSS
OMISSION
OTHER
```

Allow multiple drift labels per rating.

---

# 18. Cultural Fidelity Score — SKB

## Formula

```text
SKB =
sum of rating scores
-------------------------------
2 × number of evaluated units
```

Range:

```text
0.00 - 1.00
```

Example:

```text
ratings:
2, 2, 1, 0, 2, 1

sum = 8
units = 6
maximum = 12

SKB = 8 / 12 = 0.6667
```

---

# 19. SKB Scopes

Calculate SKB at multiple levels.

## Overall Model

Example:

```text
Gemini overall SKB
```

## Prompt

Example:

```text
Gemini + PA
```

## Category

Example:

```text
Gemini + Political
```

## Category + Prompt

Example:

```text
Claude + Political + PB
```

## Term

Average fidelity across three prompts.

Do not automatically combine different models into one scientific score unless explicitly requested.

---

# 20. Prompt Instability Index — IKG

IKG measures stability.

A term is stable only if its rating is identical across PA, PB, and PC.

Example:

```text
PA = 2
PB = 2
PC = 2

Stable
```

Example:

```text
PA = 2
PB = 1
PC = 2

Unstable
```

Example:

```text
PA = 0
PB = 0
PC = 0

Stable
```

---

# 21. IKG Formula

```text
IKG =
number of unstable terms
-------------------------
number of eligible terms
```

Example:

```text
24 terms
13 unstable

IKG = 13 / 24 = 0.5417
```

Lower IKG is better because IKG measures instability.

---

# 22. SKB and IKG Must Remain Separate

SKB answers:

> How faithful is the translation?

IKG answers:

> How sensitive is that fidelity to prompt direction?

Example:

```text
Term A:
PA = 1
PB = 1
PC = 1
```

This term is:

- not highly faithful,
- but stable.

Example:

```text
Term B:
PA = 2
PB = 0
PC = 2
```

This term may have a moderate average fidelity,
but is highly unstable.

This is why one score is insufficient.

---

# 23. Two-Axis Interpretation

Core dashboard visualization:

```text
X-axis = SKB
Y-axis = IKG
```

Interpretation:

```text
High SKB + Low IKG
= high fidelity and high stability

High SKB + High IKG
= high average fidelity but unstable

Low SKB + Low IKG
= weak but consistent

Low SKB + High IKG
= weak and unstable
```

Do not hard-code scientific claims beyond the raw metrics.

---

# 24. User Roles

Recommended roles:

---

## 24.1 Super Admin

Can:

- manage system-wide settings,
- manage organisations,
- manage AI providers,
- manage default cultural frameworks,
- manage prompt templates,
- view logs.

---

## 24.2 Organisation Admin

Can:

- manage organisation users,
- create projects,
- assign roles,
- invite experts,
- configure project settings.

---

## 24.3 Researcher

Can:

- create projects,
- upload documents,
- classify cultural terms,
- configure audits,
- run model generation,
- review outputs,
- rate translations,
- generate reports.

---

## 24.4 Expert Reviewer

Can:

- access assigned audits,
- review assigned output units,
- submit independent ratings,
- propose category corrections,
- leave expert comments.

Recommended:

Hide researcher ratings from experts until expert submission.

---

## 24.5 Observer

Read-only role.

Possible users:

- supervisor,
- editor,
- collaborator,
- client.

---

# 25. Organisation Structure

```text
Organisation
    ↓
Project
    ↓
Source Documents
    ↓
Audit Runs
    ↓
Generations
    ↓
Term Outputs
    ↓
Ratings
    ↓
Scores
    ↓
Reports
```

---

# 26. Project Example

```text
Organisation:
UniSZA Research Team

Project:
Malay-Arabic Cultural Drift Study

Document:
Cinta Ahmad Mutawakkil

Audit:
Pilot Replication 2027
```

---

# 27. Audit Status State Machine

Use explicit status.

```text
DRAFT
DOCUMENT_READY
TERMS_READY
MODELS_CONFIGURED
READY_TO_GENERATE
GENERATING
GENERATED
RESEARCHER_REVIEW
AWAITING_EXPERT
EXPERT_REVIEW
SCORING_READY
COMPLETED
ARCHIVED
```

Failure state:

```text
GENERATION_FAILED
```

Retry must create a new attempt, not erase failure history.

---

# 28. Main User Workflow

```text
1. Create Organisation
2. Create Project
3. Add Source Document
4. Create Document Version
5. Identify Cultural Terms
6. Classify Terms
7. Select Terms for Audit
8. Select Prompt Versions
9. Configure AI Models
10. Create Audit
11. Freeze Audit
12. Run Full Translations
13. Extract Term Outputs
14. Confirm Extraction
15. Researcher Rating
16. Expert Validation
17. Calculate Scores
18. Review Dashboard
19. Export Report
```

---

# 29. Source Document Management

Supported inputs:

- paste text,
- TXT,
- DOCX,
- PDF.

Store:

```text
title
author
publication
publication_year
source_language
original_file_path
text_content
version_number
content_hash
notes
```

If source text changes:

create a new version.

Never mutate a source version already used in a completed audit.

---

# 30. Cultural Term Inventory

Each cultural element should store:

```text
source_phrase
source_sentence
source_context
start_offset
end_offset
category
subcategory
cultural_significance
selected_for_audit
selection_reason
```

Example:

```text
Source phrase:
Malayan Union

Category:
Political

Subcategory:
Political concept/system

Significance:
Historical institution specific to colonial Malaya.
```

---

# 31. Term Selection Modes

## Balanced Research Mode

Useful for controlled studies.

Example:

```text
3 terms per category
8 categories
24 total terms
```

The app should warn if balance is incomplete.

Do not block unless project configuration requires strict balance.

---

## Flexible Audit Mode

Professional users may select any number of terms.

Example:

```text
14 political terms
4 religion terms
7 linguistic terms
```

The report must clearly identify this mode.

---

# 32. AI Model Configuration

Store:

```text
provider
model_name
provider_model_id
execution_environment
temperature
top_p
max_tokens
seed
parameters_json
configuration_date
notes
```

Supported execution environments:

```text
API
MANUAL_WEB
IMPORTED_OUTPUT
```

---

# 33. Why Execution Environment Matters

The original study used consumer web interfaces.

The app will normally use APIs.

These environments may not produce identical results.

Therefore every generation must disclose its execution environment.

Never claim an API run is an exact replication of a web-interface run.

---

# 34. Audit Freeze

When the researcher clicks:

```text
Freeze Audit
```

create immutable snapshots of:

- source text,
- cultural terms,
- category assignments,
- prompts,
- model configuration,
- scoring mode.

If the user wants to modify these later:

```text
Clone Audit
```

---

# 35. Generation Logic

For every model:

run:

```text
PA
PB
PC
```

Example:

```text
Claude PA
Claude PB
Claude PC
Gemini PA
Gemini PB
Gemini PC
```

Each is one full-text generation.

---

# 36. Generation Record

Store:

```text
audit_id
audit_model_id
audit_prompt_id
attempt_number
replicate_number
submitted_prompt
source_text_snapshot
raw_request_json
raw_response_text
translated_text
analysis_text
provider_request_id
input_tokens
output_tokens
cost
latency_ms
status
error_message
started_at
completed_at
```

Important distinction:

```text
attempt_number
```

means retry after technical failure.

```text
replicate_number
```

means intentional experimental repetition.

Do not confuse them.

---

# 37. AI Provider Abstraction

Create:

```php
interface TranslationProviderInterface
{
    public function generate(
        GenerationRequest $request
    ): GenerationResult;
}
```

Implement:

```text
AnthropicProvider
GeminiProvider
OpenAIProvider
ManualProvider
```

Use:

```text
AIProviderFactory
```

Never call vendor SDK directly inside controllers.

---

# 38. AI Output Contract

When provider supports structured output, request:

```json
{
  "translation": "...",
  "analysis": "...",
  "cultural_notes": []
}
```

Always store:

- parsed output,
- original raw response.

Never discard raw provider response.

---

# 39. Term Extraction

The difficult part is identifying the target-language equivalent of each source cultural term.

Use a two-stage process.

---

## Stage 1 — Machine-Assisted Extraction

Input:

```text
Source term
Source sentence
Full generated translation
```

Ask system to identify:

```text
target expression
target sentence/context
confidence
possible omission
```

---

## Stage 2 — Human Confirmation

Researcher must:

```text
Accept
Edit
Mark Omitted
Mark Unable To Identify
```

Do not treat machine extraction as final research evidence.

---

# 40. Term Output Structure

Store:

```text
generation_id
audit_term_id
target_expression
target_context
transliteration
extraction_method
extraction_confidence
researcher_confirmed
omitted
notes
```

---

# 41. Transliteration

The original study uses ALA-LC.

Support:

- manual entry,
- AI-assisted suggestion,
- future automatic transliteration.

Store original Arabic and transliteration separately.

Never overwrite researcher-corrected transliteration.

---

# 42. Researcher Review UI

Recommended side-by-side view:

```text
SOURCE TERM
SOURCE CONTEXT
CATEGORY
SUBCATEGORY

PA OUTPUT     PB OUTPUT     PC OUTPUT

Arabic        Arabic        Arabic
Translit.     Translit.     Translit.
Context       Context       Context

Rating        Rating        Rating
Drift         Drift         Drift
Rationale     Rationale     Rationale
```

This view is highly important because MIZAN3G depends on comparing prompt variation.

---

# 43. Expert Validation

Expert assignment can cover:

- full audit,
- selected category,
- selected model,
- selected terms.

Expert review fields:

```text
rating
rationale
confidence
drift_types
proposed_category
proposed_subcategory
corrected_translation
notes
```

---

# 44. Blind Expert Review

Project setting:

```text
blind_expert_review = true
```

When enabled, expert must not see:

- researcher score,
- researcher rationale,
- other expert scores,
- final SKB,
- final IKG.

After submission:

allow comparison.

Recommended default:

```text
true
```

---

# 45. Rating Sources

Support:

```text
RESEARCHER
EXPERT
CONSENSUS
ADJUDICATED
IMPUTED
```

---

# 46. Scoring Modes

---

## 46.1 Expert Only

Use expert rating only.

If missing:

exclude.

---

## 46.2 Verified Only

Same practical score source as expert-only but explicitly intended for validated units.

Report missing units.

---

## 46.3 Manuscript Compatible

If expert rating exists:

use expert.

If expert rating missing:

use researcher rating.

Mark that unit:

```text
imputed = true
```

This reproduces the pilot's imputation logic.

---

## 46.4 Consensus

For multiple experts.

Possible strategy:

- majority,
- median ordinal score,
- adjudicated final score.

Do not assume one automatically.

Project owner must choose.

---

# 47. Missing Rating Transparency

Never silently impute.

Example result:

```text
SKB: 0.688
Mode: Manuscript-Compatible
Validated Units: 68
Imputed Units: 4
Missing Units: 0
```

Example:

```text
SKB: 0.691
Mode: Verified Only
Validated Units: 68
Excluded Missing Units: 4
```

---

# 48. Rating Resolution Pseudocode

```text
resolveRating(termOutput, scoringMode):

    if scoringMode == VERIFIED_ONLY:
        return expertRating if submitted
        return null

    if scoringMode == EXPERT_ONLY:
        return expertRating if submitted
        return null

    if scoringMode == MANUSCRIPT_COMPATIBLE:
        if expertRating exists:
            return expertRating
        if researcherRating exists:
            return researcherRating marked as imputed
        return null

    if scoringMode == CONSENSUS:
        return resolveConsensus(termOutput)

    if scoringMode == ADJUDICATED:
        return adjudicatedRating
```

---

# 49. SKB Service Pseudocode

```text
calculateSKB(units, scoringMode):

    valid = []

    for unit in units:
        resolved = resolveRating(unit, scoringMode)

        if resolved is not null:
            valid.append(resolved)

    if valid is empty:
        return null

    numerator = sum(valid.rating_value)
    denominator = 2 * count(valid)

    score = numerator / denominator

    return:
        score
        numerator
        denominator
        evaluated_units
        missing_units
        imputed_units
```

---

# 50. IKG Service Pseudocode

```text
calculateIKG(model, terms, scoringMode):

    eligible_terms = 0
    unstable_terms = 0
    excluded_terms = 0

    for term in terms:

        PA = resolve rating for PA
        PB = resolve rating for PB
        PC = resolve rating for PC

        if any required rating missing:
            excluded_terms += 1
            continue

        eligible_terms += 1

        if PA != PB or PB != PC:
            unstable_terms += 1

    if eligible_terms == 0:
        return null

    IKG = unstable_terms / eligible_terms

    return:
        IKG
        unstable_terms
        eligible_terms
        excluded_terms
```

---

# 51. Weighted Instability

Future feature only.

Original IKG treats:

```text
2 → 1
```

and:

```text
2 → 0
```

both as unstable.

Future version may calculate instability severity.

Do not replace original IKG.

Create optional:

```text
Weighted IKG
```

---

# 52. Inter-Rater Reliability

Initial implementation:

- observed agreement
- Cohen's Kappa
- disagreement count
- expert stricter count
- researcher stricter count

Future:

- Fleiss Kappa
- Krippendorff Alpha

Do not require future metrics for MVP.

---

# 53. Category Reclassification

An expert may disagree with category assignment.

Never overwrite original classification.

Store:

```text
original category
expert proposed category
accepted analysis category
```

Allow:

```text
Primary Analysis
Sensitivity Analysis
```

This preserves the original research design while allowing alternative classification analysis.

---

# 54. Translation Procedure Coding

Optional but supported.

Seed:

```text
LITERAL_TRANSLATION
NEUTRALISATION
PARAPHRASE
GENERAL_SENSE
CULTURAL_EQUIVALENT
ACCEPTED_STANDARD_TRANSLATION
CLASSIFIER
TRANSLATION_COUPLET
TRANSCRIPTION
DELETION
GLOSSARY
TRANSLATION_LABEL
```

Store coder identity.

Procedure coding should support second-coder validation later.

---

# 55. Dashboard

Project dashboard cards:

```text
Documents
Cultural Elements
Selected Terms
AI Models
Completed Generations
Pending Reviews
Completed Reviews
Reports
```

Scientific cards per model:

```text
SKB
IKG
Validated Units
Missing Units
Imputed Units
```

---

# 56. Charts

Recommended:

1. Overall SKB by model
2. Overall IKG by model
3. SKB by cultural category
4. IKG by category
5. SKB by prompt
6. Two-axis SKB vs IKG scatter
7. Rating distribution
8. Drift distribution
9. Researcher vs expert agreement
10. Translation procedure distribution

---

# 57. Two-Axis Scatter

X:

```text
SKB
```

Y:

```text
IKG
```

Markers:

- models,
- categories.

Clicking a point should open underlying terms.

Threshold/reference line options:

```text
None
Mean
Median
Custom threshold
```

Do not force arbitrary scientific cutoffs.

---

# 58. Report Generator

Supported:

- PDF
- CSV
- XLSX
- JSON research export

Suggested report sections:

```text
1. Project Information
2. Research Objective
3. Source Document
4. Cultural Framework
5. Selected Terms
6. Prompt Versions
7. Model Configuration
8. Generation Environment
9. SKB Results
10. IKG Results
11. Category Results
12. Prompt Results
13. Two-Axis Analysis
14. Researcher vs Expert Agreement
15. Drift Case Studies
16. Translation Procedures
17. Missing / Imputed Data
18. Sensitivity Analysis
19. Methodological Limitations
20. Audit Provenance
```

---

# 59. Reproducibility Package

Generate:

```text
mizan3g-audit-{audit_id}.zip
```

Example structure:

```text
manifest.json
source/
    source.txt
terms/
    terms.csv
prompts/
    PA.txt
    PB.txt
    PC.txt
models/
    models.json
generations/
    claude_PA.txt
    claude_PB.txt
    claude_PC.txt
ratings/
    researcher.csv
    expert.csv
scores/
    scores.json
reports/
    report.pdf
```

---

# 60. Database Schema

---

## users

```text
id
name
email
password
status
last_login_at
created_at
updated_at
```

---

## organisations

```text
id
name
slug
owner_user_id
settings_json
created_at
updated_at
```

---

## organisation_user

```text
organisation_id
user_id
role
joined_at
```

---

## projects

```text
id
organisation_id
name
slug
description
objective
source_language
target_language
framework
status
created_by
created_at
updated_at
```

---

## source_documents

```text
id
project_id
title
author
publication
publication_year
source_language
current_version_id
created_by
created_at
updated_at
```

---

## source_document_versions

```text
id
source_document_id
version_number
text_content
file_path
content_hash
notes
created_by
created_at
```

---

## cultural_categories

```text
id
code
name
description
framework
sort_order
active
created_at
updated_at
```

---

## cultural_subcategories

```text
id
category_id
code
name
description
active
created_at
updated_at
```

---

## cultural_terms

```text
id
document_version_id
source_phrase
source_sentence
source_context
start_offset
end_offset
category_id
subcategory_id
cultural_significance
selected_for_audit
selection_reason
created_by
created_at
updated_at
```

---

## term_classification_proposals

```text
id
cultural_term_id
proposed_category_id
proposed_subcategory_id
proposed_by
proposal_type
reason
status
reviewed_by
reviewed_at
created_at
```

---

## prompt_templates

```text
id
code
name
theoretical_basis
orientation
is_system_default
active
created_at
updated_at
```

---

## prompt_versions

```text
id
prompt_template_id
version_number
prompt_body
content_hash
created_by
created_at
locked_at
supersedes_id
```

---

## ai_providers

```text
id
code
name
active
configuration_schema_json
created_at
updated_at
```

---

## ai_model_configurations

```text
id
project_id
provider_id
display_name
provider_model_id
execution_environment
temperature
top_p
max_tokens
seed
parameters_json
notes
created_by
created_at
updated_at
```

---

## audits

```text
id
project_id
document_version_id
name
description
audit_mode
scoring_mode
blind_expert_review
status
started_at
completed_at
created_by
created_at
updated_at
```

---

## audit_terms

```text
id
audit_id
cultural_term_id
source_phrase_snapshot
source_context_snapshot
category_id_snapshot
subcategory_id_snapshot
sort_order
created_at
```

---

## audit_prompts

```text
id
audit_id
prompt_version_id
code
prompt_body_snapshot
sort_order
created_at
```

---

## audit_models

```text
id
audit_id
ai_model_configuration_id
model_name_snapshot
provider_snapshot
parameters_snapshot_json
created_at
```

---

## generations

```text
id
audit_id
audit_model_id
audit_prompt_id
attempt_number
replicate_number
submitted_prompt
source_text_snapshot
raw_request_json
raw_response_text
translated_text
analysis_text
provider_request_id
input_tokens
output_tokens
cost
latency_ms
status
error_message
started_at
completed_at
created_at
updated_at
```

---

## term_outputs

```text
id
generation_id
audit_term_id
target_expression
target_context
transliteration
extraction_method
extraction_confidence
researcher_confirmed
omitted
notes
created_at
updated_at
```

---

## ratings

```text
id
term_output_id
reviewer_user_id
reviewer_role
rating_value
rationale
confidence
submitted_at
status
created_at
updated_at
```

---

## drift_types

```text
id
code
name
description
active
created_at
updated_at
```

---

## rating_drift_types

```text
rating_id
drift_type_id
```

---

## translation_procedure_codes

```text
id
code
name
description
active
created_at
updated_at
```

---

## term_output_procedures

```text
id
term_output_id
procedure_code_id
coded_by
confidence
notes
created_at
updated_at
```

---

## expert_assignments

```text
id
audit_id
expert_user_id
scope_type
scope_json
status
assigned_by
assigned_at
submitted_at
created_at
updated_at
```

---

## score_snapshots

```text
id
audit_id
audit_model_id
scope_type
scope_id
scoring_mode
skb
ikg
evaluated_units
eligible_terms
unstable_terms
missing_units
imputed_units
calculation_metadata_json
calculated_at
created_at
```

---

## reports

```text
id
audit_id
report_type
file_path
generated_by
score_snapshot_version
created_at
```

---

## audit_logs

```text
id
user_id
organisation_id
project_id
action
entity_type
entity_id
old_values_json
new_values_json
ip_address
user_agent
created_at
```

---

# 61. Laravel Folder Structure

Recommended:

```text
app/
├── Domain/
│   ├── Audits/
│   ├── CulturalTerms/
│   ├── Documents/
│   ├── Generations/
│   ├── Prompts/
│   ├── Reviews/
│   ├── Scoring/
│   └── Reports/
│
├── Services/
│   ├── AI/
│   │   ├── Contracts/
│   │   ├── Providers/
│   │   ├── DTOs/
│   │   └── AIProviderFactory.php
│   ├── Documents/
│   └── Export/
│
├── Jobs/
│   ├── GenerateTranslation.php
│   ├── ExtractTermOutputs.php
│   ├── CalculateAuditScores.php
│   └── GenerateAuditReport.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   └── Api/
│   ├── Requests/
│   └── Resources/
│
├── Models/
├── Policies/
├── Enums/
├── Events/
├── Listeners/
└── Notifications/
```

---

# 62. Controller Rule

Controllers should:

```text
authorize
validate
call domain/service
return response
```

Controllers must not contain:

```text
SKB formulas
IKG formulas
prompt logic
provider SDK logic
rating resolution logic
consensus algorithms
```

---

# 63. Recommended Enums

```text
AuditStatus
AuditMode
ProjectStatus
ScoringMode
PromptCode
PromptOrientation
RatingLevel
ReviewerRole
GenerationStatus
ExecutionEnvironment
DriftType
ExpertAssignmentStatus
RatingStatus
```

Example:

```php
enum RatingLevel: int
{
    case Inaccurate = 0;
    case LessAccurate = 1;
    case Accurate = 2;
}
```

---

# 64. Queue Flow

```text
User clicks Generate
↓
Create generation rows
↓
Dispatch GenerateTranslation jobs
↓
Call provider API
↓
Save raw response
↓
Save parsed translation
↓
Dispatch ExtractTermOutputs
↓
Create term output suggestions
↓
Update progress
```

Use queue workers.

Do not make long AI calls inside synchronous controller requests.

---

# 65. Failure Handling

A failure in one generation must not fail the whole audit.

Example:

```text
Claude PA = completed
Claude PB = failed
Claude PC = completed
Gemini PA = completed
Gemini PB = completed
Gemini PC = completed
```

Allow retry:

```text
attempt_number = 2
```

Preserve failed attempt history.

---

# 66. API Base

```text
/api/v1
```

---

# 67. Authentication Endpoints

```text
POST /auth/login
POST /auth/logout
GET  /auth/me
```

For mobile:

use Sanctum token authentication.

---

# 68. Project Endpoints

```text
GET    /projects
POST   /projects
GET    /projects/{project}
PATCH  /projects/{project}
DELETE /projects/{project}
```

---

# 69. Document Endpoints

```text
POST /projects/{project}/documents
GET  /documents/{document}
POST /documents/{document}/versions
GET  /documents/{document}/versions/{version}
```

---

# 70. Cultural Term Endpoints

```text
GET    /document-versions/{version}/cultural-terms
POST   /document-versions/{version}/cultural-terms
PATCH  /cultural-terms/{term}
DELETE /cultural-terms/{term}

POST /cultural-terms/{term}/classification-proposals
```

---

# 71. Audit Endpoints

```text
POST /projects/{project}/audits
GET  /audits/{audit}
POST /audits/{audit}/freeze
POST /audits/{audit}/generate
POST /audits/{audit}/clone
```

---

# 72. Generation Endpoints

```text
GET  /audits/{audit}/generations
GET  /generations/{generation}
POST /generations/{generation}/retry
```

---

# 73. Term Output Endpoints

```text
GET   /audits/{audit}/term-outputs
GET   /term-outputs/{termOutput}
PATCH /term-outputs/{termOutput}
POST  /term-outputs/{termOutput}/confirm-extraction
```

---

# 74. Rating Endpoints

```text
POST  /term-outputs/{termOutput}/ratings
PATCH /ratings/{rating}
POST  /ratings/{rating}/submit
```

---

# 75. Expert Endpoints

```text
POST /audits/{audit}/expert-assignments
GET  /expert/assignments
GET  /expert/assignments/{assignment}
POST /expert/assignments/{assignment}/submit
```

---

# 76. Score Endpoints

```text
GET /audits/{audit}/scores
GET /audits/{audit}/scores/models
GET /audits/{audit}/scores/categories
GET /audits/{audit}/scores/prompts
GET /audits/{audit}/agreement
```

Query option:

```text
?scoring_mode=verified_only
```

---

# 77. Report Endpoints

```text
POST /audits/{audit}/reports
GET  /audits/{audit}/reports
GET  /reports/{report}/download
```

---

# 78. Web Navigation

Main navigation:

```text
Dashboard
Projects
Documents
Audits
Expert Reviews
Reports
Settings
```

---

# 79. Project Navigation

```text
Overview
Documents
Cultural Inventory
Audit Runs
Collaborators
Reports
Settings
```

---

# 80. Audit Wizard

Recommended steps:

```text
1. Source Document
2. Cultural Terms
3. Models
4. Prompt Versions
5. Scoring Configuration
6. Review & Freeze
7. Generate
8. Confirm Extraction
9. Researcher Review
10. Expert Review
11. Results
12. Report
```

---

# 81. Flutter App Structure

```text
lib/
├── core/
│   ├── api/
│   ├── auth/
│   ├── routing/
│   ├── storage/
│   └── theme/
│
├── features/
│   ├── projects/
│   ├── documents/
│   ├── audits/
│   ├── review/
│   ├── scores/
│   ├── expert/
│   └── reports/
│
└── main.dart
```

---

# 82. Flutter MVP

Mobile should initially support:

- login,
- project list,
- audit status,
- review term outputs,
- submit rating,
- expert review,
- score dashboard,
- report access.

Keep advanced configuration web-first initially.

---

# 83. Security

Requirements:

- encrypted AI credentials,
- secure password hashing,
- role-based permissions,
- organisation isolation,
- Laravel Policies,
- signed report URLs,
- input validation,
- rate limiting,
- CSRF protection on web,
- token authentication on mobile,
- audit logs,
- no API secrets sent to browser/mobile.

---

# 84. Multi-Tenancy

Use organisation-level logical isolation.

Every project belongs to one organisation.

Every query must be permission checked.

Do not rely only on UI filtering.

Use Laravel Policies and scoped queries.

---

# 85. AI API Keys

Preferred first implementation:

```text
Platform-owned API keys
```

Optional later:

```text
Bring Your Own Key
```

If BYOK:

- encrypt at rest,
- never return raw key to client,
- all provider calls happen server-side.

---

# 86. Cost Tracking

Per generation store:

```text
input_tokens
output_tokens
cost
latency
```

Dashboard can show:

```text
Total cost
Cost by model
Cost by audit
```

Cost is operational.

Do not mix cost into MIZAN3G scientific score.

---

# 87. Testing Strategy

The most important tests are scoring and reproducibility tests.

---

## SKB Unit Test

Input:

```text
2, 2, 1, 0
```

Expected:

```text
5 / 8 = 0.625
```

---

## IKG Unit Test

```text
Term 1 = [2,2,2]
Term 2 = [2,1,2]
Term 3 = [0,0,0]
```

Expected:

```text
unstable = 1
eligible = 3
IKG = 0.333333
```

---

## Missing Expert Rating Tests

Test:

- verified only,
- manuscript compatible,
- expert only,
- consensus.

---

# 88. Scientific Integrity Tests

Add tests that prevent:

1. prompt mutation after freeze,
2. document mutation after freeze,
3. term mutation after freeze,
4. deletion of completed generation,
5. silent imputation,
6. IKG calculation without complete PA/PB/PC ratings,
7. scoring draft ratings,
8. cross-organisation data access,
9. overwriting submitted expert review,
10. accidental mixing of different document versions.

---

# 89. Initial Seed Data

Seed:

- 8 Ghazala categories,
- default subcategories where appropriate,
- drift types,
- translation procedure codes,
- PA prompt template,
- PB prompt template,
- PC prompt template,
- system roles.

Do not seed copyrighted story text without permission.

Use demo/sample content.

---

# 90. MVP Scope

MVP must include:

```text
Authentication
Organisations
Projects
Document upload/paste
Document versioning
Cultural term inventory
Ghazala categories
Term selection
PA/PB/PC prompts
Prompt versioning
Claude integration
Gemini integration
Queued generation
Term extraction
Manual extraction confirmation
Researcher ratings
Expert ratings
SKB calculation
IKG calculation
Model comparison
Category breakdown
Basic agreement statistics
PDF report
Audit log
```

---

# 91. Version 1.1

Add:

```text
Multiple experts
Cohen's Kappa dashboard
Sensitivity analysis
Translation procedure coding
CSV/XLSX export
Prompt customisation
Project cloning
Advanced report builder
```

---

# 92. Version 2

Add:

```text
Repeated experimental runs
Weighted instability
Additional language pairs
Additional cultural frameworks
AI-assisted cultural-term detection
Statistical analysis
Multi-expert consensus
Benchmark mode
Public research datasets
Advanced collaboration
```

---

# 93. Audit Modes

Recommended:

```text
PILOT_COMPATIBLE
PROFESSIONAL_AUDIT
REPEATED_RESEARCH
CUSTOM
```

---

## PILOT_COMPATIBLE

Rules:

```text
PA, PB, PC
One generation per prompt/model
Ghazala categories
Original SKB
Original IKG
```

---

## PROFESSIONAL_AUDIT

Rules:

```text
Flexible term count
Multiple models
Expert optional
Operational reporting
```

---

## REPEATED_RESEARCH

Rules:

```text
Multiple experimental replicates
Prompt sensitivity analysis
Randomness baseline
Advanced comparison
```

---

# 94. Repeated-Run Research Mode

Future experiment example:

```text
3 models
3 prompts
5 replicates
```

Total:

```text
3 × 3 × 5 = 45 full text generations
```

This allows researchers to distinguish:

```text
prompt sensitivity
vs
generation randomness
```

---

# 95. Implementation Order for Codex

Codex should not try to implement everything in one pass.

Build sequentially.

---

# 96. Phase 0 — Repository Setup

Create:

- Laravel application,
- PostgreSQL connection,
- Redis,
- Sanctum,
- Pint,
- PHPUnit/Pest,
- queue configuration,
- environment files,
- Git repository.

Recommended local environment:

```text
Docker Compose
```

Services:

```text
app
nginx
postgres
redis
queue
```

---

# 97. Phase 1 — Authentication and Organisations

Implement:

- users,
- login/logout,
- organisation creation,
- organisation membership,
- roles,
- policies.

Acceptance:

- organisation data isolated,
- authenticated user can create a project only where authorised.

---

# 98. Phase 2 — Projects and Documents

Implement:

- projects,
- source documents,
- source document versions,
- upload/paste text,
- file storage,
- content hashes.

Acceptance:

- editing a used document creates a new version,
- old audit still references old version.

---

# 99. Phase 3 — Cultural Inventory

Implement:

- cultural categories,
- subcategories,
- cultural terms,
- source context,
- term selection,
- balanced/flexible mode.

Acceptance:

- researcher can select 24 balanced terms,
- audit can snapshot term classifications.

---

# 100. Phase 4 — Prompt System

Implement:

- prompt templates,
- prompt versions,
- PA/PB/PC seed data,
- version locking.

Acceptance:

- used prompt version cannot be modified,
- new edit creates a new prompt version.

---

# 101. Phase 5 — AI Providers

Implement:

- provider interface,
- provider factory,
- Anthropic,
- Gemini,
- manual/import mode,
- encrypted configuration.

Acceptance:

- controller does not know provider SDK,
- provider can be swapped via model configuration.

---

# 102. Phase 6 — Audit Creation and Freeze

Implement:

- audit wizard,
- audit terms,
- audit prompts,
- audit models,
- immutable snapshots,
- audit state machine.

Acceptance:

- frozen audit configuration cannot change.

---

# 103. Phase 7 — Generation Queue

Implement:

- generation records,
- GenerateTranslation job,
- retry,
- token/cost/latency storage,
- progress tracking.

Acceptance:

- six generation jobs can run independently,
- one failure does not cancel others.

---

# 104. Phase 8 — Term Extraction

Implement:

- AI-assisted extraction service,
- term_output table,
- manual confirmation,
- omission status,
- transliteration field.

Acceptance:

- no extracted result is considered confirmed until human approval.

---

# 105. Phase 9 — Researcher Ratings

Implement:

- rating UI,
- 0/1/2 rubric,
- drift tags,
- rationale,
- submit/finalise state.

Acceptance:

- draft ratings excluded from scientific scores.

---

# 106. Phase 10 — Expert Review

Implement:

- assignments,
- blind review,
- submitted ratings,
- expert notes,
- proposed classification.

Acceptance:

- expert does not see researcher rating when blind mode is active.

---

# 107. Phase 11 — Scoring Engine

Implement:

```text
RatingResolver
SKBCalculator
IKGCalculator
AgreementCalculator
ScoreSnapshotService
```

Suggested folder:

```text
app/Domain/Scoring/
```

Acceptance:

- all formulas covered by unit tests,
- all results include metadata explaining denominator and missing data.

---

# 108. Phase 12 — Dashboard

Implement:

- model cards,
- category table,
- prompt table,
- SKB chart,
- IKG chart,
- two-axis chart,
- disagreement summary.

Acceptance:

- user can drill down from model score to individual term output.

---

# 109. Phase 13 — Reporting

Implement:

- PDF generation,
- CSV export,
- reproducibility package,
- score snapshot.

Acceptance:

- report can be regenerated using frozen audit data.

---

# 110. Phase 14 — Flutter Mobile App

After REST API stabilises:

Implement:

- authentication,
- project list,
- audit overview,
- review flow,
- ratings,
- expert assignments,
- results,
- report viewing.

Do not build mobile before backend contracts stabilise.

---

# 111. Recommended Laravel Services

```text
DocumentVersionService
CulturalTermService
AuditBuilderService
AuditFreezeService
PromptVersionService
GenerationService
TermExtractionService
RatingResolutionService
MizanScoringService
AgreementService
SensitivityAnalysisService
ReportService
AuditLogService
```

---

# 112. Domain Objects / DTOs

Recommended:

```text
GenerationRequest
GenerationResult
ResolvedRating
SKBResult
IKGResult
AgreementResult
AuditSnapshot
TermExtractionResult
```

Example:

```php
final class SKBResult
{
    public function __construct(
        public readonly float $score,
        public readonly int $numerator,
        public readonly int $denominator,
        public readonly int $evaluatedUnits,
        public readonly int $missingUnits,
        public readonly int $imputedUnits,
    ) {}
}
```

---

# 113. Suggested Events

```text
AuditFrozen
GenerationStarted
GenerationCompleted
GenerationFailed
TermExtractionCompleted
ResearcherReviewCompleted
ExpertReviewCompleted
AuditReadyForScoring
ScoreCalculated
ReportGenerated
```

Use listeners for notifications and secondary actions.

---

# 114. Suggested Notifications

```text
ExpertAssignmentCreated
GenerationCompleted
GenerationFailed
ExpertReviewSubmitted
AuditReadyForScoring
ReportReady
```

Support email later.

---

# 115. Recommended Policies

```text
OrganisationPolicy
ProjectPolicy
DocumentPolicy
AuditPolicy
GenerationPolicy
RatingPolicy
ExpertAssignmentPolicy
ReportPolicy
```

---

# 116. UI Design Principles

The UI should feel like:

```text
Research platform
+
Audit dashboard
+
Translation comparison tool
```

Not like a generic chatbot.

Important:

- structured tables,
- side-by-side comparison,
- visible metadata,
- status badges,
- score explanations,
- audit trail.

---

# 117. Important Screen — Audit Overview

Display:

```text
Audit Name
Source Document
Document Version
Source → Target Language
Mode
Models
Prompts
Selected Terms
Status
Progress
Scoring Mode
```

Then:

```text
Generation Matrix

             PA       PB       PC
Claude       Done     Done     Done
Gemini       Done     Failed   Done
```

---

# 118. Important Screen — Term Comparison

Header:

```text
Term: Pengganas
Category: Political
Source Context: ...
```

Columns:

```text
Claude PA
Claude PB
Claude PC
Gemini PA
Gemini PB
Gemini PC
```

Each cell:

```text
Arabic output
Transliteration
Rating
Drift labels
Reviewer notes
```

This screen is one of the main intellectual views of the system.

---

# 119. Important Screen — Results

Tabs:

```text
Overview
Models
Categories
Prompts
Terms
Stability
Agreement
Drift Cases
Sensitivity
```

---

# 120. Important Screen — Expert Workspace

Expert sees:

```text
Assignment progress
Source term
Source context
Full generated context
Target expression
Rubric
Rating
Drift
Comment
Submit
```

When blind mode enabled:

hide researcher data.

---

# 121. Search and Filters

Allow filters by:

```text
Model
Prompt
Category
Rating
Stable / Unstable
Drift type
Reviewer
Expert status
Missing rating
Imputed rating
```

---

# 122. Data Provenance

Every scientific value shown should have a traceable source.

Example:

```text
SKB 0.797
↓
69 verified units
↓
Ratings
↓
Term outputs
↓
Generations
↓
Prompt version + model + source snapshot
```

This provenance chain must remain accessible.

---

# 123. Audit Logging

Log important changes:

```text
project created
document version created
term classified
audit frozen
generation run
generation retry
term extraction edited
researcher rating submitted
expert rating submitted
classification changed
score generated
report generated
```

Do not log AI API secrets.

---

# 124. Scientific Disclaimer

Reports should include something like:

```text
MIZAN3G scores are descriptive audit measures.
Results depend on source corpus, selected terms, prompt versions,
model version, generation environment, reviewer expertise, and scoring mode.
They should not be interpreted as universal rankings of AI models.
```

---

# 125. Non-Goals for MVP

Do not overbuild.

MVP does not need:

- social feed,
- chatbot conversation UI,
- custom model fine-tuning,
- automatic publication submission,
- public marketplace,
- real-time collaborative editing,
- fully automatic cultural judgement,
- statistical significance testing,
- complex psychometric validation.

---

# 126. Development Rules for Codex

Codex must follow these instructions.

## Rule 1

Before implementing a module:

identify:

```text
purpose
input
output
database tables
domain service
API route
permissions
tests
```

---

## Rule 2

Do not invent new scientific formulas without explicit approval.

---

## Rule 3

Do not simplify SKB and IKG into one number.

---

## Rule 4

Do not calculate IKG from translation text similarity.

IKG is based on **rating stability** across prompts.

---

## Rule 5

Do not delete historical audit data.

---

## Rule 6

Do not silently impute missing expert ratings.

---

## Rule 7

Do not automatically trust AI term extraction.

Require human confirmation.

---

## Rule 8

Do not expose API keys to Flutter or browser clients.

---

## Rule 9

Do not duplicate scoring logic between web and mobile.

Laravel is authoritative.

---

## Rule 10

Every scientific calculation must be unit tested.

---

# 127. First Codex Task

The first implementation task should be:

```text
Create the Laravel project foundation and database/domain skeleton
for MIZAN3G without implementing AI APIs yet.
```

Specifically:

1. configure PostgreSQL,
2. configure Sanctum,
3. create authentication,
4. create organisations,
5. create projects,
6. create source documents and versions,
7. create cultural categories,
8. create cultural terms,
9. create prompt templates and versions,
10. create audit tables,
11. create Laravel enums,
12. create policies,
13. seed Ghazala categories,
14. seed PA/PB/PC templates,
15. write migrations and model relationships,
16. write basic feature tests.

Only after this foundation is stable should Codex implement AI providers.

---

# 128. Suggested First Repository Milestones

## Milestone 1

```text
Authentication + Organisations
```

## Milestone 2

```text
Projects + Documents
```

## Milestone 3

```text
Cultural Inventory
```

## Milestone 4

```text
Prompt Versioning
```

## Milestone 5

```text
Audit Freeze
```

## Milestone 6

```text
AI Generation
```

## Milestone 7

```text
Term Extraction
```

## Milestone 8

```text
Researcher/Expert Review
```

## Milestone 9

```text
SKB/IKG
```

## Milestone 10

```text
Dashboard + Reports
```

## Milestone 11

```text
Flutter Mobile
```

---

# 129. Definition of Done for MVP

The MVP is considered complete when a researcher can:

1. create a project,
2. upload a Malay source text,
3. identify and classify cultural terms,
4. select audit terms,
5. choose Claude and Gemini,
6. freeze PA/PB/PC prompt versions,
7. generate all model/prompt translations,
8. confirm target term extraction,
9. rate each output,
10. assign expert review,
11. calculate SKB,
12. calculate IKG,
13. compare models,
14. inspect category performance,
15. inspect prompt instability,
16. see agreement statistics,
17. generate a reproducible PDF report,
18. access the same project from Flutter for review tasks.

---

# 130. Final Product Vision

MIZAN3G should become more than a single research implementation.

The long-term product should be a general platform for:

> **Auditing the cultural reliability and prompt stability of generative AI translation systems.**

The initial Malay-to-Arabic research project is the first implementation.

The architecture should therefore allow future expansion to:

```text
Malay → English
Arabic → Malay
English → Arabic
Indonesian → Arabic
other culturally distant language pairs
```

The same core logic should remain reusable:

```text
Source
↓
Cultural Elements
↓
Multiple Prompt Orientations
↓
AI Outputs
↓
Human Evaluation
↓
Fidelity Score
+
Stability Score
↓
Transparent Audit
```

---

# 131. Final Instruction to Codex

When working on this project, always remember:

> **MIZAN3G is not simply a translation application. It is a reproducible cultural translation audit system.**

The purpose is not merely to generate Arabic.

The purpose is to detect cases where AI produces fluent Arabic while changing the cultural meaning of the Malay source.

The core scientific value lies in comparing:

```text
WHAT the AI translated
```

with:

```text
HOW STABLE that translation remains when the prompt orientation changes.
```

Every technical decision should preserve that objective.

