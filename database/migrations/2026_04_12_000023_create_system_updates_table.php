<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        if (Schema::connection($this->connection)->hasTable('system_updates')) {
            return;
        }

        Schema::connection($this->connection)->create('system_updates', function (Blueprint $table) {
            $table->id();
            $table->string('release_version', 30);
            $table->string('release_url', 500);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->json('options')->nullable();
            $table->json('logs')->nullable();
            $table->string('backup_path', 500)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('central_superadmins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('system_updates');
    }
};
