<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDateExpirationAndObservationtoToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('documents', function(Blueprint $table) {
            $table->date('date_expiration')->nullable()->after('date_issue');
            $table->string('observation')->nullable()->after('date_expiration');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('documents', function(Blueprint $table) {
            $table->dropColumn(['date_expiration', 'observation']);
        });
    }
}
