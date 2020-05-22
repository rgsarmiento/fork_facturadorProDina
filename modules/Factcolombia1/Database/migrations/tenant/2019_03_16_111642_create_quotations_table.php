<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('quotations', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('state_quote_id')->default(1);
            $table->foreign('state_quote_id')->references('id')->on('state_quotes');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->json('client');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->decimal('sale', 10, 2);
            $table->decimal('total_discount', 10, 2);
            $table->json('taxes');
            $table->decimal('total_tax', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->unsignedInteger('version_ubl_id');
            $table->foreign('version_ubl_id')->references('id')->on('version_ubls');
            $table->unsignedInteger('ambient_id');
            $table->foreign('ambient_id')->references('id')->on('ambients');
            $table->softDeletes();
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('quotations');
    }
}
