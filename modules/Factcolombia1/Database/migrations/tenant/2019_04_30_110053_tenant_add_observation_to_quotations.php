<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TenantAddObservationToQuotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('quotations', function(Blueprint $table) {
            $table->string('observation')->nullable()->after('currency_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('quotations', function(Blueprint $table) {
            $table->dropColumn('observation');
        });
    }
}
