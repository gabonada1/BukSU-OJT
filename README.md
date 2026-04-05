# University Practicum

University Practicum is a multitenant Laravel practicum management system for Bukidnon State University. The central application manages university provisioning and subscriptions, while each tenant portal uses its own isolated tenant database.

## Current Direction

- Product name: `University Practicum`
- Example institution: `Bukidnon State University`
- Tenant users: one `tenant_users` table with role-based accounts
- Tenant roles: `admin`, `supervisor`, and `student`
- RBAC: enforced in the tenant portal; removed from the central superadmin area

## Tenant Example

- University portal: `Bukidnon State University - College of Technologies`
- Tenant database: `buksu_college_of_technologies`
- Tenant domain: `technology.buksu.test`

## Environment

Use this tenant database block in `.env` and `.env.example`:

```env
TENANT_CONNECTION=tenant
TENANT_DB_CONNECTION=mysql
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_DATABASE=buksu_college_of_technologies
TENANT_DB_USERNAME=root
TENANT_DB_PASSWORD=
TENANT_DOMAIN=technology.buksu.test
```

Recommended related values:

```env
APP_NAME="University Practicum"
CENTRAL_SUPERADMIN_NAME="Bukidnon State University Superadmin"
CENTRAL_SUPERADMIN_EMAIL=superadmin@buksu.test
CENTRAL_SUPERADMIN_PASSWORD=password123
CENTRAL_DB_DATABASE=buksu_central
TENANCY_DEFAULT_TENANT=technology
```

## Local Bootstrapping

1. Create `buksu_central`.
2. Create `buksu_college_of_technologies`.
3. Run `php artisan migrate`.
4. Run `php artisan db:seed`.
5. Run `npm run build`.

## Default Access

- Central superadmin: `superadmin@buksu.test` / `password123` (development only)

## Notes

- Tenant user data is consolidated by `database/migrations/tenant/2026_04_03_000017_consolidate_tenant_users_into_single_table.php`.
- Student-related records still use `student_id`, but now reference `tenant_users.id`.
- Tenant RBAC settings are stored on the tenant record and enforced in tenant-side controllers.
