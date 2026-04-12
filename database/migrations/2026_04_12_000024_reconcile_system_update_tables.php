<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        $schema = Schema::connection($this->connection);

        if ($schema->hasTable('system_releases')) {
            if (! $schema->hasColumn('system_releases', 'github_sha')) {
                $schema->table('system_releases', function (Blueprint $table) {
                    $table->string('github_sha', 64)->nullable()->after('github_tag');
                });
            }

            if (! $schema->hasColumn('system_releases', 'archive_url')) {
                $schema->table('system_releases', function (Blueprint $table) {
                    $table->string('archive_url', 500)->nullable()->after('github_sha');
                });
            }

            if (! $schema->hasColumn('system_releases', 'notes')) {
                $schema->table('system_releases', function (Blueprint $table) {
                    $table->text('notes')->nullable()->after('archive_url');
                });
            }

            if (! $schema->hasColumn('system_releases', 'status')) {
                $schema->table('system_releases', function (Blueprint $table) {
                    $table->string('status', 20)->default('published')->after('notes');
                });
            }

            if (! $schema->hasColumn('system_releases', 'created_by')) {
                $schema->table('system_releases', function (Blueprint $table) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('status');
                });
            }

            if (! $schema->hasColumn('system_releases', 'published_at')) {
                $schema->table('system_releases', function (Blueprint $table) {
                    $table->timestamp('published_at')->nullable()->after('created_by');
                });
            }

            if (! $schema->hasColumn('system_releases', 'created_at') || ! $schema->hasColumn('system_releases', 'updated_at')) {
                $schema->table('system_releases', function (Blueprint $table) use ($schema) {
                    if (! $schema->hasColumn('system_releases', 'created_at')) {
                        $table->timestamp('created_at')->nullable();
                    }

                    if (! $schema->hasColumn('system_releases', 'updated_at')) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }

        if ($schema->hasTable('system_updates')) {
            if (! $schema->hasColumn('system_updates', 'options')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->json('options')->nullable()->after('status');
                });
            }

            if (! $schema->hasColumn('system_updates', 'logs')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->json('logs')->nullable()->after('options');
                });
            }

            if (! $schema->hasColumn('system_updates', 'backup_path')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->string('backup_path', 500)->nullable()->after('logs');
                });
            }

            if (! $schema->hasColumn('system_updates', 'error_message')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->text('error_message')->nullable()->after('backup_path');
                });
            }

            if (! $schema->hasColumn('system_updates', 'started_at')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->timestamp('started_at')->nullable()->after('error_message');
                });
            }

            if (! $schema->hasColumn('system_updates', 'finished_at')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->timestamp('finished_at')->nullable()->after('started_at');
                });
            }

            if (! $schema->hasColumn('system_updates', 'triggered_by')) {
                $schema->table('system_updates', function (Blueprint $table) {
                    $table->unsignedBigInteger('triggered_by')->nullable()->after('finished_at');
                });
            }

            if (! $schema->hasColumn('system_updates', 'created_at') || ! $schema->hasColumn('system_updates', 'updated_at')) {
                $schema->table('system_updates', function (Blueprint $table) use ($schema) {
                    if (! $schema->hasColumn('system_updates', 'created_at')) {
                        $table->timestamp('created_at')->nullable();
                    }

                    if (! $schema->hasColumn('system_updates', 'updated_at')) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Intentionally left blank: this migration reconciles existing production schemas.
    }
};
