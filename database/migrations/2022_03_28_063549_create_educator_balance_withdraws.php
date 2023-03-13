<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducatorBalanceWithdraws extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educator_balance_withdraws', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('educator_id');
            $table->unsignedBigInteger('bank_account_id');
            $table->decimal("amount", 8, 2, true);
            $table->boolean("status")->default(false)->comment("0 = Pending, 1 = Approved, 2 = Rejected");
            $table->string("notes")->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('educator_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('bank_account_id')->references('id')->on('educator_bank_accounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('educator_balance_widthdraws');
    }
}
