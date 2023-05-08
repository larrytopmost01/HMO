<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthCareServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_care_services', function (Blueprint $table) {
            $table->id();
            $table->text('services');
            $table->string('service_name');
            $table->unsignedBigInteger('appointment_id')->index();
            $table->string('transaction_id')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->float('amount_paid');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('health_care_appointments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_care_services');
    }
}
