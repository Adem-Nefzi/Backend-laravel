<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
public function up()
{
Schema::create('donations', function (Blueprint $table) {
$table->id();
$table->unsignedBigInteger('offer_id');
$table->unsignedBigInteger('donor_id');
$table->unsignedBigInteger('recipient_id');
$table->unsignedBigInteger('application_id');
$table->date('handover_date');
$table->enum('status', ['scheduled', 'completed', 'cancelled']);
$table->text('feedback_donor')->nullable();
$table->text('feedback_recipient')->nullable();
$table->timestamps();

// Foreign Keys
$table->foreign('offer_id')->references('id')->on('donation_offers')->onDelete('cascade');
$table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
$table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
$table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
});
}

public function down()
{
Schema::dropIfExists('donations');
}
}
