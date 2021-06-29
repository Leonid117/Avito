<?php namespace Drabbit\Avito\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDrabbitAvitoOffers extends Migration
{
    public function up()
    {
        Schema::create('drabbit_avito_offers', function($table)
        {
            Schema::dropIfExists('drabbit_avito_offers');
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('avito_id')->unique();

            $table->timestampTz('begin_at'); // Дата и время начала размещения объявления
            $table->timestampTz('end_at'); // Дата и время, до которых объявление актуально

            $table->string('listing_fee'); // Вариант платного размещения
            $table->string('ad_status'); // Платная услуга, которую нужно применить к объявлению
            $table->string('allow_email', 3)->default('Да'); // Возможность написать сообщение по объявлению через сайт
            $table->string('manager_name', 40); // Имя менеджера, контактного лица компании по данному объявлению
            $table->string('contact_phone'); // Контактный телефон

            $table->string('address')->nullable(); // Полный адрес объекта. Либо Latitude, Longitude
            $table->string('latitude')->nullable(); // Географические координаты
            $table->string('longitude')->nullable(); // Географические координаты

            $table->string('category'); // Категория товара
            $table->string('goods_type'); // Вид товара
            $table->string('ad_type'); // Вид объявления

            $table->json('apparel')->nullable(); // Предмет одежды. Одежда, обувь, аксессуары, детская одежда и обувь*

            $table->json('size')->nullable(); // Размер одежды или обуви — значение зависит от вида товара (GoodsType) и предмета одежды (Apparel).
            $table->json('condition'); // Для подкатегорий: Новый, Б/у

            $table->string('title', 50); // Название объявления
            $table->text('description'); // Текстовое описание объявления в соответствии с правилами Авито
            $table->integer('price'); // Цена в рублях

            /** images */ // Фотографии. Max 10 photo
            $table->string('video_url')->nullable(); // Видео c YouTube https://www.youtube.com/watch?v=***

            $table->timestampsTz();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('drabbit_avito_offers');
    }
}
