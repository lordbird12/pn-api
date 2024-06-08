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

            $table->string('user_no', 50)->charset('utf8')->nullable();

            $table->date('date')->nullable();

            $table->string('time', 50)->charset('utf8')->nullable();

            $table->enum('time_status', ['In', 'Out'])->charset('utf8')->default('In');

            $table->integer('area_id')->nullable();

            $table->string('location', 50)->charset('utf8')->nullable();

            $table->string('ot', 10)->charset('utf8')->nullable();

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
