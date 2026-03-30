# BukSU Practicum Portal

> Auto-generated project documentation. This file is refreshed by `php artisan docs:generate-project` and automatically updates when college records are created, updated, or deleted through the application.

## 1. Project Summary

The BukSU Practicum Portal is a multitenant Laravel application that separates the **University Administration application** from each **college portal**.

- The **University Administration application** runs with no tenant context and is used by the BukSU University Admin to manage colleges, license tiers, domains, and registration.
- Each **college portal** runs in tenant context and is used by each college for practicum operations, role-based access, and college-specific records.

## 2. Architecture

### University Administration Application

- Uses the central database: `central` with central connection `central`.
- Main responsibilities:
- Authenticate the BukSU University Admin.
- Create and register college portals.
- Approve college applications and assign tenant access domains.
- Create college databases and launch college migrations.
- Maintain the college directory and launch links.

### College Portal Application

- Uses the tenant connection `tenant` after college resolution.
- Main responsibilities:
- Authenticate internship coordinators, company supervisors, and students.
- Manage partner companies, student applications, forms and requirements, progress reports, and evaluation workflows.
- Render college dashboards for each role.

## 3. Databases

- Central database connection: `central`.
- Tenant database connection: `tenant`.
- Base domain for generated college subdomains: `buksu.test`.
- Central domains: `127.0.0.1`, `localhost`, `lvh.me`.

### Central Database Stores

- College registry and metadata
- Approved tenant domain records
- BukSU University Admin accounts
- Shared app-level configuration

### College Portal Database Stores

- Internship coordinators
- Company supervisors
- Students
- Partner companies
- Student applications
- Forms and requirements
- Progress and hour logs
- Evaluation records

## 4. Authentication and Roles

### Central Role

- BukSU University Admin
- Login: `/central/login` on a central domain such as `lvh.me`, `127.0.0.1`, or `localhost`.

### College Portal Roles

- College Admin / Internship Coordinator: manages partner companies, reviews submissions, assigns students, tracks OJT hours, and reviews evaluations.
- Company Supervisor: accepts or rejects assigned students, logs attendance or hours, submits evaluation forms, and validates student reports.
- Student: views partner companies, applies for internship slots, uploads requirements, submits reports, and tracks OJT progress.
- Shared college portal login: `/login` on a tenant hostname such as `technology.lvh.me:8000`.

## 5. Route Structure

- `routes/web.php`: top-level entry resolver.
- `routes/central.php`: central application routes.
- `routes/tenant.php`: college portal application routes.

### Important Central Routes

- `GET /` -> central app entry resolver
- `GET /central/login` -> University Administration login page
- `GET /central/dashboard` -> University Administration dashboard
- `POST /central/tenants` -> register a new college and its access metadata

### Important Tenant Routes

- `GET /` on a tenant hostname -> college portal entry
- `GET /login` on a tenant hostname -> college portal login page
- `GET /admin/dashboard` on a tenant hostname -> internship coordinator dashboard
- `GET /supervisor/dashboard` on a tenant hostname -> company supervisor dashboard
- `GET /student/dashboard` on a tenant hostname -> student dashboard

## 6. Provisioning Flow

When the BukSU University Admin registers a new college from the central dashboard, the application:

1. Saves the college metadata in the central database.
2. Stores any approved direct-access domains in the central domain registry.
3. Creates the college database if it does not yet exist.
4. Runs college migrations on the new database.
5. Creates the first internship coordinator account.
6. Refreshes this project documentation file automatically.

## 7. Current Managed Tenants

| College | Code | License Tier | Approved Domains | Database | Status |
| --- | --- | --- | --- | --- | --- |
| College of Business | COB | PREMIUM | n/a | buksu_college_of_business | Active |
| College of Technology | COT | PREMIUM | cot.lvh.me, cot.localhost | buksu_college_of_technology | Active |

## 8. Seeded Local Credentials

### BukSU University Admin

- Email: `superadmin@buksu.test`
- Password: defined by `CENTRAL_SUPERADMIN_PASSWORD` in `.env`

### Default College Demo Accounts

- Internship Coordinator: `admin@technology.lvh.me` / `password123`
- Company Supervisor: `supervisor@technology.lvh.me` / `password123`
- Student: `student@technology.lvh.me` / `password123`

## 9. Local Development

### XAMPP Workflow

- Start Apache and MySQL in XAMPP.
- Start the app with `php artisan serve --host=127.0.0.1 --port=8000`.
- Open the central app at `http://lvh.me:8000/central/login`.
- Open college portals with `http://technology.lvh.me:8000/login` or another tenant hostname.
- Run `npm run build` only when frontend assets change.

### Maintenance Commands

```bash
php artisan migrate
php artisan db:seed
php artisan tenants:migrate 1
php artisan tenants:seed 1
php artisan docs:generate-project
npm run build
```

## 10. Documentation Automation

- This file is generated at: `docs/PROJECT_DOCUMENTATION.md`.
- Manual refresh command: `php artisan docs:generate-project`.
- Automatic refresh happens whenever a college record is saved or deleted through the application.

## 11. Key Files

- Central dashboard controller: `app/Http/Controllers/Central/CentralDashboardController.php`
- Central provisioning controller: `app/Http/Controllers/Central/TenantProvisionController.php`
- Central auth controller: `app/Http/Controllers/Central/CentralAuthController.php`
- College portal auth controller: `app/Http/Controllers/TenantAuthController.php`
- Tenancy config: `config/tenancy.php`
- Central routes: `routes/central.php`
- Tenant routes: `routes/tenant.php`
