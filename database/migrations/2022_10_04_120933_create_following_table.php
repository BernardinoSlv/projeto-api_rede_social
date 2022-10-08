<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('following', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id_from");
            $table->unsignedBigInteger("user_id_to");
            $table->timestamps();

            $table->foreign("user_id_from")->references("id")->on("user")->cascadeOnDelete();
            $table->foreign("user_id_to")->references("id")->on("user")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('following');
    }
};
