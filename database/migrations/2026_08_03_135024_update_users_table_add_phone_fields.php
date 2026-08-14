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
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable because a user can sign up with phone only
            $table->string('email')->nullable()->change();

            // Add missing columns
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('status')->default('active')->after('password');
            $table->string('avatar')->nullable()->after('status');
            $table->string('locale')->default('ar')->after('avatar');
            $table->boolean('is_super_admin')->default(false)->after('locale');
            $table->unsignedBigInteger('active_company_id')->nullable()->after('is_super_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();

            $table->dropColumn([
                'phone',
                'status',
                'avatar',
                'locale',
                'is_super_admin',
                'active_company_id'
            ]);
        });
    }
};
