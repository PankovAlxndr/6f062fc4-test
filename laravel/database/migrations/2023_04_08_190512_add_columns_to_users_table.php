<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->string('description')->nullable()->after('avatar');
            $table->string('telegram_login')->unique()->after('description');
            $table->unsignedBigInteger('telegram_id')->unique()->after('telegram_login');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'description', 'telegram_login', 'telegram_id']);
        });
    }
};
