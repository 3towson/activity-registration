<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('categories')->insert([
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'CT001',
                'name' => 'กีฬาและสุขภาพ',
                'description' => "กิจกรรมออกกำลังกาย การแข่งขันกีฬา\r\nและส่งเสริมสุขภาพ",
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'CT002',
                'name' => 'จิตอาสา',
                'description' => "กิจกรรมบำเพ็ญประโยชน์ต่อสังคม\r\nและชุมชน",
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'CT003',
                'name' => 'วิชาการ',
                'description' => "อบรม สัมมนา เวิร์กช็อป\r\nและการแข่งขันทางวิชาการ",
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'CT004',
                'name' => 'ศิลปวัฒนธรรม',
                'description' => 'ยังไม่มีกิจกรรมในหมวดนี้ — ใช้ทดสอบการลบหมวดหมู่ที่ว่าง',
            ],
        ]);
    }
}
