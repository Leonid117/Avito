<?php namespace Drabbit\Avito\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('drabbit_avito_products', function (Blueprint $table) {
            Schema::dropIfExists('drabbit_avito_products');
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('title');
            $table->string('type');

            $table->string('avito_id')->unique();
            $table->bigInteger('price');
            $table->string('comment');

            $table->bigInteger('layout_id');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drabbit_avito_products');
    }
}
