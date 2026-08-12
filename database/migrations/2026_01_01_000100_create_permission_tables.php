<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Matches spatie/laravel-permission's expected schema, but swaps the morph
 * key to a ULID string so it matches our User primary key type. After
 * publishing the package config, set in config/permission.php:
 *   'column_names' => ['model_morph_key' => 'model_id'],
 * and 'model_key' behavior is handled automatically since we use ulidMorphs.
 */
return new class extends Migration
{
    public function up(): void
    {
        $teams = config('permission.teams', false);

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) use ($teams) {
            $table->id();
            if ($teams) {
                $table->unsignedBigInteger('team_id')->nullable();
                $table->index('team_id', 'roles_team_foreign_key_index');
            }
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $teams
                ? $table->unique(['team_id', 'name', 'guard_name'])
                : $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) use ($teams) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->ulid('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->cascadeOnDelete();

            if ($teams) {
                $table->unsignedBigInteger('team_id');
                $table->index('team_id', 'model_has_permissions_team_foreign_key_index');
                $table->primary(['team_id', 'permission_id', 'model_id', 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary(['permission_id', 'model_id', 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }
        });

        Schema::create('model_has_roles', function (Blueprint $table) use ($teams) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->ulid('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->cascadeOnDelete();

            if ($teams) {
                $table->unsignedBigInteger('team_id');
                $table->index('team_id', 'model_has_roles_team_foreign_key_index');
                $table->primary(['team_id', 'role_id', 'model_id', 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary(['role_id', 'model_id', 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->cascadeOnDelete();
            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->cascadeOnDelete();

            $table->primary(['permission_id', 'role_id']);
        });

        app('cache')->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key', 'spatie.permission.cache'));
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
