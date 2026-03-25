@php($layoutMode = 'dashboard')

@extends('layouts.central')

@section('content')
    @include('partials.rbac-matrix', [
        'title' => 'Role Permissions',
        'subtitle' => 'Superadmin control over the platform-wide permission matrix.',
        'description' => 'Control which actions each role can perform in the BukSU practicum platform. This central matrix defines the default access model.',
        'roles' => $roles,
        'definitions' => $definitions,
        'matrix' => $matrix,
        'saveAction' => $saveAction,
        'resetAction' => $resetAction,
    ])
@endsection
