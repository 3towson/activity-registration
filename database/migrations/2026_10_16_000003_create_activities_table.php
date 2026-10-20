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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->string('code', 64)->unique();
            $table->string('name', 255);

            // ลบหมวด/ผู้จัดที่ยังมีกิจกรรมอยู่ไม่ได้ (restrict)
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('organizer_id')->constrained()->restrictOnDelete();

            $table->date('date');
            $table->string('location', 255);
            $table->unsignedInteger('capacity');
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
