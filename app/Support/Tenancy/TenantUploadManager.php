<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TenantUploadManager
{
    public function store(UploadedFile $file, Tenant $tenant, string $segment): string
    {
        $directory = 'uploads/tenants/'.$tenant->getRouteKey().'/'.trim($segment, '/');
        $absoluteDirectory = public_path($directory);

        File::ensureDirectoryExists($absoluteDirectory);

        $extension = $file->getClientOriginalExtension();
        $filename = (string) Str::uuid();

        if ($extension !== '') {
            $filename .= '.'.$extension;
        }

        $file->move($absoluteDirectory, $filename);

        return $directory.'/'.$filename;
    }

    public function replace(?UploadedFile $file, Tenant $tenant, string $segment, ?string $existingPath = null): ?string
    {
        if (! $file) {
            return $existingPath;
        }

        $storedPath = $this->store($file, $tenant, $segment);

        if ($existingPath) {
            $this->delete($existingPath);
        }

        return $storedPath;
    }

    public function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $absolutePath = public_path(ltrim((string) $path, '/\\'));

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
