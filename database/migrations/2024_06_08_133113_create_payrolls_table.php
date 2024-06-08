<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->increments('id');

            $table->double('total_income', 10, 2)->default(0.00);
            $table->double('total_deduct', 10, 2)->default(0.00);
            $table->double('total_ot', 10, 2)->default(0.00);
            $table->double('total_late_deduct', 10, 2)->default(0.00);
            $table->double('salary', 10, 2)->default(0.00);
            $table->double('total_summary', 10, 2)->default(0.00);

            $table->string('month', 2)->nullable();
            $table->string('year', 4)->nullable();

            $table->boolean('status')->default(0);
            $table->string('create_by', 100)->nullable();
            $table->string('update_by', 100)->nullable();
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
        Schema::dropIfExists('payrolls');
    }
}
