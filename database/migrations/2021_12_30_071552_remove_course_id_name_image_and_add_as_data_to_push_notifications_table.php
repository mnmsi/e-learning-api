<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCourseIdNameImageAndAddAsDataToPushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropColumn('course_id');
            $table->dropColumn('course_name');
            $table->dropColumn('image');
            $table->string('data', 2000)->nullable()->after('tutor_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->string('course_id')->nullable();
            $table->string('course_name')->nullable();
            $table->string('image')->nullable();
            $table->dropColumn('data');
        });
    }
}
