<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayzenPaiementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payzen_paiements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('commande_id');
            $table->integer('order_id');
            $table->integer('trans_id');
            $table->string('trans_date');
            $table->integer('prix');
            $table->integer('statut')->nullable()->default(null);
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
        Schema::dropIfExists('payzen_paiements');
    }
}
