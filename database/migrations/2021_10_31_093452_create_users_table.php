<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acc_type_id')->nullable();
            $table->unsignedBigInteger('age_type_id')->nullable();
            $table->unsignedBigInteger('ethnicity_id')->nullable();
            $table->unsignedBigInteger('user_parent_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('child_age')->nullable();
            $table->string('social_uid')->nullable();
            $table->string('device_id')->nullable();
            $table->rememberToken();
            $table->boolean('is_acc_type_update')->default(true)->comment('0 = not updated & 1 = updated. it for social login users');
            $table->boolean('is_baned')->default(false)->comment('0 = not baned & 1 = baned');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('acc_type_id')->references('id')->on('user_acc_types');
            $table->foreign('age_type_id')->references('id')->on('user_age_types');
            $table->foreign('ethnicity_id')->references('id')->on('user_ethnicity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
