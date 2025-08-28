<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_add_filiere_id_to_classrooms_table.php

public function up()
{
    Schema::table('classrooms', function (Blueprint $table) {
        $table->unsignedBigInteger('filiere_id')->after('id')->nullable();

        $table->foreign('filiere_id')
              ->references('id')
              ->on('filieres')
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::table('classrooms', function (Blueprint $table) {
        $table->dropForeign(['filiere_id']);
        $table->dropColumn('filiere_id');
    });
}

};
