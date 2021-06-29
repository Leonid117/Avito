<?php namespace Drabbit\Avito\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteDrabbitAvitoOfferProducts extends Migration
{
    public function up()
    {
        Schema::dropIfExists('drabbit_avito_offer_products');
    }
    
    public function down()
    {
        Schema::create('drabbit_avito_offer_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
        });
    }
}
