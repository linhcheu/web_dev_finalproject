# CareConnect API

The only CareConnect app with database access. The website (`../frontend`) and the admin panel
(`../backend`) read and write everything through it. All URLs start with `/api/v1`, and responses are JSON.

## Authentication

* `POST /auth/login` (patients, hospital admins) or `POST /admin/auth/login` (platform admins) returns `{ data: { token, user|admin } }`.
* Send the token as `Authorization: Bearer <token>`. Tokens expire after `SANCTUM_TOKEN_EXPIRATION` minutes.
* Login and registration are limited to 5 attempts per minute per e-mail.

## Endpoints

| Who | Method + path | What |
|---|---|---|
| anyone | `GET home` | popular hospitals + recent feedback |
| anyone | `GET hospitals?province=`, `GET hospitals/{id}` | approved hospitals |
| anyone | `POST auth/register`, `POST auth/register-hospital` | sign up (hospitals start *pending*) |
| member | `GET me`, `POST me` (multipart), `PUT me/password`, `POST auth/logout`, `POST feedback` | own account |
| patient | `GET bookable-hospitals`, `GET/POST appointments`, `GET appointments/{id}` | own appointments only |
| hospital admin | `GET/POST my-hospital`, `GET my-hospital/dashboard`, `GET my-hospital/appointments`, `PATCH my-hospital/appointments/{id}`, `PUT my-hospital/subscription`, `POST my-hospital/subscription/cancel` | own hospital only |
| admin | `GET admin/dashboard`, `admin/users`, `admin/hospitals`, `admin/appointments`, `admin/subscriptions`, `admin/feedback` (list, show, create, `POST /{id}` or `PUT /{id}` update, delete) | everything |

Lists are paginated: `{ data: [...], meta: { current_page, last_page, per_page, total } }`.
Validation errors return `422` with `{ message, errors: { field: [..] } }`.

## Code map

* `routes/api.php`: every route and who may call it (`account:` middleware)
* `app/Http/Controllers/Api/`: public, member, patient and hospital-admin endpoints
* `app/Http/Controllers/Api/Admin/`: admin-panel endpoints
* `app/Http/Middleware/EnsureAccountType.php`: role checks
* `app/Support/ImageStore.php`: picture uploads (`storage/app/public`)
* `database/`: the schema migration and demo seeders
* `tests/Feature/ApiTest.php`: security and behaviour tests (`php artisan test`)
