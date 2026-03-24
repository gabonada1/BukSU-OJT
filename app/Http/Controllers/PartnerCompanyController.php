<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithTenantRouting;
use App\Models\PartnerCompany;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PartnerCompanyController extends Controller
{
    use InteractsWithTenantRouting;

    public function store(Request $request, CurrentTenant $currentTenant): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        PartnerCompany::query()->create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'available_positions' => ['nullable', 'string', 'max:2500'],
            'required_documents' => ['nullable', 'string', 'max:2500'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'intern_slot_limit' => ['required', 'integer', 'min:1'],
        ]) + [
            'is_active' => true,
        ]);

        return $this->redirectToTenantRoute(
            $request,
            $tenant,
            'admin.dashboard',
            ['section' => 'companies'],
            'Partner company added.'
        );
    }

    public function update(Request $request, CurrentTenant $currentTenant, PartnerCompany $company): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $company->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'available_positions' => ['nullable', 'string', 'max:2500'],
            'required_documents' => ['nullable', 'string', 'max:2500'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'intern_slot_limit' => ['required', 'integer', 'min:1'],
        ]));

        return $this->redirectToTenantRoute(
            $request,
            $tenant,
            'admin.dashboard',
            ['section' => 'companies'],
            'Partner company updated.'
        );
    }
}
