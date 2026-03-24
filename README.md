<<<<<<< HEAD
# BukSU-OJT
=======
# BukSU Internship / Practicum Placement System

This project is now scoped around a centralized BukSU practicum platform with a separate database for each tenant. The first active tenant is the **College of Technologies**.

## Central Application vs Tenant Application

This codebase now separates the app into two clear layers:

- **Central application**: runs with no tenant context and uses the central database for tenant management, launch links, plans, and central monitoring.
- **Tenant application**: runs in tenant context and uses the tenant database for role-based login, practicum records, dashboards, requirements, and OJT workflows.

## Superadmin and Tenant Provisioning

The central application now includes a **superadmin** account that can:

- Sign in to the central control layer
- Create additional tenants
- Generate each tenant's **subdomain**
- Create the tenant database
- Run tenant migrations automatically
- Create the first tenant admin account during provisioning

Route files:

- [routes/central.php](c:/Users/RYZEN/Desktop/buksu-practicum/BukSU/buksu-practicum/routes/central.php) for central application routes
- [routes/tenant.php](c:/Users/RYZEN/Desktop/buksu-practicum/BukSU/buksu-practicum/routes/tenant.php) for tenant application routes
- [routes/web.php](c:/Users/RYZEN/Desktop/buksu-practicum/BukSU/buksu-practicum/routes/web.php) as the top-level entry resolver

## Current Setup

- **Central app database** keeps the tenant registry, plan information, and shared platform configuration.
- **Tenant database** stores college-specific operational data such as partner companies, student applications, requirements, reports, OJT hours, and evaluations.
- **Default tenant focus** is `college-of-technologies`.
- **Tenant domains** can point directly to a tenant and skip the slug-based path.

## Tenant Model

The seeded tenant is:

- Name: `College of Technologies`
- Slug: `college-of-technologies`
- Code: `COT`
- Domain: `technology.buksu.test`
- Plan: `premium`
- Database: `buksu_college_of_technologies`

## Environment Variables

Set these values in `.env`:

```env
DB_CONNECTION=central
TENANCY_BASE_DOMAIN=buksu.test
CENTRAL_SUPERADMIN_NAME="BukSU Superadmin"
CENTRAL_SUPERADMIN_EMAIL=superadmin@buksu.test
CENTRAL_SUPERADMIN_PASSWORD=password123

CENTRAL_DB_CONNECTION=mysql
CENTRAL_DB_HOST=127.0.0.1
CENTRAL_DB_PORT=3306
CENTRAL_DB_DATABASE=buksu_central
CENTRAL_DB_USERNAME=root
CENTRAL_DB_PASSWORD=

TENANT_DB_CONNECTION=mysql
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_DATABASE=buksu_college_of_technologies
TENANT_DB_USERNAME=root
TENANT_DB_PASSWORD=
TENANT_DOMAIN=technology.buksu.test

TENANCY_DEFAULT_TENANT=college-of-technologies
CENTRAL_DOMAINS=127.0.0.1,localhost
```

## Local Bootstrapping

1. Create the central database, for example `buksu_central`.
2. Create the tenant database for College of Technologies, for example `buksu_college_of_technologies`.
3. Run the central migrations:

```bash
php artisan migrate
```

4. Seed the default tenant:

```bash
php artisan db:seed
```

5. Run the tenant migrations for the College of Technologies database:

```bash
php artisan tenants:migrate college-of-technologies
```

6. Seed tenant role accounts and starter data:

```bash
php artisan tenants:seed college-of-technologies
```

7. Build the frontend assets once for Apache/XAMPP use:

```bash
npm run build
```

8. Start Apache and MySQL in XAMPP.

Open the **central application** on:

- `http://localhost/buksu-practicum/`
- `http://localhost/buksu-practicum/central`
- `http://localhost/buksu-practicum/central/login`

Open the **tenant application** on:

- `http://localhost/buksu-practicum/tenants/college-of-technologies/login`
- `http://technology.buksu.test/login` if you configured tenant domains locally

Default seeded central superadmin:

- Superadmin: `superadmin@buksu.test` / `password123`

## Role Logins

Each tenant now has separate dashboards and role-specific permissions for:

- Admin
- Supervisor
- Student

Default seeded credentials for the College of Technologies tenant:

- Admin: `admin@technology.buksu.test` / `password123`
- Supervisor: `supervisor@technology.buksu.test` / `password123`
- Student: `student@technology.buksu.test` / `password123`

Login routes:

- Shared slug-based tenant login: `/tenants/college-of-technologies/login`
- Shared domain-based tenant login: `/login`
- Optional role-specific tenant login paths still exist for direct access when needed

## Creating Additional Tenants

1. Log in as the central superadmin.
2. Open the central dashboard.
3. Fill in the tenant name, plan, subdomain, tenant database name, and first tenant admin credentials.
4. Submit the provisioning form.
5. Add the new tenant domain to your local hosts file if you want to test the subdomain locally.

The provisioning form is intentionally simplified:

- You enter: tenant name, plan, subdomain, tenant database, admin email, and admin password.
- The system auto-generates: tenant code and tenant admin display name.
- The system uses the tenant DB host, port, username, and password defaults from `.env`.

Example:

- Subdomain: `bsit`
- Base domain: `buksu.test`
- Full tenant domain: `bsit.buksu.test`

## Next Build Steps

- Split shared users and tenant users based on your preferred authentication design.
- Build dashboards and workflows for partner companies, documents, OJT logs, and supervisor evaluations.
>>>>>>> ce4b5c6 (50%)
