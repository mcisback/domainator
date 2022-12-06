<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedoAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sedo_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('username');
            $table->string('password');
            $table->string('partner_id');
            $table->string('signkey');
            $table->string('domain_ownership_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sedo_accounts');
    }
}
