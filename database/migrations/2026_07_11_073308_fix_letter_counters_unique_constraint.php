<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letter_counters', function (Blueprint $table) {
            $table->dropUnique(['year']);
            $table->unique(['year', 'letter_type_id']);
        });
    }

    public function down(): void
    {
        Schema::table('letter_counters', function (Blueprint $table) {
            $table->dropUnique(['year', 'letter_type_id']);
            $table->unique('year');
        });
    }
};
