<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('log_documents', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents');
            $table->unsignedInteger('state_document_id');
            $table->foreign('state_document_id')->references('id')->on('state_documents');
            $table->longText('payload');
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
        Schema::dropIfExists('log_documents');
    }
}
