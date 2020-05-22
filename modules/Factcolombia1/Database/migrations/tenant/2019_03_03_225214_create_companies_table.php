<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('companies', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_identity_document_id')->nullable();
            $table->foreign('type_identity_document_id')->references('id')->on('type_identity_documents');
            $table->string('identification_number')->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('email');
            $table->string('subdomain', 10)->unique();
            $table->unsignedInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->unsignedInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->unsignedInteger('type_regime_id')->nullable();
            $table->foreign('type_regime_id')->references('id')->on('type_regimes');
            $table->unsignedInteger('type_obligation_id')->nullable();
            $table->foreign('type_obligation_id')->references('id')->on('type_obligations');
            $table->unsignedInteger('version_ubl_id')->nullable();
            $table->foreign('version_ubl_id')->references('id')->on('version_ubls');
            $table->unsignedInteger('ambient_id')->nullable();
            $table->foreign('ambient_id')->references('id')->on('ambients');
            $table->string('software_identifier')->nullable();
            $table->string('software_password')->nullable();
            $table->string('pin')->nullable();
            $table->string('certificate_name')->nullable();
            $table->string('certificate_password')->nullable();
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
        Schema::dropIfExists('companies');
    }
}
