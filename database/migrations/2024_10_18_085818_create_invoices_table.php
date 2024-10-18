<?php

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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id');
            $table->string('stripe_id')->length(20)->nullable();
            $table->string('project')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('UNPAID')->length(20);
            $table->text('invoice_link')->nullable();
            $table->text('invoice_img_link')->nullable();
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
