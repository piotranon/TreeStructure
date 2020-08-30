<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = DB::table('users')->where('email', 'admin@admin.com')->first();

        DB::table('nodes')->insert([
            'name' => 'Grupa Główna 1',
            'order' => 1,
            'owner_id' => $user->id,
        ]);

        DB::table('nodes')->insert([
            'name' => 'Grupa Główna 2',
            'order' => 2,
            'owner_id' => $user->id,
        ]);

        $group1 = DB::table('nodes')->where('name', 'Grupa Główna 1')->first();
        $group2 = DB::table('nodes')->where('name', 'Grupa Główna 2')->first();

        DB::table('nodes')->insert([
            'name' => 'Podgrupa 1 Grupy Głównej 1',
            'order' => 1,
            'parent_id' => $group1->id,
            'owner_id' => $user->id,
        ]);

        DB::table('nodes')->insert([
            'name' => 'Podgrupa 2 Grupy Głównej 1',
            'order' => 2,
            'parent_id' => $group1->id,
            'owner_id' => $user->id,
        ]);

        DB::table('nodes')->insert([
            'name' => 'Podgrupa 3 Grupy Głównej 1',
            'order' => 3,
            'parent_id' => $group1->id,
            'owner_id' => $user->id,
        ]);

        DB::table('nodes')->insert([
            'name' => 'Podgrupa 1 Grupy Głównej 2',
            'order' => 1,
            'parent_id' => $group2->id,
            'owner_id' => $user->id,
        ]);

        DB::table('nodes')->insert([
            'name' => 'Podgrupa 2 Grupy Głównej 2',
            'order' => 2,
            'parent_id' => $group2->id,
            'owner_id' => $user->id,
        ]);
    }
}
