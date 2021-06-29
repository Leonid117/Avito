<?php


namespace Drabbit\Avito\Models\Offer;


use Drabbit\Avito\Models\Offer;

class FilterFields
{


    /**
     * @var Offer
     */
    public $model;

    function __construct(Offer $model)
    {
        $this->model = $model;
    }
    /** Свойства для фильтров */
    use FilterConfig;

    /** Настройки поумолчанию */
    public function setDefaultFieldGoodsTypeValue($fields)
    {
        foreach ($this->categoriesList as $key => $value) {
            $fields->{"goods_type[category][$key]"}->value = null;
        }
    }
    public function setDefaultFieldGoodsTypeHidden($fields)
    {
        foreach ($this->categoriesList as $key => $value) {
            $fields->{"goods_type[category][$key]"}->hidden = true;
        }
    }

    public function setDefaultFieldConditionValue($fields)
    {
        $fields->{"condition[category][children]"}->value = null;
        $fields->{"condition[category][rest]"}->value = null;
    }
    public function setDefaultFieldConditionHidden($fields)
    {
        $fields->{"condition[category][children]"}->hidden = true;
        $fields->{"condition[category][rest]"}->hidden = true;
    }

    public function setDefaultFieldApparelValue($fields)
    {
        foreach ($this->goodsTypeListForFieldApparel as $key => $value) {
            $fields->{"apparel[category][goods_type][$key]"}->value = null;
            $a[] = $fields->{"apparel[category][goods_type][$key]"}->value;
        }
    }
    public function setDefaultFieldApparelHidden($fields)
    {
        foreach ($this->goodsTypeListForFieldApparel as $key => $value) {
            $fields->{"apparel[category][goods_type][$key]"}->hidden = true;
            $a[] = $fields->{"apparel[category][goods_type][$key]"}->hidden;
        }
    }

    public function setDefaultFieldSizeValue($fields)
    {
        $sizeList = array_merge($this->goodsTypeListForFieldApparel, $this->apparelListForFieldSize);
        foreach ($sizeList as $key => $value) {
            $fields->{"size[category][goods_type][apparel][$key]"}->value = null;
        }
    }
    public function setDefaultFieldSizeHidden($fields)
    {
        $sizeList = array_merge($this->goodsTypeListForFieldApparel, $this->apparelListForFieldSize);
        foreach ($sizeList as $key => $value) {
            $fields->{"size[category][goods_type][apparel][$key]"}->hidden = true;
            $a[$key] = $fields->{"size[category][goods_type][apparel][$key]"}->hidden;
        }
    }

    public function setDefaultFieldsValue($fields)
    {
        $this->setDefaultFieldGoodsTypeValue($fields);

        $this->setDefaultFieldConditionValue($fields);

        $this->setDefaultFieldApparelValue($fields);

        $this->setDefaultFieldSizeValue($fields);
    }
    public function setDefaultFieldsHidden($fields)
    {
        $this->setDefaultFieldGoodsTypeHidden($fields);

        $this->setDefaultFieldConditionHidden($fields);

        $this->setDefaultFieldApparelHidden($fields);

        $this->setDefaultFieldSizeHidden($fields);
    }

    /**  */
    function getGoodsTypeValues($fields)
    {
        foreach ($this->categoriesList as $key => $value){
            $fieldGoodsType[$key] = $fields->{"goods_type[category][$key]"}->value;
        }

        return $fieldGoodsType;
    }

    function getApparelValues($fields)
    {
        $a = $this->goodsTypeListForFieldApparel;
        foreach ($a as $key => $value){
            $fieldApparel[$key] = $fields->{"apparel[category][goods_type][$key]"}->value;
        }

        return $fieldApparel;
    }
    function getApparelHidden($fields)
    {
        foreach ($this->goodsTypeListForFieldApparel as $key => $value){
            $fieldApparel[$key] = $fields->{"apparel[category][goods_type][$key]"}->hidden;
        }

        return $fieldApparel;
    }


    /**
     * Методы филтрации полей
     *
     * @param $fields
     */
    public function filterFieldGoodsType($fields) // Категория товара
    {
        $fieldCategory = $fields->category->value;
        if ($fieldCategory !== null) {
            foreach ($this->categoriesList as $key => $value) {
                if ($fieldCategory == $key) {
                    $this->setDefaultFieldsHidden($fields);
                    $fields->{"goods_type[category][$key]"}->hidden = false;
                }
                else $fields->{"goods_type[category][$key]"}->value = null;
            }
        }
    }

    public function filterFieldCondition($fields)
    {
        $fieldCategory = $fields->category->value;
        if ($fieldCategory !== null) {
            foreach ($this->categoriesList as $key => $value) {
                if (in_array($fieldCategory, ["baby_clothes", "goods_for_kids"])) {
                    $fields->{"condition[category][children]"}->hidden = false;
                }
                elseif (in_array($fieldCategory, ["clothes", "jewelry", "health_and_beauty"])) {
                    $fields->{"condition[category][rest]"}->hidden = false;
                }
            }
        }
    }

    public function filterFieldApparel($fields) // Вид товара
    {

        if (!in_array($fields->category->value, ["clothes", "baby_clothes"])) {
            $this->setDefaultFieldGoodsTypeValue($fields);
            $this->setDefaultFieldApparelValue($fields);
            $this->setDefaultFieldSizeValue($fields);
            return;
        }
        $fieldListAll = $this->getGoodsTypeValues($fields);

        if (in_array( !null, $fieldListAll)){
            foreach ($this->goodsTypeListForFieldApparel as $key => $value) {
                if (in_array($key, $fieldListAll)) {
                    $this->setDefaultFieldApparelHidden($fields);
                    $this->setDefaultFieldSizeHidden($fields);
                    $fields->{"apparel[category][goods_type][$key]"}->hidden = false;
                    $fields->{"size[category][goods_type][apparel][$key]"}->hidden = false;
                }
                else {
                    $fields->{"apparel[category][goods_type][$key]"}->value = null;
                    $fields->{"size[category][goods_type][apparel][$key]"}->value = null;
                    $fields->{"apparel[category][goods_type][$key]"}->hidden = true;
                    $fields->{"size[category][goods_type][apparel][$key]"}->hidden = true;
                }
            }
        }
    }

    public function filterFieldSize($fields) // Размер одежды или обуви
    {
        if (!in_array($fields->category->value, ["clothes", "baby_clothes"])) return;
        $fieldsApparel = $this->getApparelValues($fields);


        if(in_array(!null, $this->getApparelValues($fields)) and in_array(false, $this->getApparelHidden($fields))) {
            foreach ($fieldsApparel as $key => $value) {
                $name = "size[category][goods_type][apparel][$key" ."_". "$value]";
                if(in_array($value, ['jeans', 'footwear'])){
                    $this->setDefaultFieldSizeHidden($fields);
                    $fields->{$name}->hidden = false;
                }
            }
        }
    }
}