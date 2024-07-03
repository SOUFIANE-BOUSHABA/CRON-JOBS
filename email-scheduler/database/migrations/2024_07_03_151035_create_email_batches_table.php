<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_id')->constrained('mail');
            $table->integer('quantity');
            $table->integer('interval_minutes');
            $table->string('status', 50)->default('pending');
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
        Schema::dropIfExists('email_batches');
    }
}
