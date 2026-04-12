<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        if (Schema::connection($this->connection)->hasTable('system_releases')) {
            return;
        }

        Schema::connection($this->connection)->create('system_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version', 30)->unique();
            $table->string('github_tag', 50)->unique();
            $table->string('github_sha', 64);
            $table->string('archive_url', 500);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('published');
            $table->foreignId('created_by')->nullable()->constrained('central_superadmins')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('system_releases');
    }
};
