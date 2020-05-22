<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddEconomicActivityCodeAndIcaRateToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('companies', function(Blueprint $table) {
            $table->string('economic_activity_code')->nullable()->after('type_regime_id');
            $table->string('ica_rate')->nullable()->after('economic_activity_code');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('companies', function(Blueprint $table) {
            $table->dropColumn(['economic_activity_code', 'ica_rate']);
        });
    }
}
