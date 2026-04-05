@php($layoutMode = 'dashboard')

@extends('layouts.tenant')

@section('content')
    @include('partials.rbac-matrix', [
        'title' => 'Role Permissions',
        'subtitle' => 'Tenant-level role matrix for '.$tenant->name.'.',
        'description' => 'Control which actions each tenant role can perform inside this university portal.',
        'roles' => $roles,
        'definitions' => $definitions,
        'matrix' => $matrix,
        'saveAction' => $saveAction,
        'resetAction' => $resetAction,
    ])
@endsection
