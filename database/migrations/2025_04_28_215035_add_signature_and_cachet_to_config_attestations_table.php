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
        Schema::table('config_attestations', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('logo_path');
            $table->string('cachet_path')->nullable()->after('signature_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_attestations', function (Blueprint $table) {
            $table->dropColumn('signature_path');
            $table->dropColumn('cachet_path');
        });
    }
};
