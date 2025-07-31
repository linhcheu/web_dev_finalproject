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
        // First check if the table exists
        if (Schema::hasTable('feedback')) {
            // Drop the existing table and recreate with new structure
            Schema::table('feedback', function (Blueprint $table) {
                // Add the missing columns
                if (!Schema::hasColumn('feedback', 'hospital_id')) {
                    $table->integer('hospital_id')->unsigned()->after('user_id');
                    $table->foreign('hospital_id')->references('hospital_id')->on('hospitals')->onDelete('cascade');
                }
                
                if (!Schema::hasColumn('feedback', 'rating')) {
                    $table->integer('rating')->default(5)->after('hospital_id');
                }
                
                if (!Schema::hasColumn('feedback', 'is_public')) {
                    $table->boolean('is_public')->default(false)->after('comments');
                }
                
                if (!Schema::hasColumn('feedback', 'name')) {
                    $table->string('name', 100)->nullable()->after('is_public');
                }
                
                if (!Schema::hasColumn('feedback', 'email')) {
                    $table->string('email', 100)->nullable()->after('name');
                }
            });
        } else {
            // Create a new feedback table with all required columns
            Schema::create('feedback', function (Blueprint $table) {
                $table->increments('feedback_id');
                $table->integer('user_id')->unsigned()->nullable();
                $table->integer('hospital_id')->unsigned();
                $table->integer('rating')->default(5);
                $table->text('comments')->nullable();
                $table->boolean('is_public')->default(false);
                $table->string('name', 100)->nullable();
                $table->string('email', 100)->nullable();
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('user_id')->references('user_id')->on('users_table')->onDelete('set null');
                $table->foreign('hospital_id')->references('hospital_id')->on('hospitals')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If we created the table, drop it
        if (!Schema::hasTable('feedback_table')) {
            Schema::dropIfExists('feedback');
        } else {
            // Otherwise just remove the columns we added
            Schema::table('feedback', function (Blueprint $table) {
                $table->dropColumn([
                    'hospital_id',
                    'rating',
                    'is_public',
                    'name',
                    'email'
                ]);
            });
        }
    }
};
