<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegistrationSeeder extends Seeder
{
    /**
     * ข้อมูลการสมัครตัวอย่าง (ตาราง pivot activity_user)
     */
    public function run(): void
    {
        $now = now();
        $users = DB::table('users')->pluck('id', 'email');
        $activities = DB::table('activities')->pluck('id', 'code');

        // activity code => รายชื่ออีเมลผู้สมัคร
        $registrations = [
            'AC001' => ['user@activity.test', 'user2@activity.test', 'user3@activity.test'],
            'AC002' => ['user2@activity.test', 'user4@activity.test'],
            'AC003' => ['user3@activity.test', 'user5@activity.test'],  // รับ 3 เหลือ 1 ที่ (ใกล้เต็ม)
            'AC004' => ['user2@activity.test', 'user3@activity.test'],  // รับ 2 → เต็มแล้ว
            'AC006' => ['user@activity.test', 'user4@activity.test', 'user5@activity.test'],
            'AC008' => ['user@activity.test', 'user2@activity.test'],   // จบไปแล้ว
            'AC010' => ['user5@activity.test'],
        ];

        $data = [];
        foreach ($registrations as $code => $emails) {
            foreach ($emails as $email) {
                $data[] = [
                    'activity_id' => $activities[$code],
                    'user_id' => $users[$email],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('activity_user')->insert($data);
    }
}
