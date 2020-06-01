<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIclassSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iclass_sections', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('iclass_id');
            $table->unsignedBigInteger('section_id');
            $table->timestamps();

            $table->foreign('institute_id')->references('id')->on('institutes')->onDelete('cascade');
            $table->foreign('iclass_id')->references('id')->on('iclasses')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iclass_sections');
    }
}
