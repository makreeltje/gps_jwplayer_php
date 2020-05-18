<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cues', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("voice");
            $table->string("text");
            $table->integer("identifier");
            
        });
        /*
 array (size=5)
          'voice' => string '' (length=0)
          'start' => int 31
          'end' => int 33
          'text' => string 'Dan is inderdaad buiten iemand echt precies niet. ' (length=50)
          'identifier' => string '' (length=0)
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cues');
    }
}
