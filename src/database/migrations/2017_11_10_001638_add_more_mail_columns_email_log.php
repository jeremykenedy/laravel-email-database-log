<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreMailColumnsEmailLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('email_log', function ($table) {
            if (!Schema::hasColumn('email_log', 'id')) {
                $table->increments('id')->first();
                $table->string('from')->after('date')->nullable();
                $table->string('cc')->after('to')->nullable();
                $table->text('headers')->after('body')->nullable();
                $table->text('attachments')->after('headers')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('email_log', function ($table) {
            $table->dropColumn(['id', 'from', 'cc', 'headers', 'attachments']);
        });
    }
}
