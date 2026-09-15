# MIZAN3G

MIZAN3G is a reproducible cultural-translation audit platform. It evaluates cultural fidelity (SKB) separately from prompt instability (IKG).

## Local run

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8001
```

Open http://127.0.0.1:8001. For styled frontend assets, run `npm install` then `npm run dev -- --host 127.0.0.1 --port 5176 --strictPort` in a second terminal. Do not browse directly to Vite's port.

## Audit workflow

1. Sign in, create a project, paste a source document, and classify/confirm cultural terms.
2. In **Audit runs**, create a draft, select the document version, models, and the PA/PB/PC prompt versions, then freeze it.
3. Set `ANTHROPIC_API_KEY` and/or `GEMINI_API_KEY` only in `.env` when using automated providers. Do not put provider keys in browser code, Flutter code, Git, or API requests.
4. Start a queue worker before starting an automated run:

```powershell
php artisan queue:work --tries=3
```

5. Confirm extracted term outputs, submit ratings, inspect separate SKB/IKG results, and download the PDF or reproducibility package.

`MANUAL_IMPORT` model configurations do not call an AI provider; import completed output through the generation API for controlled/manual research runs.

## Verification

```powershell
vendor/bin/pint --test
php artisan test
composer audit --locked
npm audit --omit=dev
```

Local SQLite data, environment credentials, logs, and generated frontend manifests are ignored by Git.
