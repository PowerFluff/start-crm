# Start CRM Progress Update

Date: 2026-06-07

## Summary

Учебный CRM-проект развивается как связка:

```text
nginx -> php-fpm -> Laravel API -> PostgreSQL
```

Рабочая директория проекта:

```text
/Users/zaharanuhin/start-crm
```

Основная цель проекта:

```text
Laravel API backend + React frontend
```

Backend уже содержит CRM API, авторизацию через Sanctum, тесты, ownership для компаний и первый слой policies.

## Infrastructure

Сделано:

- настроен Docker Compose;
- сервисы:
  - `nginx`;
  - `php`;
  - `postgres`;
- Laravel установлен внутри `app`;
- nginx отдаёт Laravel через `public/index.php`;
- PostgreSQL доступен Laravel через Docker service name `postgres`;
- PHP image содержит Composer, PostgreSQL extensions и `intl`;
- локальный backend открыт на:

```text
http://localhost:8080
```

Основные команды запускаются из корня проекта:

```bash
cd /Users/zaharanuhin/start-crm
docker compose exec php php artisan ...
```

## Current Domain Shape

Основная CRM-модель:

```text
User
  has many Companies

Company
  belongs to User as owner
  has many Contacts
  has many Deals

Contact
  belongs to Company

Deal
  belongs to Company
  has many Tasks

Task
  belongs to Deal
```

Дополнительно создан учебный модуль:

```text
Client
```

`Client` используется как простая CRUD-сущность для закрепления Laravel API, resources, validation, factories и feature tests.

## Concepts Covered

Уже разобраны и применены:

- Docker Compose services;
- Docker image vs container;
- volume и bind mount;
- port forwarding;
- internal Docker networking;
- nginx и php-fpm;
- Laravel project structure;
- routes;
- controllers;
- migrations;
- Eloquent models;
- mass assignment и `$fillable`;
- query builder;
- pagination;
- local query scopes;
- route model binding;
- form requests;
- API resources;
- Eloquent relationships;
- eager loading;
- N+1 problem;
- PHP backed enums;
- Eloquent enum casts;
- model factories;
- feature tests;
- `RefreshDatabase`;
- Laravel Sanctum;
- Bearer token auth;
- protected API routes;
- JSON API error handling;
- basic ownership;
- Laravel policies;
- `AuthorizesRequests`.

## Authentication

Реализована API-аутентификация через Laravel Sanctum.

Endpoints:

```text
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user
```

Поведение:

- `register` создаёт пользователя и возвращает token;
- `login` проверяет credentials и возвращает token;
- `logout` удаляет текущий access token;
- `/api/user` возвращает текущего пользователя;
- CRM routes защищены middleware `auth:sanctum`.

API без токена теперь корректно возвращает:

```json
{
  "message": "Unauthenticated."
}
```

Для `/api/*` настроено JSON-поведение ошибок в `app/bootstrap/app.php`.

Также настроено, что guest API-запросы не пытаются редиректиться на web route `login`.

## Company API

Implemented files:

- `app/app/Models/Company.php`
- `app/app/Http/Controllers/CompanyController.php`
- `app/app/Http/Requests/StoreCompanyRequest.php`
- `app/app/Http/Requests/UpdateCompanyRequest.php`
- `app/app/Http/Resources/CompanyResource.php`
- `app/app/Policies/CompanyPolicy.php`
- `app/database/factories/CompanyFactory.php`
- `app/database/migrations/*_create_companies_table.php`
- `app/database/migrations/*_add_owner_id_to_companies_table.php`
- `app/tests/Feature/CompanyApiTest.php`

Endpoints:

```text
GET    /api/companies
POST   /api/companies
GET    /api/companies/{company}
PATCH  /api/companies/{company}
PUT    /api/companies/{company}
DELETE /api/companies/{company}
```

Implemented features:

- create company;
- list companies;
- show single company;
- update company;
- delete company;
- paginated company list;
- search by `name`, `website`, `phone`;
- `owner_id` relation;
- `Company belongsTo User` as `owner`;
- `User hasMany Company`;
- company creation assigns `owner_id` from authenticated user;
- API does not trust client-provided `owner_id` on create;
- company responses can include nested owner data;
- company show response can include contacts and deals.

Current ownership rules:

```text
GET /api/companies
  -> returns only authenticated user's companies

GET /api/companies/{company}
  -> CompanyPolicy@view

PATCH/PUT /api/companies/{company}
  -> CompanyPolicy@update

DELETE /api/companies/{company}
  -> CompanyPolicy@delete
```

`CompanyPolicy` currently allows access only when:

```php
$company->owner_id === $user->id
```

The base controller now uses:

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
```

so resource controllers can call:

```php
$this->authorize(...)
```

## Contact API

Implemented files:

- `app/app/Models/Contact.php`
- `app/app/Http/Controllers/ContactController.php`
- `app/app/Http/Requests/StoreContactRequest.php`
- `app/app/Http/Requests/UpdateContactRequest.php`
- `app/app/Http/Resources/ContactResource.php`
- `app/database/factories/ContactFactory.php`
- `app/database/migrations/*_create_contacts_table.php`
- `app/tests/Feature/ContactApiTest.php`

Endpoints:

```text
GET    /api/contacts
POST   /api/contacts
GET    /api/contacts/{contact}
PATCH  /api/contacts/{contact}
PUT    /api/contacts/{contact}
DELETE /api/contacts/{contact}
```

Implemented features:

- create contact;
- list contacts;
- show single contact;
- update contact;
- delete contact;
- link contact to company through `company_id`;
- return nested company data in contact API responses.

Ownership is not fully enforced yet for contacts. Next planned step is to authorize contacts through their company:

```text
Contact -> Company -> owner_id
```

## Deal API

Implemented files:

- `app/app/Enums/DealStatus.php`
- `app/app/Models/Deal.php`
- `app/app/Http/Controllers/DealController.php`
- `app/app/Http/Requests/IndexDealRequest.php`
- `app/app/Http/Requests/StoreDealRequest.php`
- `app/app/Http/Requests/UpdateDealRequest.php`
- `app/app/Http/Resources/DealResource.php`
- `app/database/factories/DealFactory.php`
- `app/database/migrations/*_create_deals_table.php`
- `app/tests/Feature/DealApiTest.php`

Endpoints:

```text
GET    /api/deals
POST   /api/deals
GET    /api/deals/{deal}
PATCH  /api/deals/{deal}
PUT    /api/deals/{deal}
DELETE /api/deals/{deal}
```

Implemented features:

- create deal;
- list deals;
- show single deal;
- update deal;
- delete deal;
- link deal to company through `company_id`;
- return nested company data in deal API responses;
- return nested tasks in single deal responses;
- filter deals by `status`;
- filter deals by `company_id`;
- validate deal status through `DealStatus`;
- cast deal status to `DealStatus` in Eloquent.

Supported statuses:

```text
new
in_progress
won
lost
```

Ownership is not fully enforced yet for deals. Planned rule:

```text
Deal -> Company -> owner_id
```

## Task API

Implemented files:

- `app/app/Enums/TaskStatus.php`
- `app/app/Models/Task.php`
- `app/app/Http/Controllers/TaskController.php`
- `app/app/Http/Requests/IndexTaskRequest.php`
- `app/app/Http/Requests/StoreTaskRequest.php`
- `app/app/Http/Requests/UpdateTaskRequest.php`
- `app/app/Http/Resources/TaskResource.php`
- `app/database/factories/TaskFactory.php`
- `app/database/migrations/*_create_tasks_table.php`
- `app/tests/Feature/TaskApiTest.php`

Endpoints:

```text
GET    /api/tasks
POST   /api/tasks
GET    /api/tasks/{task}
PATCH  /api/tasks/{task}
PUT    /api/tasks/{task}
DELETE /api/tasks/{task}
```

Implemented features:

- create task;
- list tasks;
- show single task;
- update task;
- delete task;
- link task to deal through `deal_id`;
- return nested deal data in task API responses;
- filter tasks by `status`;
- filter tasks by `deal_id`;
- validate task status through `TaskStatus`;
- cast task status to `TaskStatus` in Eloquent.

Supported statuses:

```text
todo
in_progress
done
canceled
```

Ownership is not fully enforced yet for tasks. Planned rule:

```text
Task -> Deal -> Company -> owner_id
```

## Client API

Created as a learning CRUD module.

Implemented files:

- `app/app/Models/Client.php`
- `app/app/Http/Controllers/ClientController.php`
- `app/app/Http/Requests/StoreClientRequest.php`
- `app/app/Http/Requests/UpdateClientRequest.php`
- `app/app/Http/Resources/ClientResource.php`
- `app/database/factories/ClientFactory.php`
- `app/database/migrations/2026_05_28_142030_create_clients_table.php`
- `app/tests/Feature/ClientApiTest.php`

Endpoints:

```text
GET    /api/clients
POST   /api/clients
GET    /api/clients/{client}
PATCH  /api/clients/{client}
PUT    /api/clients/{client}
DELETE /api/clients/{client}
```

Implemented features:

- create client;
- list clients;
- show single client;
- update client;
- delete client;
- validate required `name`;
- validate email format;
- return API responses through `ClientResource`;
- search clients by:
  - `name`;
  - `email`;
  - `phone`;
  - `company`;
- paginated list response with `data` and `meta`;
- full feature test coverage.

Search endpoint example:

```text
GET /api/clients?search=ivan
```

Important test/database note:

```text
PostgreSQL supports ILIKE.
SQLite does not support ILIKE.
```

`Client::scopeSearch()` now selects the search operator based on the active database driver:

```text
pgsql  -> ilike
sqlite -> like
```

## Test Coverage

Current test files:

- `app/tests/Feature/AuthProtectionTest.php`
- `app/tests/Feature/ClientApiTest.php`
- `app/tests/Feature/CompanyApiTest.php`
- `app/tests/Feature/ContactApiTest.php`
- `app/tests/Feature/DealApiTest.php`
- `app/tests/Feature/TaskApiTest.php`

Current factories:

- `app/database/factories/UserFactory.php`
- `app/database/factories/ClientFactory.php`
- `app/database/factories/CompanyFactory.php`
- `app/database/factories/ContactFactory.php`
- `app/database/factories/DealFactory.php`
- `app/database/factories/TaskFactory.php`

Latest full test run:

```text
Tests: 33 passed (244 assertions)
Duration: 1.04s
```

Covered scenarios include:

- authenticated API access;
- unauthenticated API protection;
- paginated list responses;
- create endpoints;
- validation errors;
- enum validation;
- search;
- filters by status;
- filters by relation id;
- nested relation responses;
- route model binding;
- ownership for company list/show/update/delete;
- policy-based company authorization.

## Important Learning Notes

Laravel chooses controller actions by HTTP method and URL:

```text
POST /api/companies             -> CompanyController@store
GET /api/companies              -> CompanyController@index
GET /api/companies/{company}    -> CompanyController@show
PATCH /api/companies/{company}  -> CompanyController@update
DELETE /api/companies/{company} -> CompanyController@destroy
```

`Route::apiResource()` creates this CRUD routing table automatically.

Eloquent model meaning:

```text
Table row -> model object
Table     -> model class
Column    -> model property
Relation  -> model method
```

Query builder example:

```php
Company::query()
    ->search($search)
    ->latest()
    ->paginate(10);
```

This gradually builds SQL and executes it only on terminal methods such as:

- `get()`;
- `first()`;
- `paginate()`;
- `count()`;
- `exists()`.

Route model binding example:

```php
public function show(Company $company): JsonResponse
```

For:

```text
GET /api/companies/5
```

Laravel automatically resolves:

```php
Company::findOrFail(5)
```

Sanctum testing:

```php
Sanctum::actingAs(User::factory()->create());
```

sets the authenticated user for feature tests.

Policy authorization:

```php
$this->authorize('view', $company);
```

delegates access decisions to:

```text
App\Policies\CompanyPolicy
```

## Current Status

Stable backend checkpoint:

```text
Docker + Laravel + PostgreSQL + Sanctum + CRM API + Client learning CRUD + tests + CompanyPolicy
```

Full suite is green:

```text
33 tests passed
244 assertions
```

## Next Steps

1. Add ownership authorization for `Contact`.
   - Create `ContactPolicy`.
   - Authorize by `Contact -> Company -> owner_id`.
   - Add tests:
     - user cannot show another user's contact;
     - user cannot update another user's contact;
     - user cannot delete another user's contact.
   - Restrict contact list to contacts from current user's companies.

2. Add ownership authorization for `Deal`.
   - Authorize by `Deal -> Company -> owner_id`.
   - Restrict deal list to current user's companies.

3. Add ownership authorization for `Task`.
   - Authorize by `Task -> Deal -> Company -> owner_id`.
   - Restrict task list to tasks from current user's companies.

4. Add roles.
   - `admin`;
   - `manager`.

5. Expand policies for roles.
   - admin sees all;
   - manager sees own data.

6. Add auth endpoint tests.
   - register;
   - login;
   - logout;
   - `/api/user`.

7. Start React frontend after backend ownership/auth rules are stable.
