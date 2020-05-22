<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsApiResponseToServiceCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_companies', function (Blueprint $table) {
            $table->string('message', 100);
            $table->text('password');
            $table->text('api_token');

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
             $table->dropColumn(['message', 'password', 'api_token']);
        });
    }
}
