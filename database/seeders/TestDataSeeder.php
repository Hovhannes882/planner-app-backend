<?php

namespace Database\Seeders;

use App\Models\Aspect;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            \DB::transaction(function () {
                $userData = [
                    "username" => "hovhanensghukasian",
                    "email" => "hovhannesghukasian@gmail.com",
                    "password" => \Hash::make("asd123")
                ];

                $user = User::create($userData);
                $aspectData = [
                    "name" => "Life",
                    "user_id" => $user->__get("id"),
                ];

                $aspect = Aspect::create($aspectData);

                $taskData = [
                    "name" => "Task name",
                    "user_id" => $user->__get("id"),
                    "aspect_id" => $aspect->__get("id"),
                    "day" => \Carbon\Carbon::now()->format("Y-m-d")
                ];

                Task::create($taskData);
            });

        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
        }

    }
}
