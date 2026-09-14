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

Open http://127.0.0.1:8001. For styled frontend assets, run `npm install` then `npm run dev -- --host 127.0.0.1` in a second terminal. Do not browse directly to Vite's port.

## Verification

```powershell
vendor/bin/pint --test
php artisan test
composer audit --locked
```

Local SQLite data, environment credentials, logs, and generated frontend manifests are ignored by Git.
