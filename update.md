# Start CRM Progress Update

Date: 2026-05-25

## Summary

Started a learning CRM project from scratch to practice Docker, Laravel, PostgreSQL, and later React.

Current backend foundation is working:

```text
nginx -> php-fpm -> Laravel -> PostgreSQL
```

The project already has two connected CRM entities:

```text
Company
  has many Contacts

Contact
  belongs to Company
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

## Next Steps

1. Update `GET /api/companies/{company}` to return company contacts.
2. Add `Deal` entity.
3. Connect deals to companies.
4. Add deal statuses.
5. Add `Task` entity.
6. Start building a more complete CRM domain:

```text
Company -> Contact
Company -> Deal
Deal -> Task
```

