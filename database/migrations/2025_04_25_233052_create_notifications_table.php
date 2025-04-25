<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['application', 'approval', 'message', 'system']);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Nullable polymorphic-like references
            $table->unsignedBigInteger('related_offer_id')->nullable();
            $table->unsignedBigInteger('related_application_id')->nullable();
            $table->unsignedBigInteger('related_message_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('related_offer_id')->references('id')->on('donation_offers')->onDelete('set null');
            $table->foreign('related_application_id')->references('id')->on('applications')->onDelete('set null');
            $table->foreign('related_message_id')->references('id')->on('messages')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
