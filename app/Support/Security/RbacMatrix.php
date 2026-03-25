<?php

namespace App\Support\Security;

class RbacMatrix
{
    public static function definitions(): array
    {
        return [
            'tenant.create' => ['label' => 'create', 'subject' => 'tenant'],
            'tenant.read' => ['label' => 'read', 'subject' => 'tenant'],
            'tenant.update' => ['label' => 'update', 'subject' => 'tenant'],
            'tenant.activate' => ['label' => 'activate', 'subject' => 'tenant'],
            'tenant.subscription.update' => ['label' => 'update subscription', 'subject' => 'tenant'],
            'tenant.notify' => ['label' => 'send notification', 'subject' => 'tenant'],
            'user.read' => ['label' => 'read', 'subject' => 'user'],
            'user.create' => ['label' => 'create', 'subject' => 'user'],
            'user.update' => ['label' => 'update', 'subject' => 'user'],
            'user.suspend' => ['label' => 'suspend', 'subject' => 'user'],
            'user.role.assign' => ['label' => 'assign role', 'subject' => 'user'],
            'company.manage' => ['label' => 'manage', 'subject' => 'company'],
            'application.manage' => ['label' => 'manage', 'subject' => 'application'],
            'requirement.review' => ['label' => 'review', 'subject' => 'requirement'],
            'hours.review' => ['label' => 'review', 'subject' => 'hour log'],
            'report.view' => ['label' => 'view', 'subject' => 'report'],
        ];
    }

    public static function centralRoles(): array
    {
        return [
            'superadmin' => 'Superadmin',
            'tenant_admin' => 'Tenant Admin',
            'supervisor' => 'Supervisor',
            'student' => 'Student',
        ];
    }

    public static function tenantRoles(): array
    {
        return [
            'tenant_admin' => 'Tenant Admin',
            'supervisor' => 'Supervisor',
            'student' => 'Student',
        ];
    }

    public static function defaultCentralMatrix(): array
    {
        return [
            'tenant.create' => ['superadmin' => true, 'tenant_admin' => false, 'supervisor' => false, 'student' => false],
            'tenant.read' => ['superadmin' => true, 'tenant_admin' => false, 'supervisor' => false, 'student' => false],
            'tenant.update' => ['superadmin' => true, 'tenant_admin' => false, 'supervisor' => false, 'student' => false],
            'tenant.activate' => ['superadmin' => true, 'tenant_admin' => false, 'supervisor' => false, 'student' => false],
            'tenant.subscription.update' => ['superadmin' => true, 'tenant_admin' => false, 'supervisor' => false, 'student' => false],
            'tenant.notify' => ['superadmin' => true, 'tenant_admin' => false, 'supervisor' => false, 'student' => false],
            'user.read' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.create' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.update' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.suspend' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.role.assign' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'company.manage' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'application.manage' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => true],
            'requirement.review' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'hours.review' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'report.view' => ['superadmin' => false, 'tenant_admin' => true, 'supervisor' => true, 'student' => true],
        ];
    }

    public static function defaultTenantMatrix(): array
    {
        return [
            'user.read' => ['tenant_admin' => true, 'supervisor' => true, 'student' => false],
            'user.create' => ['tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.update' => ['tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.suspend' => ['tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'user.role.assign' => ['tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'company.manage' => ['tenant_admin' => true, 'supervisor' => false, 'student' => false],
            'application.manage' => ['tenant_admin' => true, 'supervisor' => true, 'student' => true],
            'requirement.review' => ['tenant_admin' => true, 'supervisor' => true, 'student' => false],
            'hours.review' => ['tenant_admin' => true, 'supervisor' => true, 'student' => false],
            'report.view' => ['tenant_admin' => true, 'supervisor' => true, 'student' => true],
        ];
    }

    public static function normalize(array $matrix, array $roles, array $definitions): array
    {
        $normalized = [];

        foreach (array_keys($definitions) as $permission) {
            $roleValues = [];
            $permissionValues = is_array($matrix[$permission] ?? null) ? $matrix[$permission] : [];

            foreach (array_keys($roles) as $role) {
                $roleValues[$role] = (bool) ($permissionValues[$role] ?? false);
            }

            $normalized[$permission] = $roleValues;
        }

        return $normalized;
    }
}
