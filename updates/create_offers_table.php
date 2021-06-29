<?php namespace Drabbit\Avito\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateOffersTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('drabbit_avito_offers');
//        Schema::create('drabbit_avito_offers', function (Blueprint $table) {
//
//            $table->engine = 'InnoDB';
//            $table->increments('id');
//
//            $table->string('title')->unique();
//            $table->string('avito_id')->unique();
//            $table->bigInteger('price');
//            $table->string('comment');
//            $table->timestamps();
//        });
    }

    public function down()
    {
        Schema::dropIfExists('drabbit_avito_offers');
    }
}
