<?php

namespace Database\Seeders;

use App\Models\ApiCall;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiCallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApiCall::firstOrCreate(['type' => ApiCall::SINGLE]);
        ApiCall::firstOrCreate(['type' => ApiCall::BATCH]);
    }
}
