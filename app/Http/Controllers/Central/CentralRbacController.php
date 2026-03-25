<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Support\Security\RbacMatrix;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class CentralRbacController extends Controller
{
    public function index(): View
    {
        $superadmin = Auth::guard('central_superadmin')->user();
        Gate::forUser($superadmin)->authorize('manage-tenants');

        $definitions = RbacMatrix::definitions();
        $roles = RbacMatrix::centralRoles();
        $matrix = RbacMatrix::defaultCentralMatrix();
        $persistenceReady = $this->supportsPersistedSettings();

        if ($persistenceReady) {
            $storedMatrix = data_get($superadmin?->settings, 'rbac.matrix');

            if (is_array($storedMatrix) && $storedMatrix !== []) {
                $matrix = $storedMatrix;
            }
        }

        return view('central.rbac.index', [
            'pageTitle' => 'Role Permissions | '.config('app.name', 'BukSU Practicum Portal'),
            'roles' => $roles,
            'definitions' => $definitions,
            'matrix' => RbacMatrix::normalize($matrix, $roles, $definitions),
            'persistenceReady' => $persistenceReady,
            'saveAction' => route('central.rbac.update'),
            'resetAction' => route('central.rbac.reset'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $superadmin = Auth::guard('central_superadmin')->user();
        Gate::forUser($superadmin)->authorize('manage-tenants');

        if (! $this->supportsPersistedSettings()) {
            return redirect()
                ->route('central.rbac.index')
                ->withErrors([
                    'rbac' => 'RBAC settings cannot be saved yet because the `central_superadmins.settings` column is missing. Run `php artisan migrate` first.',
                ]);
        }

        $definitions = RbacMatrix::definitions();
        $roles = RbacMatrix::centralRoles();
        $matrix = RbacMatrix::normalize($request->input('permissions', []), $roles, $definitions);

        $settings = $superadmin->settings ?? [];
        $settings['rbac']['matrix'] = $matrix;
        $superadmin->forceFill(['settings' => $settings])->save();

        return redirect()->route('central.rbac.index')->with('status', 'Role permissions saved.');
    }

    public function reset(): RedirectResponse
    {
        $superadmin = Auth::guard('central_superadmin')->user();
        Gate::forUser($superadmin)->authorize('manage-tenants');

        if (! $this->supportsPersistedSettings()) {
            return redirect()
                ->route('central.rbac.index')
                ->withErrors([
                    'rbac' => 'RBAC settings cannot be reset yet because the `central_superadmins.settings` column is missing. Run `php artisan migrate` first.',
                ]);
        }

        $settings = $superadmin->settings ?? [];
        unset($settings['rbac']);
        $superadmin->forceFill(['settings' => $settings])->save();

        return redirect()->route('central.rbac.index')->with('status', 'Role permissions reset to defaults.');
    }

    protected function supportsPersistedSettings(): bool
    {
        return Schema::connection('central')->hasColumn('central_superadmins', 'settings');
    }
}
