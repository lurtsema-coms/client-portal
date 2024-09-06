<?php

use App\Models\MoreInfo;
use App\Models\User;
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
        Schema::create('more_info_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MoreInfo::class);
            $table->foreignIdFor(User::class);
            $table->string('text_value')->nullable();
            $table->text('paragraph_value')->nullable();
            $table->date('date_value')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'more_info_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('more_info_values');
    }
};
