<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddResponseNoteToServiceCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_companies', function (Blueprint $table) {
            $table->text('response_resolution_credit')->nullable();
            $table->text('response_resolution_debit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_companies', function (Blueprint $table) {
            $table->dropColumn(['response_resolution_credit', 'response_resolution_debit']);
        });
    }
}
