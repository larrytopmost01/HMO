<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrolleeRequestCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollee_request_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();

            /** this is neccessary because an enrollee can request 
             * for card multiple times and make payments 
             * it is important for this table to know if an enrollee
             * has made payment.
             * */
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
            $table->string('enrollee_id')->index();
            $table->boolean('card_collected')->default(false);
            $table->string('status')->default('pending');
            $table->string('passport_url')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enrollee_request_cards');
    }
}
