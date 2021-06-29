<?php namespace Drabbit\Avito\Models;

use Drabbit\Avito\Models\Offer\FilterConfig;
use Drabbit\Avito\Models\Offer\FilterFields;

use Model;

/**
 * Offer Model
 */
class Offer extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'drabbit_avito_offers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'avito_id', 'begin_at', 'end_at', 'listing_fee', 'ad_status', 'allow_email', 'manager_name', 'contact_phone',
        'address', 'latitude', 'longitude', 'category', 'goods_type', 'ad_type', 'apparel', 'size', 'condition',
        'title', 'description', 'price', 'video_url', 'images'
    ];

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
    protected $jsonable = [
        'goods_type',
        'condition',
        'apparel',
        'size',
    ];

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
        'begin_at',
        'end_at',
    ];

    public $attributes = [];

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
        'images' => 'System\Models\File',
    ];
    /**
     * @var FilterFields
     */
    public $filter;

    public function __construct()
    {
        $this->filter = new FilterFields($this);
    }

    static public function exists($id)
    {
        return self::where('avito_id', $id)->exists();
    }

    static public function deleteAll()
    {
        foreach (self::all() as $offer) {
            $idList[] = $offer->id;
        }

        // удаляем все офферы
        self::destroy($idList);
        self::truncate();
    }

    public function getProduct(): Product
    {
        $avito_id = substr($this->avito_id, 0, -2);
        return Product::where('avito_id', $avito_id)->first();
    }

    /**
     * Options for fields
     */
    public function getCategoryOptions()
    {
        return $this->filter->categoriesList;
    }

    /** GOODS
     *
     * Option lists for templates
     */
    public function listGoodsTypeClothes()
    {
        return $this->filter->clothesList;
    }

    public function listGoodsTypeBabyClothes()
    {
        return $this->filter->babyClothesList;
    }

    public function listGoodsTypeGoodsForKids()
    {
        return $this->filter->goodsForKidsList;
    }

    public function listGoodsTypeJewelry()
    {
        return $this->filter->jewelryList;
    }

    public function listGoodsTypeHealthAndBeauty()
    {
        return $this->filter->healthAndBeautyList;
    }

    /** APPAREL
     *
     * Option lists for templates
     */
    public function listApparelWomensClothing()
    {
        return $this->filter->womensClothingList;
    }

    public function listApparelMensClothing()
    {
        return $this->filter->mensClothingList;
    }

    public function listApparelForGirls()
    {
        return $this->filter->forGirlsList;
    }

    public function listApparelForBoys()
    {
        return $this->filter->forBoysList;
    }

    /** Condition
     *
     * Option lists for templates
     */

    public function listConditionForChildren()
    {
        return $this->filter->conditionForChildrenList;
    }
    public function listConditionForRest()
    {
        return $this->filter->conditionForRestList;
    }

    /** SIZE
     *
     * Option lists for templates
     */
    public function listSizeWomensClothing()
    {
        return $this->filter->sizeWomensClothing;
    }

    public function listSizeMensClothing()
    {
        return $this->filter->sizeMensClothing;
    }

    public function listSizeWomensClothingJeans()
    {
        return $this->filter->sizeWomensClothingJeans;
    }

    public function listSizeMensClothingJeans()
    {
        return $this->filter->sizeMensClothingJeans;
    }

    public function listSizeWomensClothingFootwear()
    {
        return $this->filter->sizeWomensClothingFootwear;
    }

    public function listSizeMensClothingFootwear()
    {
        return $this->filter->sizeMensClothingFootwear;
    }

    public function listSizeForGirlsAndBoys()
    {
        return $this->filter->sizeForGirlsAndBoys;
    }

    public function listSizeForGirlsAndBoysFootwear()
    {
        return $this->filter->sizeForGirlsAndBoysFootwear;
    }


    /** Загрузчик фильтров
     * @param $fields
     * @param null $context
     */
    public function filterFields($fields, $context = null){
        $this->filter->setDefaultFieldsHidden($fields);

        $this->filter->filterFieldGoodsType($fields);
        $this->filter->filterFieldCondition($fields);
        $this->filter->filterFieldApparel($fields);
        $this->filter->filterFieldSize($fields);
    }


    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Значения dropdown
     *
     * @return mixed|null
     */
    public function getAdType()
    {
        return FilterConfig::$ad_type[$this->ad_type];
    }

    public function getCategory()
    {
        return $this->filter->categoriesList[$this->category];
    }

    public function getGoodsType()
    {
        $goodsTypeConfig = array_merge(
            $this->filter->healthAndBeautyList,
            $this->filter->clothesList,
            $this->filter->babyClothesList,
            $this->filter->goodsForKidsList,
            $this->filter->jewelryList
        );

        if(is_array($this->goods_type['category'])) {
            $goodsType = $this->goods_type['category'];
            $goodsTypeValue = $goodsType[key($goodsType)];

            if(!is_null($goodsTypeValue))
                $result = $goodsTypeConfig[$goodsTypeValue];
        }

        return $result ?? null;
    }


    public function getCondition()
    {
        $conditionConfig = array_merge(
            $this->filter->conditionForChildrenList,
            $this->filter->conditionForRestList
        );

        if(is_array($this->condition['category'])){
            $condition = $this->condition['category'];
            $conditionValue = $condition[key($condition)];

            if(!is_null($conditionValue))
                $result = $conditionConfig[$conditionValue];
        }
        return $result ?? null;
    }

    public function getApparel()
    {
        $apparelConfig = array_merge(
            $this->filter->womensClothingList,
            $this->filter->mensClothingList,
            $this->filter->forGirlsList,
            $this->filter->forBoysList
        );

        if(is_array($this->apparel['category']['goods_type'])){
            $apparel = $this->apparel['category']['goods_type'];
            $apparelValue = $apparel[key($apparel)];

            if(!is_null($apparelValue))
                $result = $apparelConfig[$apparelValue];
        }

        return $result ?? null;
    }

    public function getSizeFootwear()
    {
        $size = $this->size['category']['goods_type']['apparel'];
        $configKey = key($size);
        $configVal = current($size);

        if($configKey == 'womens_clothing') $result = $this->filter->sizeWomensClothing[$configVal];
        elseif($configKey == 'mens_clothing') $result = $this->filter->sizeMensClothing[$configVal];
        elseif($configKey == 'womens_clothing_jeans') $result = $this->filter->sizeWomensClothingJeans[$configVal];
        elseif($configKey == 'mens_clothing_jeans') $result = $this->filter->sizeMensClothingJeans[$configVal];
        elseif($configKey == 'womens_clothing_footwear') $result = $this->filter->sizeWomensClothingFootwear[$configVal];
        elseif($configKey == 'mens_clothing_footwear') $result = $this->filter->sizeMensClothingFootwear[$configVal];
        elseif (in_array($configKey, ['for_girls', 'for_girls_footwear']))
            $result = $this->filter->sizeForGirlsAndBoys[$configVal];
        elseif(in_array($configKey, ['for_boys', 'for_boys_footwear']))
            $result = $this->filter->sizeForGirlsAndBoysFootwear[$configVal];

        return $result ?? null;
    }
}
