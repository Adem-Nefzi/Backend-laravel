<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->enum('category', ['Food', 'Clothes', 'Healthcare', 'Education', 'Home supplies'])
                ->nullable()
                ->after('description');
        });
    }

    public function down()
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
