<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * รัน: php artisan migrate:fresh --seed
     * ลำดับสำคัญ: ตารางแม่ก่อนตารางลูก (User/Category/Organizer → Activity → การสมัคร)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            OrganizerSeeder::class,
        ]);
    }
}
