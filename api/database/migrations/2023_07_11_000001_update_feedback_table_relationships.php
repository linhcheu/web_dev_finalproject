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
        Schema::table('feedback_table', function (Blueprint $table) {
            // Add foreign key constraints if they don't exist
            if (!Schema::hasColumn('feedback_table', 'hospital_id')) {
                $table->unsignedBigInteger('hospital_id')->after('user_id');
            }
            
            // Add the foreign keys
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users_table')
                ->onDelete('set null');
                
            $table->foreign('hospital_id')
                ->references('hospital_id')
                ->on('hospitals_table')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedback_table', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['hospital_id']);
        });
    }
};
