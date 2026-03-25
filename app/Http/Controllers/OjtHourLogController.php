<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithTenantRouting;
use App\Models\OjtHourLog;
use App\Models\Student;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OjtHourLogController extends Controller
{
    use InteractsWithTenantRouting;

    public function store(Request $request, CurrentTenant $currentTenant): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:tenant.students,id'],
            'log_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0.5', 'max:24'],
            'activity' => ['required', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
        ]);

        $hourLog = OjtHourLog::query()->create($data + [
            'approved_at' => $data['status'] === 'approved' ? now() : null,
        ]);

        if ($hourLog->status === 'approved') {
            Student::query()
                ->whereKey($hourLog->student_id)
                ->update([
                    'completed_hours' => DB::raw('completed_hours + '.((float) $hourLog->hours)),
                ]);
        }

        return $this->redirectToTenantRoute(
            $request,
            $tenant,
            'admin.dashboard',
            ['section' => 'hours'],
            'OJT hour log recorded.'
        );
    }

    public function update(Request $request, CurrentTenant $currentTenant, OjtHourLog $hour): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $previousApprovedHours = $hour->status === 'approved' ? (float) $hour->hours : 0.0;

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:tenant.students,id'],
            'log_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0.5', 'max:24'],
            'activity' => ['required', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
        ]);

        $hour->update($data + [
            'approved_at' => $data['status'] === 'approved' ? now() : null,
        ]);

        $currentApprovedHours = $hour->status === 'approved' ? (float) $hour->hours : 0.0;
        $difference = $currentApprovedHours - $previousApprovedHours;

        if ($difference !== 0.0) {
            Student::query()
                ->whereKey($hour->student_id)
                ->update([
                    'completed_hours' => DB::raw('GREATEST(completed_hours + '.((float) $difference).', 0)'),
                ]);
        }

        return $this->redirectToTenantRoute(
            $request,
            $tenant,
            'admin.dashboard',
            ['section' => 'hours'],
            'OJT hour log updated.'
        );
    }
}
