<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
               $table->string('slug')->nullable();
            $table->string('name');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('block')->default(0);
            $table->string('status')->default('active');
            $table->string('access_type')->default('User');
            $table->boolean('is_approved')->default(0);
            $table->datetime('approved_at')->nullable();
            $table->string('require_password_reset')->default('no');
            $table->string('is_expire')->default('no');
            $table->datetime('expire_date')->nullable();
            $table->bigInteger('invited_by')->nullable();
            $table->datetime('invited_date')->nullable();
            $table->boolean('accepted')->default(0);
            $table->datetime('accepted_date')->nullable();
            $table->boolean('is_online')->default(0);
            $table->datetime('login_at')->nullable();
            $table->datetime('logout_at')->nullable();
            $table->string('employee_id')->unique()->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->foreignId('court_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('registry_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
             $table->foreignId('role_id')->nullable()->constrained('user_roles')->onDelete('set null');
             $table->boolean('is_active')->default(true);
            $table->text('signature')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
   
    }
    

    public function down()
    {
           Schema::dropIfExists('users');
    }
};