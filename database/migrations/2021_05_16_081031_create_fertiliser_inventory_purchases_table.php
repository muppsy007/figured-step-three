<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFertiliserInventoryPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fertiliser_inventory_purchases', function (Blueprint $table) {
            $table->id();

            $table->dateTime('date');
            $table->integer('quantity_purchased');
            $table->integer('quantity_remaining');
            $table->decimal('unit_price', 9, 2);

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
        Schema::dropIfExists('fertiliser_inventory_purchase');
    }
}
