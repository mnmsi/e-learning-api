<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSkippedInterestAndIsSkippedCourseCreationToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_skipped_interest')->after('is_acc_type_update')->default(false)->comment("1 = Skipped, 0 = Updated");
            $table->boolean('is_skipped_course_creation')->after('is_skipped_interest')->default(false)->comment("1 = Skipped, 0 = Updated");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_skipped_interest');
            $table->dropColumn('is_skipped_course_creation');
        });
    }
}
