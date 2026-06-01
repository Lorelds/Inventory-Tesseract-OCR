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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->text('raw_text')->nullable(); // OCR raw output
            $table->string('store_name')->nullable(); // extracted store name from OCR
            $table->date('transaction_date')->nullable(); // extracted date
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->enum('payment_status', ['lunas', 'hutang'])->default('hutang');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
