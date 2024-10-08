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
        Schema::create('more_infos', function (Blueprint $table) {
            $table->id();
            $table->string('client_type')->length(10);
            $table->string('label')->length(100);
            $table->string('data_type')->length(20);
            $table->smallInteger('order')->unsigned()->nullable();
            $table->smallInteger('created_by')->unsigned();
            $table->smallInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('more_infos');
    }
};
