<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBccColumnEmailLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('email_log', function ($table) {
            if (! Schema::hasColumn('email_log', 'bcc')) {
                $table->string('to')->nullable()->change();
                $table->string('bcc')->after('to')->nullable();
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
            $table->string('to')->change();
            $table->dropColumn('bcc');
        });
    }
}
