<?php

namespace Database\Seeders;

use App\Models\ApprovalChain;
use App\Models\Department;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $finance = Department::create(['name' => 'Finance']);

        // Create users
        $john = User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $mike = User::create(['name' => 'Mike', 'email' => 'mike@example.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $jane = User::create(['name' => 'Jane', 'email' => 'jane@example.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $dave = User::create(['name' => 'Dave', 'email' => 'dave@example.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $bob = User::create(['name' => 'Bob', 'email' => 'bob@example.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);

        // Create approval chain records
        $head = ApprovalChain::create([
            'department_id' => $finance->id,
            'user_id' => $john->id,
        ]);
        $ceo = ApprovalChain::create([
            'department_id' => $finance->id,
            'user_id' => $mike->id,
        ]);
        $ga_jane = ApprovalChain::create([
            'department_id' => $finance->id,
            'user_id' => $jane->id,
        ]);
        $ga_dave = ApprovalChain::create([
            'department_id' => $finance->id,
            'user_id' => $dave->id,
        ]);

        // Set hierarchical paths (using IDs)
        $head->path = "{$finance->id}.{$head->id}";
        $head->save();

        $ceo->path = "{$finance->id}.{$head->id}.{$ceo->id}";
        $ceo->save();

        // Both Jane and Dave are at the same level (both need to approve)
        $ga_jane->path = "{$finance->id}.{$head->id}.{$ceo->id}.{$ga_jane->id}";
        $ga_jane->save();

        $ga_dave->path = "{$finance->id}.{$head->id}.{$ceo->id}.{$ga_dave->id}";
        $ga_dave->save();
    }
}
