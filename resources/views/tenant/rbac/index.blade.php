@php($layoutMode = 'dashboard')

@extends('layouts.tenant')

@section('content')
    @include('partials.rbac-matrix', [
        'title' => 'Role Permissions',
        'subtitle' => 'Tenant-level role matrix for '.$tenant->name.'.',
        'description' => 'Turn feature access on or off per role using the matrix below.',
        'roles' => $roles,
        'definitions' => $definitions,
        'matrix' => $matrix,
        'saveAction' => $saveAction,
        'resetAction' => $resetAction,
    ])
@endsection
