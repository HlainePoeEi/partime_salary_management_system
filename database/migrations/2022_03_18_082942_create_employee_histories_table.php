<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_histories', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('employee_name');
            $table->string('employee_email');
            $table->string('employee_nrc_number')->nullable();
            $table->string('aggregate')->nullable();
            $table->string('pre_training_hours')->nullable();
            $table->string('meeting_attendance')->nullable();
            $table->string('leader_allowance')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('cross_check')->nullable();
            $table->string('correction_work_time')->nullable();
            $table->string('basic_hourly_wage')->nullable();
            $table->string('incentives')->nullable();
            $table->string('payment_amount_with_yen')->nullable();
            $table->string('usd_rate')->nullable();
            $table->string('yarn_rate')->nullable();
            $table->string('mmk_rate')->nullable();
            $table->string('total_payment')->nullable();
            $table->string('pay_month')->nullable();
            $table->string('add_info')->nullable();
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
        Schema::dropIfExists('employee_histories');
    }
}
