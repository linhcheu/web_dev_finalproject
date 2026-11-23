# CareConnect admin panel

For platform administrators: dashboard, users, hospitals (including approving new ones),
appointments, subscriptions and feedback. **It has no database:** everything goes through the
CareConnect API (`../api`) with the admin's token.

* `app/Services/Api/`: the same API client and session guard as the website
* `app/Http/Controllers/*`: one controller per admin page
* `public/css/admin.css`: the Tailwind CSS for the views, pre-built, so no build step is needed on the server. After changing Tailwind classes in a view, run `npm install && npm run build:css`.

Settings (`.env`): `API_URL`, `API_MEDIA_URL`, and a unique `SESSION_COOKIE`.
Tests: `php artisan test` (the API is faked).
