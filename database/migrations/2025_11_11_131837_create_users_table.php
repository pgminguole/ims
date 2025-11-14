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
               $table->string('slug',191)->nullable();
            $table->string('first_name',191);
          
            $table->string('last_name',191)->nullable();
            $table->string('username',191)->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('phone',191)->nullable()->unique();
            $table->string('email',191)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',191)->nullable();
            $table->boolean('block')->default(0);
            $table->string('status',191)->default('active');
            $table->string('access_type',191)->default('User');
            $table->boolean('is_approved')->default(0);
            $table->datetime('approved_at')->nullable();
            $table->string('require_password_reset',191)->default('no');
            $table->string('is_expire',191)->default('no');
            $table->datetime('expire_date')->nullable();
            $table->bigInteger('invited_by')->nullable();
            $table->datetime('invited_date')->nullable();
            $table->boolean('accepted')->default(0);
            $table->datetime('accepted_date')->nullable();
            $table->boolean('is_online')->default(0);
            $table->datetime('login_at')->nullable();
            $table->datetime('logout_at')->nullable();
            $table->string('employee_id',191)->unique()->nullable();
            $table->string('position',191)->nullable();
            $table->string('department',191)->nullable();
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