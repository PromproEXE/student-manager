<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absents', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('user_name');
            $table->string('user_class');
            $table->string('user_room');
            $table->string('type');
            $table->timestamp('from')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('to')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('details');
            $table->boolean('approve')->nullable();
            $table->string('approve_by')->nullable();
            $table->timestamp('approve_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by');
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
        Schema::dropIfExists('absents');
    }
};
