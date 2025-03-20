<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'jkmdev@expensetracker.io')->first();

        if (!$admin) {
            User::create([
                'username' => 'jkm96dev',
                'email' => 'jkmdev@expensetracker.io',
                'password' => Hash::make('jkm@2pac'),
                'is_active' => 1,
                'is_email_verified' => 1,
                'email_verified_at' => Carbon::now(),
            ]);
        }
    }
}
