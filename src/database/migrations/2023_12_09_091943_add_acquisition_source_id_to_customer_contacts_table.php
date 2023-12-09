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
        Schema::table('customer_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('acquisition_source_id')->nullable()->after('customer_id');
            $table->foreign('acquisition_source_id')->references('id')->on('acquisition_sources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_contacts', function (Blueprint $table) {
            $table->dropForeign(['acquisition_source_id']);
            $table->dropColumn('acquisition_source_id');
        });
    }
};
