<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_type')->after('sender_id')->default('App\Models\User');
        });
    }
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
