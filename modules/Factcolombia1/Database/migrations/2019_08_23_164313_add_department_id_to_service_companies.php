<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentIdToServiceCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_companies', function (Blueprint $table) {
            //$table->unsignedInteger('limit_documents')->default(0)->after('subdomain');
            $table->unsignedBigInteger('department_id')->nullable()->after('country_id');
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
              $table->dropColumn('department_id');
        });
    }
}
