<?php namespace Drabbit\Avito\Models;

use Model;

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'drabbit_avito_products';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['layout_id'];

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
        'updated_at'
    ];

    const SNEAKERS = "sneakers", GUMSHOES = "gumshoes", BOOTS = "boots";
    const TYPE = array(
        self::SNEAKERS => 'Кроссовки',
        self::BOOTS => 'Ботинки',
        self::GUMSHOES => 'Кеды'
    );

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
    public $attachOne = [];
    public $attachMany = [
        'images' => ['System\Models\File']
    ];

    public function __construct(array $attributes = array())
    {
        $this->attributes['avito_id'] = $this->getGenerateAvitoId();

        parent::__construct($attributes);
    }

    public function getLayoutIdOptions()
    {
        $options = array();
        foreach (Layout::all() as $layout) {
            $options[$layout->id] = "$layout->id - $layout->name";
        }

        return $options;
    }

    public function getTypeOptions()
    {
        return self::TYPE;
    }

    function getGenerateAvitoId()
    {
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle($permitted_chars), 0, '5');
    }

    public function getLayout() : Layout
    {
        return Layout::where('id', $this->layout_id)->first();
    }

    public function getTitleList()// Список значений из формы поля title
    {
        return explode(',', $this->title);
    }

    public function getTitle($id)
    {
        return $this->getTitleList()[$id];
    }

}