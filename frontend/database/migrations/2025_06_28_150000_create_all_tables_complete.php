<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // USERS TABLE
        Schema::create('users_table', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 100)->unique();
            $table->text('password_hash');
            $table->string('phone', 20)->nullable();
            $table->enum('role', ['user', 'hospital_admin'])->default('user');
            $table->text('profile_picture')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // ADMINS TABLE
        Schema::create('admins_table', function (Blueprint $table) {
            $table->increments('admin_id');
            $table->string('username', 100)->unique();
            $table->string('email', 100)->unique();
            $table->text('password_hash');
            $table->text('profile_picture')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // HOSPITALS TABLE
        Schema::create('hospitals', function (Blueprint $table) {
            $table->increments('hospital_id');
            $table->string('name', 100);
            $table->string('province', 100)->nullable();
            $table->text('location');
            $table->text('contact_info')->nullable();
            $table->text('information')->nullable();
            $table->text('profile_picture')->nullable();
            $table->integer('owner_id')->unsigned()->unique()->nullable();
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('owner_id')->references('user_id')->on('users_table')->onDelete('cascade');
        });

        // SUBSCRIPTIONS TABLE
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('subscription_id');
            $table->integer('hospital_id')->unsigned();
            $table->enum('plan_type', ['basic', 'premium', 'enterprise'])->default('basic');
            $table->decimal('price', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('hospital_id')->references('hospital_id')->on('hospitals')->onDelete('cascade');
        });

        // APPOINTMENTS TABLE
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('appointment_id');
            $table->integer('user_id')->unsigned();
            $table->integer('hospital_id')->unsigned();
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->string('patient_phone', 20)->nullable();
            $table->text('symptom')->nullable();
            $table->enum('status', ['upcoming', 'completed','no_show'])->default('upcoming');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('user_id')->references('user_id')->on('users_table')->onDelete('cascade');
            $table->foreign('hospital_id')->references('hospital_id')->on('hospitals')->onDelete('cascade');
        });

        // TOKENS TABLE
        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('token_id');
            $table->integer('user_id')->unsigned();
            $table->text('access_token');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('user_id')->on('users_table')->onDelete('cascade');
        });

        // FEEDBACK TABLE
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('feedback_id');
            $table->integer('user_id')->unsigned();
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('user_id')->references('user_id')->on('users_table')->onDelete('cascade');
        });

        // SESSIONS TABLE
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('tokens');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('hospitals');
        Schema::dropIfExists('admins_table');
        Schema::dropIfExists('users_table');
    }
}; 