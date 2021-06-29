<?php namespace Drabbit\Avito\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateLayoutsTable extends Migration
{
    public function up()
    {
        Schema::create('drabbit_avito_layouts', function (Blueprint $table) {
            Schema::dropIfExists('drabbit_avito_layouts');
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('category');
            $table->string('ad_type');
            $table->string('apparel');
            $table->string('city');
            $table->text('address');
            $table->integer('interval_minutes')->nullable();
            $table->integer('interval_days')->nullable();
            $table->text('description');
            $table->string('time_limit');

            $table->timestampTz('begin_at'); // Дата и время начала размещения объявления
            $table->boolean('on_interval')->default(False);
            $table->timestampTz('end_at_cycle')->nullable();
            $table->boolean('on_random')->default(False);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drabbit_avito_layouts');
    }
}
