<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 125);
            $table->string('guard_name', 125)->default('api');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('permission_group_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_group_id');
            $table->unsignedBigInteger('permission_id');

            $table->foreign('permission_group_id')
                ->references('id')
                ->on('permission_groups')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on(config('permission.table_names.permissions', 'permissions'))
                ->onDelete('cascade');

            $table->primary(['permission_group_id', 'permission_id'], 'permission_group_has_permissions_primary');
        });

        Schema::create('role_has_permission_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_group_id');

            $table->foreign('role_id')
                ->references('id')
                ->on(config('permission.table_names.roles', 'roles'))
                ->onDelete('cascade');

            $table->foreign('permission_group_id')
                ->references('id')
                ->on('permission_groups')
                ->onDelete('cascade');

            $table->primary(['role_id', 'permission_group_id'], 'role_has_permission_groups_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permission_groups');
        Schema::dropIfExists('permission_group_has_permissions');
        Schema::dropIfExists('permission_groups');
    }
};
