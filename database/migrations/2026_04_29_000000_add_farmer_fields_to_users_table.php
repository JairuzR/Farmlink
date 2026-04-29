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
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('farm_name')->nullable()->after('address');
            $table->string('farmer_id_path')->nullable()->after('farm_name');
            $table->boolean('is_approved')->default(true)->after('farmer_id_path')->comment('Farmers require approval, users are approved by default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'farm_name', 'farmer_id_path', 'is_approved']);
        });
    }
};
