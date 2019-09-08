<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'email' => 'andi@mail.com',
                'authorization' => 'this-is-andi'
            ],
            [
                'email' => 'noya@mail.com',
                'authorization' => 'this-is-noya'
            ]
        ];


        User::insert($users);
    }
}
