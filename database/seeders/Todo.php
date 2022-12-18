<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Todo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('todo')->insert([
            'user_id' => 1,
            'category_id' => 1,
            'name' => 'Standart todo',
            'description' => 'Standart description',
            'created_at' => now()
        ]);
    }
}
