<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('url_sharepoint')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        $tableName = 'users';
        $columnName = 'url_sharepoint';

        DB::statement("UPDATE `$tableName` SET `$columnName` = LEFT(`$columnName`, 255)");

        Schema::table('users', function (Blueprint $table) {
            $table->string('url_sharepoint')->nullable()->change();
        });
    }
};
