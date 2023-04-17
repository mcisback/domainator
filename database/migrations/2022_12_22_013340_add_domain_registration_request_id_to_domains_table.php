<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainRegistrationRequestIdToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {

            $table->unsignedBigInteger('domain_registration_request_id');
            $table->foreign('domain_registration_request_id')
                ->references('id')
                ->on('domain_registration_requests')
                ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropConstrainedForeignId('domain_registration_request_id');
        });
    }
}
