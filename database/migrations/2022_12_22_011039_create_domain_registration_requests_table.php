<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainRegistrationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_registration_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('submitted_by_user_id');
            $table->foreign('submitted_by_user_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->foreign('approved_by_user_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('sedo_account_id')->nullable();
            $table->foreign('sedo_account_id')
                ->references('id')
                ->on('sedo_accounts');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('verified_on_sedo_at')->nullable();

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
        Schema::dropIfExists('domain_registration_requests');
    }
}
