<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->string('name', 1000)->nullable(false)->change(); // Increasing length to 1000 characters
            $table->text('description')->nullable()->change(); // Making description TEXT and allowing NULL
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->change(); // Reverting back to default length
            $table->string('description')->nullable()->change();
        });
    }
}; 