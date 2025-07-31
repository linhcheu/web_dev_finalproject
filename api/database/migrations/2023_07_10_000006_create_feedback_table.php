<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_table', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('hospital_id');
            $table->string('name', 100)->nullable(); // For guest feedback
            $table->string('email', 100)->nullable(); // For guest feedback
            $table->integer('rating')->default(5);
            $table->text('comment');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('users_table')->onDelete('set null');
            $table->foreign('hospital_id')->references('hospital_id')->on('hospitals_table')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback_table');
    }
};
