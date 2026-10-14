<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('events')->insert([
            'title' => 'Hindia',
            'category' => 'Musik',
            'ticket_price' => '250000',
            'description' => 'Tiket Konser Hindia - Jakarta',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]); 
        DB::table('events')->insert([
            'title' => 'Bootcamp Coding',
            'category' => 'Seminar',
            'ticket_price' => '80000',
            'description' => 'BootCamp Coding Online',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]); 
        DB::table('events')->insert([
            'title' => 'Fiersa Besari',
            'category' => 'Musik',
            'ticket_price' => '230000',
            'description' => 'Tiket Konser Fiersa Besari - Bandung',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]); 
        DB::table('events')->insert([
            'title' => 'Pasar Malem',
            'category' => 'Festival',
            'ticket_price' => '20000',
            'description' => 'Pasar Malem Di Jelupang',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]); 
        DB::table('events')->insert([
            'title' => 'Feast',
            'category' => 'Musik',
            'ticket_price' => '260000',
            'description' => 'Tiket Konser Feast - Jakarta',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]); 
    }
}
