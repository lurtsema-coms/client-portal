<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->default('admin');
            $table->string('client_type')->nullable();
            $table->string('email')->unique();
            $table->string('company_cell_number')->nullable();
            $table->text('company_address')->nullable();
            $table->string('project_manager')->nullable();
            $table->string('img_path')->nullable();
            $table->string('url_sharepoint')->nullable();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('created_by')->length(10)->nullable();
            $table->unsignedBigInteger('updated_by')->length(10)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
