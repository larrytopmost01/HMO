<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SubscriptionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('health_insurance_id')->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('transaction_id');
            $table->float('amount_paid');
            $table->timestamps();

            $table->foreign('health_insurance_id')->references('id')->on('health_insurance')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_history');
    }
}