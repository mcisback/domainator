<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFtpServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftp_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('protocol');
            $table->string('host');
            $table->tinyInteger('port');
            $table->string('path')->default('/');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->text('privateKey')->nullable();
            $table->boolean('ssl')->default(false);
            $table->boolean('passive')->default(false);
            $table->boolean('usePrivateKey')->default(false);

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('ftp_servers');
    }
}
