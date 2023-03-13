<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('educator_id');
            $table->unsignedBigInteger('topic_id');
            $table->enum('privacy', ['private', 'public']);
            $table->enum('subscription_type', ['free', 'paid']);
            $table->unsignedDecimal('amount', 8, 2)->nullable();
            $table->string('project_instructions', 1000);
            $table->boolean('is_for_kid')->default(false)->comment('Below 16');
            $table->string('name', 150);
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->date('publish_date');
            $table->string('image')->nullable();
            $table->string('invitation_link');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('educator_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('topic_id')->references('id')->on('topics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
