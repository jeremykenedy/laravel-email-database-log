<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'email_log', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('date');
                $table->string('from')->nullable();
                $table->string('to')->nullable();
                $table->string('cc')->nullable();
                $table->string('bcc')->nullable();
                $table->string('subject');
                $table->text('body');
                $table->text('headers')->nullable();
                $table->text('attachments')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('email_log');
    }
}
