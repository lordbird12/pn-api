<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_attendances', function (Blueprint $table) {
            $table->increments('id');

            $table->string('employeeNo', 50)->charset('utf8')->nullable();
            $table->string('groupName', 50)->charset('utf8')->nullable();

            $table->integer('absentCount')->nullable();
            $table->integer('actualWorkday')->nullable();
            $table->integer('lateCount')->nullable();
            $table->double('percenWork', 10, 2)->default(0.00);
            $table->integer('personalLeaveCount')->nullable();
            $table->integer('sickLeaveCount')->nullable();
            $table->double('sumEarly', 10, 2)->default(0.00);
            $table->double('sumLate', 10, 2)->default(0.00);
            $table->double('sumOT', 10, 2)->default(0.00);
            $table->integer('totalWorkday')->nullable();
            $table->string('name', 250)->charset('utf8')->nullable();

            $table->enum('status', ['Y', 'N'])->charset('utf8')->default('Y');

            $table->string('create_by', 100)->charset('utf8')->nullable();
            $table->string('update_by', 100)->charset('utf8')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_attendances');
    }
}
