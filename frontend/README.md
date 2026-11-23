# CareConnect website

Public site, patient area and hospital-admin portal. **It has no database:** every page gets its
data from the CareConnect API (`../api`), using the token from the user's login.

* `app/Services/Api/ApiClient.php`: calls the API. A 422 becomes normal form errors, a 401 logs the user out, and when the API is down the user sees a 503 page.
* `app/Services/Api/ApiSessionGuard.php`: keeps the logged-in account and API token in the session, so `auth()->user()` and `@auth` work as usual.
* `app/Models/*`: read-only models filled from API JSON (`ApiModel::fromApi()`).
* `media_url($path)`: URL of an uploaded picture (`API_MEDIA_URL`).

Settings (`.env`): `API_URL`, `API_MEDIA_URL`, and a unique `SESSION_COOKIE`.
Tests: `php artisan test` (the API is faked).
