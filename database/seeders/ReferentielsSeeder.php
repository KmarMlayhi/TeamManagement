<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferentielsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('grades')->insert([
            ['nom' => 'G1'],
            ['nom' => 'G2'],
            ['nom' => 'G3'],
            ['nom' => 'G4'],
            ['nom' => 'G5'],
        ]);

        DB::table('fonctions')->insert([
            ['nom' => 'Ingénieur web '],
            ['nom' => 'Chef de projet'],
            ['nom' => 'Ingénieur Génie civil'],
             ['nom' => 'Full stack'],
            
        ]);

        DB::table('directions')->insert([
            ['nom' => 'Recherche & Développement '],
            ['nom' => 'Pont'],
            ['nom' => 'Finance'],
        ]);
    }
}
