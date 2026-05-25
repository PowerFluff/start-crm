# Start CRM Progress Update

Date: 2026-05-26

## Summary

Started a learning CRM project from scratch to practice Docker, Laravel, PostgreSQL, and later React.

Current backend foundation is working:

```text
nginx -> php-fpm -> Laravel -> PostgreSQL
```

The project now has four connected CRM entities:

```text
Company
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

## Infrastructure Progress

- Created a separate project directory: `/Users/zaharanuhin/start-crm`.
- Set up Docker Compose with:
  - `nginx`;
  - `php-fpm`;
  - `postgres`.
- Configured nginx to serve Laravel through `public/index.php`.
- Built a custom PHP image with:
  - Composer;
  - PostgreSQL PHP extensions;
  - `intl` extension;
  - required system packages.
- Installed Laravel 13 inside the Docker environment.
- Connected Laravel to PostgreSQL through Docker service name `postgres`.
- Verified database connection with Laravel artisan commands.

## Concepts Covered

- Docker Compose services.
- Docker images vs containers.
- `image` vs `build`.
- Volumes and bind mounts.
- Port forwarding.
- Internal Docker networking.
- nginx and php-fpm responsibilities.
- nginx `default.conf`.
- Laravel project structure.
- Laravel migrations.
- Eloquent models.
- Query builder.
- Route model binding.
- API resources.
- Form requests.
- Mass assignment and `$fillable`.
- Pagination.
- Local query scopes.
- Eloquent relationships.
- Eager loading.
- N+1 query problem.
- PHP backed enums.
- Eloquent enum casts.
- Feature tests.
- Model factories.
- `RefreshDatabase`.
- Laravel Sanctum token authentication.
- Bearer tokens.
- Protected API routes.
- Basic ownership through `owner_id`.

## Company API

Implemented the `Company` CRM entity.

Files created/updated:

- `app/app/Models/Company.php`
- `app/app/Http/Controllers/CompanyController.php`
- `app/app/Http/Requests/StoreCompanyRequest.php`
- `app/app/Http/Requests/UpdateCompanyRequest.php`
- `app/app/Http/Resources/CompanyResource.php`
- `app/database/migrations/*_create_companies_table.php`
- `app/routes/api.php`

Implemented features:

- Create company.
- List companies.
- Show single company.
- Update company.
- Delete company.
- Paginated company list.
- Search by `name`, `website`, and `phone`.
- Optional owner relation through `owner_id`.
- Automatic company owner assignment from the authenticated user on create.
- Owner data in company API responses when loaded.

Available endpoints:

```text
GET    /api/companies
POST   /api/companies
GET    /api/companies/{company}
PATCH  /api/companies/{company}
PUT    /api/companies/{company}
DELETE /api/companies/{company}
```

## Contact API

Implemented the `Contact` CRM entity.

Files created/updated:

- `app/app/Models/Contact.php`
- `app/app/Http/Controllers/ContactController.php`
- `app/app/Http/Requests/StoreContactRequest.php`
- `app/app/Http/Requests/UpdateContactRequest.php`
- `app/app/Http/Resources/ContactResource.php`
- `app/database/migrations/*_create_contacts_table.php`
- `app/routes/api.php`

Implemented features:

- Create contact.
- List contacts.
- Show single contact.
- Update contact.
- Delete contact.
- Link contact to company through `company_id`.
- Return nested company data in contact API responses.

Available endpoints:

```text
GET    /api/contacts
POST   /api/contacts
GET    /api/contacts/{contact}
PATCH  /api/contacts/{contact}
PUT    /api/contacts/{contact}
DELETE /api/contacts/{contact}
```

Example contact response:

```json
{
  "id": 2,
  "company_id": 1,
  "company": {
    "id": 1,
    "name": "Acme Inc"
  },
  "first_name": "Maria",
  "last_name": "Smirnova",
  "email": "maria@example.com",
  "phone": "+7 999 333-44-55",
  "position": "Sales",
  "created_at": "2026-05-24T21:49:19.000000Z",
  "updated_at": "2026-05-24T21:49:19.000000Z"
}
```

## Deal API

Implemented the `Deal` CRM entity.

Files created/updated:

- `app/app/Enums/DealStatus.php`
- `app/app/Models/Deal.php`
- `app/app/Http/Controllers/DealController.php`
- `app/app/Http/Requests/IndexDealRequest.php`
- `app/app/Http/Requests/StoreDealRequest.php`
- `app/app/Http/Requests/UpdateDealRequest.php`
- `app/app/Http/Resources/DealResource.php`
- `app/database/migrations/*_create_deals_table.php`
- `app/routes/api.php`

Implemented features:

- Create deal.
- List deals.
- Show single deal.
- Update deal.
- Delete deal.
- Link deal to company through `company_id`.
- Return nested company data in deal API responses.
- Return nested tasks in single deal responses.
- Filter deals by `status`.
- Filter deals by `company_id`.
- Validate deal status through `DealStatus` enum.
- Cast deal status to `DealStatus` inside the Eloquent model.

Available endpoints:

```text
GET    /api/deals
POST   /api/deals
GET    /api/deals/{deal}
PATCH  /api/deals/{deal}
PUT    /api/deals/{deal}
DELETE /api/deals/{deal}
```

Supported deal statuses:

```text
new
in_progress
won
lost
```

## Task API

Implemented the `Task` CRM entity.

Files created/updated:

- `app/app/Enums/TaskStatus.php`
- `app/app/Models/Task.php`
- `app/app/Http/Controllers/TaskController.php`
- `app/app/Http/Requests/IndexTaskRequest.php`
- `app/app/Http/Requests/StoreTaskRequest.php`
- `app/app/Http/Requests/UpdateTaskRequest.php`
- `app/app/Http/Resources/TaskResource.php`
- `app/database/migrations/*_create_tasks_table.php`
- `app/routes/api.php`

Implemented features:

- Create task.
- List tasks.
- Show single task.
- Update task.
- Delete task.
- Link task to deal through `deal_id`.
- Return nested deal data in task API responses.
- Filter tasks by `status`.
- Filter tasks by `deal_id`.
- Validate task status through `TaskStatus` enum.
- Cast task status to `TaskStatus` inside the Eloquent model.

Available endpoints:

```text
GET    /api/tasks
POST   /api/tasks
GET    /api/tasks/{task}
PATCH  /api/tasks/{task}
PUT    /api/tasks/{task}
DELETE /api/tasks/{task}
```

Supported task statuses:

```text
todo
in_progress
done
canceled
```

## Test Coverage

Added feature tests for the core CRM API.

Test files:

- `app/tests/Feature/AuthProtectionTest.php`
- `app/tests/Feature/CompanyApiTest.php`
- `app/tests/Feature/ContactApiTest.php`
- `app/tests/Feature/DealApiTest.php`
- `app/tests/Feature/TaskApiTest.php`

Factories added:

- `app/database/factories/CompanyFactory.php`
- `app/database/factories/ContactFactory.php`
- `app/database/factories/DealFactory.php`
- `app/database/factories/TaskFactory.php`

Latest full test run:

```text
All feature tests were green after adding Sanctum protection and ownership changes.
```

Covered scenarios:

- paginated list responses;
- create endpoints;
- required validation;
- enum validation;
- filtering by status;
- filtering by relation id;
- nested relation responses.
- authenticated CRM API access;
- unauthenticated CRM API protection.

## Authentication

Added Sanctum token authentication for the API.

Files created/updated:

- `app/app/Http/Controllers/AuthController.php`
- `app/app/Models/User.php`
- `app/routes/api.php`

Implemented endpoints:

```text
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user
```

Authentication behavior:

- `register` creates a user and returns a token.
- `login` validates credentials and returns a token.
- `logout` deletes the current access token.
- `/api/user` returns the authenticated user.
- CRM resource routes are protected by `auth:sanctum`.

Protected routes:

```text
GET    /api/companies
POST   /api/companies
GET    /api/contacts
POST   /api/contacts
GET    /api/deals
POST   /api/deals
GET    /api/tasks
POST   /api/tasks
```

Requests without a valid Bearer token now return:

```text
401 Unauthorized
```

## Ownership

Started connecting CRM data to users.

Implemented so far:

- added nullable `owner_id` to `companies`;
- added `Company belongsTo User` as `owner`;
- added `User hasMany Company`;
- updated `CompanyResource` to expose `owner_id` and nested `owner`;
- updated `CompanyFactory` to create an owner by default;
- changed company creation so `owner_id` comes from the authenticated user.

Current rule:

```text
Authenticated user creates company
  -> company.owner_id = auth user id
```

Important note:

```text
The API no longer trusts client-provided owner_id for company creation.
```

## Important Learning Notes

Laravel chooses controller actions by HTTP method and URL:

```text
POST /api/companies           -> CompanyController@store
GET /api/companies            -> CompanyController@index
GET /api/companies/{company}  -> CompanyController@show
PATCH /api/companies/{company}-> CompanyController@update
DELETE /api/companies/{company}-> CompanyController@destroy
```

`Route::apiResource()` creates this CRUD routing table automatically.

Eloquent model meaning:

```text
Table row   -> model object
Table       -> model class
Column      -> model property
Relation    -> model method
```

Query builder meaning:

```text
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

Enum usage:

```php
Rule::in(DealStatus::values())
```

keeps validation rules in sync with one source of truth.

Eloquent enum casts:

```php
'status' => DealStatus::class
```

make `$deal->status` a `DealStatus` object inside the application while API resources still return plain strings:

```php
'status' => $this->status->value
```

Feature tests use `RefreshDatabase`, so each test starts from a clean database state.

Sanctum testing:

```php
Sanctum::actingAs(User::factory()->create());
```

sets the authenticated user for feature tests.

Protected route groups:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('deals', DealController::class);
    Route::apiResource('tasks', TaskController::class);
});
```

## Next Steps

1. Restrict company lists to the authenticated user's own companies.
2. Add admin/manager roles.
3. Add policies:
   - admin sees all;
   - manager sees own data.
4. Add `owner_id` to `deals` and `tasks` if needed by the CRM rules.
5. Add auth tests for register, login, logout, and `/api/user`.
6. Start React frontend after backend ownership/auth basics.

Current domain shape:

```text
Company -> Contacts
Company -> Deals
Deal -> Tasks
```
