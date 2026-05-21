<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_headers', function (Blueprint $table) {
            // Remove status column
            $table->dropColumn('status');

            // Add user column (free-text name, nullable)
            $table->string('user')->nullable()->after('note');

            // Change path from nullable to string (already is, re-affirm nullable)
            // path is already string nullable — no change needed in schema
        });
    }

    public function down(): void
    {
        Schema::table('task_headers', function (Blueprint $table) {
            $table->string('status')->default('Pending');
            $table->dropColumn('user');
        });
    }
};
