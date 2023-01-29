<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('webhook_id');
            $table->string('secret_key');
            $table->string('url');
            $table->json('events');
            $table->boolean('status');
            $table->timestamps();
        });
    }
};
