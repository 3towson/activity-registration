<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ตารางกลาง (pivot) ของการสมัครกิจกรรม: Activity N–N User
     */
    public function up(): void
    {
        Schema::create('activity_user', function (Blueprint $table) {
            // ลบกิจกรรมหรือผู้ใช้ → การสมัครที่เกี่ยวข้องถูกลบตาม
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps(); // created_at = เวลาที่สมัคร

            // composite primary key → สมัครกิจกรรมเดิมซ้ำไม่ได้ในระดับฐานข้อมูล
            $table->primary(['activity_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_user');
    }
};
