<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdFromAssociationsTable extends Migration
{
    public function up()
    {
        // First remove the foreign key constraint
        Schema::table('associations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Then remove the column
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }

    public function down()
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }
}
