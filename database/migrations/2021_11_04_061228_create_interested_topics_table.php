<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestedTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interested_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learner_id');
            $table->unsignedBigInteger('topic_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('learner_id')->references('id')->on('users')->cascadeOnDelete();
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
        Schema::dropIfExists('interested_topics');
    }
}
