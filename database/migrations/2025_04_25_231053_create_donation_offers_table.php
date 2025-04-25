<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationOffersTable extends Migration
{
    public function up()
    {
        Schema::create('donation_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('creator_id'); // FK to users or associations
            $table->enum('type', ['food', 'clothes', 'medicine', 'other']);
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->string('condition')->nullable(); // Only for clothes/items
            $table->date('expiry_date')->nullable(); // Only for food/medicine
            $table->string('location');
            $table->enum('status', ['active', 'fulfilled', 'expired']);
            $table->json('images_urls')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('donation_offers');
    }
}
