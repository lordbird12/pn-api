<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_c_s', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('brand_model_id')->unsigned()->index();
            $table->foreign('brand_model_id')->references('id')->on('brand_models')->onDelete('cascade');

            $table->string('name', 250)->charset('utf8')->nullable();

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
        Schema::dropIfExists('c_c_s');
    }
}
