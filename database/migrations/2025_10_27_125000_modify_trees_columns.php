<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTreesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // NOTE: change() requires doctrine/dbal package
        Schema::table('trees', function (Blueprint $table) {
            // increase health_status length and allow null
            $table->string('health_status', 50)->nullable()->change();
            // allow longer text for image url and notes
            $table->text('image_url')->nullable()->change();
            $table->text('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trees', function (Blueprint $table) {
            // revert to previous conservative sizes
            $table->string('health_status', 20)->nullable()->change();
            $table->string('image_url', 255)->nullable()->change();
            $table->string('notes', 255)->nullable()->change();
        });
    }
}
