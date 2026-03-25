<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithTenantRouting;
use App\Models\Course;
use App\Support\Security\AuditLogger;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    use InteractsWithTenantRouting;

    public function store(Request $request, CurrentTenant $currentTenant): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('tenant.courses', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'required_ojt_hours' => ['required', 'numeric', 'min:1', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $course = Course::query()->create($validated);

        $actor = Auth::guard('tenant_admin')->user();

        if ($actor) {
            AuditLogger::log('tenant_admin', $actor->id, $actor->name, 'created', $course, null, $course->toArray(), $request);
        }

        return $this->redirectToTenantRoute($request, $tenant, 'admin.profile.show', status: 'Course added successfully.')
            ->withFragment('courses');
    }

    public function update(Request $request, CurrentTenant $currentTenant, Course $course): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('tenant.courses', 'code')->ignore($course->getKey())],
            'name' => ['required', 'string', 'max:255'],
            'required_ojt_hours' => ['required', 'numeric', 'min:1', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $oldValues = $course->toArray();
        $oldHours = number_format((float) $oldValues['required_ojt_hours'], 2, '.', '');
        $newHours = number_format((float) $validated['required_ojt_hours'], 2, '.', '');

        $course->update($validated);

        if ($oldHours !== $newHours) {
            $course->students()
                ->where('required_hours', $oldHours)
                ->update(['required_hours' => $newHours]);
        }

        $actor = Auth::guard('tenant_admin')->user();

        if ($actor) {
            AuditLogger::log('tenant_admin', $actor->id, $actor->name, 'updated', $course, $oldValues, $course->fresh()->toArray(), $request);
        }

        return $this->redirectToTenantRoute($request, $tenant, 'admin.profile.show', status: 'Course updated.')
            ->withFragment('courses');
    }

    public function destroy(Request $request, CurrentTenant $currentTenant, Course $course): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        if ($course->students()->exists()) {
            return $this->redirectToTenantRoute($request, $tenant, 'admin.profile.show')
                ->withErrors(['course' => "Cannot delete \"{$course->code}\" because it still has enrolled students. Deactivate it instead."])
                ->withFragment('courses');
        }

        $oldValues = $course->toArray();
        $course->delete();

        $actor = Auth::guard('tenant_admin')->user();

        if ($actor) {
            AuditLogger::log('tenant_admin', $actor->id, $actor->name, 'deleted', $course, $oldValues, null, $request);
        }

        return $this->redirectToTenantRoute($request, $tenant, 'admin.profile.show', status: 'Course removed.')
            ->withFragment('courses');
    }
}
