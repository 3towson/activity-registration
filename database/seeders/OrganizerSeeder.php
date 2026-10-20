<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('organizers')->insert([
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'OG001',
                'name' => 'สโมสรนักศึกษา',
                'contact' => 'smo@activity.test, 053-000-001',
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'OG002',
                'name' => 'ชมรมอาสาพัฒนา',
                'contact' => 'volunteer@activity.test, 053-000-002',
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'OG003',
                'name' => 'ชมรมคอมพิวเตอร์',
                'contact' => 'computer@activity.test, 053-000-003',
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'code' => 'OG004',
                'name' => 'ชมรมดนตรีไทย',
                'contact' => 'thaimusic@activity.test',
            ],
        ]);
    }
}
