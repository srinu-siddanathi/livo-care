<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->integer('patient_age')->nullable()->after('patient_mobile');
            $table->enum('patient_gender', ['male', 'female', 'other'])->nullable()->after('patient_age');
            $table->text('notes')->nullable()->after('patient_gender');
        });
    }

    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['patient_age', 'patient_gender', 'notes']);
        });
    }
}; 