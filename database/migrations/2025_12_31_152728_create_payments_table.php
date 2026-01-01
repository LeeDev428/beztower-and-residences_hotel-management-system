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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('payment_type', ['down_payment', 'full_payment', 'remaining_payment'])->default('down_payment');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'gcash', 'paymaya'])->default('gcash');
            $table->string('payment_reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('percentage', 5, 2)->nullable()->comment('30 for 30% down payment');
            $table->enum('payment_status', ['pending', 'verified', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->string('proof_of_payment')->nullable()->comment('Uploaded screenshot/image path');
            $table->text('payment_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
