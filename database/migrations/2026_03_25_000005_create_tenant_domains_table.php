<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection($this->connection)->create('tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('host')->unique();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $baseDomain = (string) config('tenancy.base_domain', 'buksu.test');
        $now = now();
        $tenants = DB::connection($this->connection)
            ->table('tenants')
            ->select(['id', 'domain', 'subdomain'])
            ->get();

        foreach ($tenants as $tenant) {
            $hosts = [];

            if (filled($tenant->domain)) {
                $hosts[] = (string) $tenant->domain;
            }

            if (filled($tenant->subdomain)) {
                $derivedHost = $tenant->subdomain.'.'.$baseDomain;

                if (! in_array($derivedHost, $hosts, true)) {
                    $hosts[] = $derivedHost;
                }
            }

            foreach ($hosts as $index => $host) {
                DB::connection($this->connection)
                    ->table('tenant_domains')
                    ->insert([
                        'tenant_id' => $tenant->id,
                        'host' => $host,
                        'is_primary' => $index === 0,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('tenant_domains');
    }
};
