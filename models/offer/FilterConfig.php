<?php namespace Drabbit\Avito\Models\Offer;


trait FilterConfig
{
    public static $ad_type = [
        'Товар приобретен на продажу',
        'Товар от производителя',
    ];

    /**
     * CATEGORIES OPTIONS
     */
    public $categoriesList = [
        'clothes' => 'Одежда, обувь, аксессуары',
        'baby_clothes' => 'Детская одежда и обувь',
        'goods_for_kids' => 'Товары для детей и игрушки',
        'jewelry' => 'Часы и украшения',
        'health_and_beauty' => 'Красота и здоровье',
    ];

    /**
     * GOODS TYPE OPTIONS
     */
    public $clothesList = [ // Товары для детей и игрушки
        "womens_clothing" => "Женская одежда",
        "mens_clothing" => "Мужская одежда",
        "accessories" => "Аксессуары",
    ];
    public $babyClothesList = [ // Детская одежда и обувь
        "for_girls" => "Для девочек",
        "for_boys" => "Для мальчиков",
    ];
    public $goodsForKidsList = [ // Товары для детей и игрушки
        "car_seats" => "Автомобильные кресла",
        "bicycles_and_scooters" => "Велосипеды и самокаты",
        "childrens_furniture" => "Детская мебель",
        "strollers" => "Детские коляски",
        "toys" => "Игрушки",
        "bed_dress" => "Постельные принадлежности",
        "products_for_feeding" => "Товары для кормления",
        "bathing_goods" => "Товары для купания",
        "goods_for_school" => "Товары для школы",
    ];
    public $jewelryList = [ // Часы и украшения
        "bijouterie" => "Бижутерия",
        "clock" => "Часы",
        "jewelry" => "Ювелирные изделия",
    ];
    public $healthAndBeautyList = [ // Красота и здоровье
        "cosmetics" => "Косметика",
        "perfumery" => "Парфюмерия",
        "devices_and_accessories" => "Приборы и аксессуары",
        "hygiene_products" => "Средства гигиены",
        "hair_products" => "Средства для волос",
        "medical_devices" => "Медицинские изделия",
        "biologically_active_additives" => "Биологически активные добавки",
    ];

    /**
     * APPAREL OPTIONS
     */
    public $womensClothingList = [
        "pants" => "Брюки",
        "outerwear" => "Верхняя одежда",
        "jeans" => "Джинсы",
        "swimwear" => "Купальники",
        "underwear" => "Нижнее бельё",
        "footwear" => "Обувь",
        "blazers_and_suits" => "Пиджаки и костюмы",
        "dresses_and_skirts" => "Платья и юбки",
        "shirts_blouses" => "Рубашки и блузки",
        "wedding_dresses" => "Свадебные платья",
        "tops_and_t-shirts" => "Топы и футболки",
        "jersey" => "Трикотаж",
        "other" => "Другое",
    ];
    public $mensClothingList = [
        "pants" => "Брюки",
        "outerwear" => "Верхняя одежда",
        "jeans" => "Джинсы",
        "footwear" => "Обувь",
        "blazers_and_suits" => "Пиджаки и костюмы",
        "shirts" => "Рубашки",
        "jersey" => "Трикотаж и футболки",
        "other" => "Другое",
    ];
    public $forGirlsList = [
        "pants" => "Брюки",
        "outerwear" => "Верхняя одежда",
        "jumpsuits_and_bodysuits" => "Комбинезоны и боди",
        "footwear" => "Обувь",
        "pajamas" => "Пижамы",
        "dresses_and_skirts" => "Платья и юбки",
        "hats_mittens_scarves" => "Шапки, варежки, шарфы",
        "jersey" => "Трикотаж",
        "other" => "Другое",
    ];
    public $forBoysList = [
        "pants" => "Брюки",
        "outerwear" => "Верхняя одежда",
        "jumpsuits_and_bodysuits" => "Комбинезоны и боди",
        "footwear" => "Обувь",
        "pajamas" => "Пижамы",
        "hats_mittens_scarves" => "Шапки, варежки, шарфы",
        "jersey" => "Трикотаж",
        "other" => "Другое",
    ];

    /**
     * CONDITION OPTIONS
     */
    public $conditionForChildrenList = [
        "new_children" => "Новый",
        "used" => "Б/у",
    ];
    public $conditionForRestList = [
        "new_rest" => "Новое",
        "used" => "Б/у",
    ];


    /**
     * SIZE OPTIONS
     */
    static public $sizeClothing = [
        "women" => ["< 35", "36", "37", "38", "39", "40", "> 41",],
        "men" => ["< 40", "41", "42", "43", "44", "45", "> 46",]
    ];

    public $sizeWomensClothing = [
        "40–42 (XS)",
        "42–44 (S)",
        "44–46 (M)",
        "46–48 (L)",
        "48–50 (XL)",
        "> 50 (XXL)",
        "Без размера",
    ];
    public $sizeMensClothing = [
        "42–44 (XS)",
        "44–46 (S)",
        "46–48 (M)",
        "48–50 (L)",
        "50–52 (XL)",
        "52–54 (XXL)",
        "> 54 (XXXL)",
        "Без размера",
    ];
    public $sizeWomensClothingJeans = [
        "25",
        "26",
        "27",
        "28",
        "29",
        "30",
        "31",
        "32",
        "33",
        "> 34",
        "Без размера",
    ];
    public $sizeMensClothingJeans = [
        "29",
        "30",
        "31",
        "32",
        "33",
        "34",
        "35",
        "36",
        "37",
        "38",
        "> 38",
        "Без размера"
    ];
    public $sizeWomensClothingFootwear = [
        "< 35",
        "36",
        "37",
        "38",
        "39",
        "40",
        "> 41",
    ];
    public $sizeMensClothingFootwear = [
        "< 40",
        "41",
        "42",
        "43",
        "44",
        "45",
        "> 46",
    ];
    public $sizeForGirlsAndBoys = [
        "50-56 cм (0-2 мес)",
        "62-68 см (2-6 мес)",
        "74-80 см (7-12 мес)",
        "86-92 см (1-2 года)",
        "98-104 см (2-4 года)",
        "110-116 см (4-6 лет)",
        "122-128 см (6-8 лет)",
        "134-140 см (8-10 лет)",
        "146-152 см (10-12 лет)",
        "Без размера",
    ];
    public $sizeForGirlsAndBoysFootwear = [
        "< 19",
        "20",
        "21",
        "22",
        "23",
        "24",
        "25",
        "26",
        "27",
        "28",
        "29",
        "30",
        "31",
        "32",
        "33",
        "34",
        "35",
        "36",
        "> 36",
        "Без размера",
    ];


    /**
     *  Ключевые поля для фильтрации
     */
    public $goodsTypeListForFieldApparel = [
        "womens_clothing" => "Женская одежда",
        "mens_clothing" => "Мужская одежда",
        "for_girls" => "Для девочек",
        "for_boys" => "Для мальчиков",
    ];
    protected $apparelListForFieldSize = [
        'womens_clothing_jeans' => "Женская одежда / Джинсы",
        "mens_clothing_jeans" => "Мужская одежда / Джинсы",
        'womens_clothing_footwear' => "Женская одежда / Обувь",
        "mens_clothing_footwear" => "Мужская одежда / Обувь",
        "for_girls_footwear" => "Для девочек / Обувь",
        "for_boys_footwear" => "Для мальчиков / Обувь",
    ];
}