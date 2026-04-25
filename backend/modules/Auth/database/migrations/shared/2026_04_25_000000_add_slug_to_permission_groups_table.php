<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permission_groups', function (Blueprint $table) {
            $table->string('slug', 150)->nullable()->unique()->after('name');
        });

        $rows = DB::table('permission_groups')->select('id', 'name')->get();
        foreach ($rows as $row) {
            $base = Str::slug((string) $row->name) ?: 'group';
            $slug = $base;
            $i = 1;
            while (DB::table('permission_groups')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('permission_groups')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('permission_groups', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
