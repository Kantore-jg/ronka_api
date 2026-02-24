<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_assignments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'declined'])->default('pending')->after('member_id');
            $table->timestamp('confirmed_at')->nullable()->after('status');
            $table->boolean('notified')->default(false)->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('event_assignments', function (Blueprint $table) {
            $table->dropColumn(['status', 'confirmed_at', 'notified']);
        });
    }
};
