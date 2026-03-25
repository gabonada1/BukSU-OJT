<?php

namespace App\Models;

use App\Models\Concerns\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'code',
        'name',
        'required_ojt_hours',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'required_ojt_hours' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getAuditLabel(): string
    {
        return "{$this->code} - {$this->name}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('code');
    }
}
