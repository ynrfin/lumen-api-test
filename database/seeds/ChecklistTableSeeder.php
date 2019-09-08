<?php

use Illuminate\Database\Seeder;

class ChecklistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Checklist::class, 50)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 23)->make());
        });
    }
}
