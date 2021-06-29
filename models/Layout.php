<?php namespace Drabbit\Avito\Models;

use Carbon\Carbon;
use Model;

/**
 * Layout Model
 */
class Layout extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'drabbit_avito_layouts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'time_limit',
        'begin_at',
        'end_at_cycle'
    ];

    public $attributes = [
        'description' =>
'<p> $type $title</p>
<p></p>
<p>👟Обувь для повседневной носки,которая вам точно понравится.</p>
<p></p>
<p>📷 Дополнительные фото по запросу. 📷</p>
<p>————————————————————————</p>
<p></p>
<p>✖️ Хит сезона</p>
<p></p>
<p>✖️ Бренд:$title</p>
<p></p>
<p>✖️ Оригинальное качество</p>
<p></p>
<p>✖️ Материал: кожа , текстиль</p>
<p></p>
<p>✖️ Оригинальная упаковка с полным комплектом</p>
<p></p>
<p>арт.$id</p></p>
<p></p>
<p>☑️Размер $size. Не подходит? Не переживай, в наличии есть и другие размеры!(уточняйте в чате или по телефону)</p>
<p></p>
<p>⁉️Хотите заказать или примерить ? Оставьте заявку и менеджер свяжется с вами 🙋‍♀️</p>
<p></p>
<p>————————————————————————</p>
<p></p>
<p>◽️Самовывоз с магазина в Центре Города</p>
<p></p>
<p>◽️Доставка по СПб за 3 часа!</p>
<p></p>
<p>◽️Привезём несколько пар/моделей для примерки.</p>
<p></p>
<p>◽️Отправка наложенным платежом в регионы, а так же по всему Миру!</p>
<p></p>
<p>◽️Оплата при получении.</p>
<p></p>
<p>🎁 Заказывай прямо сейчас и получите Подарок! 🎁</p>
<p></p>
<p>‼️Так же мы работаем оптом, по вопросам сотрудничества обращайтесь по телефону☎️</p>
<p></p>
<p>В магазине Twinkls✨ собраны все актуальные новинки обуви и аксессуаров.</p>
<p></p>
<p>◾️Переходите по ссылке снизу, чтобы увидеть полный ассортимент: Guссi Rhyton, Palm Angels, Moncler, Yeezy Boost, Gucci New York, кроссовки Louis Vuitton, кеды Dior и др.</p>
<p></p>
<p>◾️ Остались вопросы? Пишите/звоните на WhаtsАрр’s или в чат на Авито.</p>'
        ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = ["logo" => "System\Models\File"];
    public $attachMany = [];

    public function __construct(array $attributes = array())
    {
        $this->attributes['time_limit'] = new Carbon('20:00');
        $this->attributes['interval_minutes'] = 0;
        $this->attributes['interval_days'] = 1;

        parent::__construct($attributes);
    }

    public function getAddressList()
    {
        return explode(',', $this->address);
    }

    public function getAddress($id)
    {
        return $this->getAddressList()['$id'];
    }
}
