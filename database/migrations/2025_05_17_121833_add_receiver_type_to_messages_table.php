<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('receiver_type')->nullable()->after('receiver_id');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
