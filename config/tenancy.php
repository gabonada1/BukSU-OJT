<?php

return [
    'central_connection' => env('CENTRAL_CONNECTION', 'central'),
    'tenant_connection' => env('TENANT_CONNECTION', 'tenant'),
    'default_tenant_slug' => env('TENANCY_DEFAULT_TENANT', 'college-of-technologies'),
    'base_domain' => env('TENANCY_BASE_DOMAIN', 'buksu.test'),
    'local_domain_suffix' => env('TENANCY_LOCAL_DOMAIN_SUFFIX', 'localhost'),
    'central_domains' => array_filter(array_map('trim', explode(',', (string) env('CENTRAL_DOMAINS', '127.0.0.1,localhost')))),
];
