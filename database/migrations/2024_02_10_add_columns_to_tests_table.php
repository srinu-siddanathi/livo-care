<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->string('code')->after('name');
            $table->string('volume')->after('description')->nullable();
            $table->decimal('price', 10, 2)->after('volume');
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['code', 'volume', 'price']);
        });
    }
}; 