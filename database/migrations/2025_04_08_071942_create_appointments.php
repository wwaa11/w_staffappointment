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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('hn');
            $table->string('phone');
            $table->string('remark')->nullable();
            $table->string('appoint_no');
            $table->string('appoint_date');
            $table->string('appoint_time');
            $table->string('appoint_doctor');
            $table->string('appoint_clinic');
            $table->string('appoint_status')->default('0');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
