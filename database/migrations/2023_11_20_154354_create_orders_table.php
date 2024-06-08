<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->string('code', 255)->charset('utf8');
            $table->date('date');

            $table->integer('finance_id')->nullable();

            $table->integer('client_id')->unsigned()->index();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->integer('total_price')->default(0);
            $table->integer('down_payment')->default(0);
            $table->integer('interest')->default(0);
            $table->integer('payment_period')->default(0);

            $table->enum('sale_type', ['Installment', 'Cash'])->charset('utf8')->default('Cash');

            $table->enum('status', ['Ordered', 'Finish'])->charset('utf8')->default('Ordered');

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
        Schema::dropIfExists('orders');
    }
}
