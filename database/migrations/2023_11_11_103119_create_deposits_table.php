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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('name');
            $table->string('assistant_person');
            $table->string('mobile')->nullable(); 
            $table->date('event_date');
            $table->string('event_time')->nullable();
            $table->string('venue')->nullable();
            $table->string('numberOfPax')->nullable();
            $table->string('expPax')->nullable();           
            $table->date('payment_date');
            $table->enum('currency', ['USD', 'MMK']);
            $table->enum('payment_type', ['Deposit', 'Final']);
            $table->float('amount',12);  
            $table->string('note')->nullable();  
            $table->foreignId('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
