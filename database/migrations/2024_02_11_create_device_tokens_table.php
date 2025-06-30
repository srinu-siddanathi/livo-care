<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); // This creates user_type and user_id columns
            $table->string('device_id');
            $table->timestamps();

            // Ensure device_id is unique per user
            $table->unique(['tokenable_type', 'tokenable_id', 'device_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_tokens');
    }
}; 