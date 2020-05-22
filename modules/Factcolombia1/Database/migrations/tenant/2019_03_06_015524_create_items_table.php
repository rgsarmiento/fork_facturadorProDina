<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('items', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->char('code')->unique();
            $table->unsignedInteger('type_unit_id');
            $table->foreign('type_unit_id')->references('id')->on('type_units');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('tax_id')->nullable();
            $table->foreign('tax_id')->references('id')->on('taxes');
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
        Schema::dropIfExists('items');
    }
}
