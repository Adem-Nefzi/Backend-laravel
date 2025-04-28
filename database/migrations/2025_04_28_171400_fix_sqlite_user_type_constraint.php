<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For SQLite only - disable foreign key constraints temporarily
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        }

        // 1. Create temporary table
        Schema::create('users_temp', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('user_type'); // Changed to string without enum
            $table->text('bio')->nullable();
            $table->string('profile_picture')->nullable();
            $table->timestamps();
        });

        // 2. Copy data with transformed user_type
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            $data = $users->map(function ($user) {
                return [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'user_type' => $this->transformUserType($user->user_type),
                    'bio' => $user->bio,
                    'profile_picture' => $user->profile_picture,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ];
            })->toArray();

            DB::table('users_temp')->insert($data);
        });

        // 3. Swap tables
        Schema::drop('users');
        Schema::rename('users_temp', 'users');

        // 4. Recreate indexes and constraints
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        }
    }

    protected function transformUserType($type)
    {
        return match ($type) {
            'individual' => 'donor',
            'association' => 'recipient',
            default => $type // keeps admin and any other values
        };
    }

    public function down()
    {
        // Similar logic for rollback if needed
    }
};
