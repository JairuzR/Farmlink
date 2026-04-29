<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['farmer', 'buyer', 'admin'])->default('buyer')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->text('address')->nullable()->after('phone');
            $table->string('farm_name')->nullable()->after('address');
            $table->string('farmer_id_path')->nullable()->after('farm_name');
            $table->boolean('is_approved')->default(false)->after('farmer_id_path');
            $table->decimal('latitude', 10, 7)->nullable()->after('is_approved');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('facebook_url')->nullable()->after('longitude');
            $table->text('bio')->nullable()->after('facebook_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'address', 'farm_name', 'farmer_id_path',
                'is_approved', 'latitude', 'longitude', 'facebook_url', 'bio',
            ]);
        });
    }
};