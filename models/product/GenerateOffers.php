<?php namespace Drabbit\Avito\Models\Product;

use Carbon\Carbon;
use Drabbit\Avito\Controllers\Products;
use Drabbit\Avito\Models\Layout;
use Drabbit\Avito\Models\Offer;
use Drabbit\Avito\Models\Offer\FilterConfig;
use Drabbit\Avito\Models\Product;
use Intervention\Image\ImageManagerStatic as Image;
use System\Helpers\DateTime;
use System\Models\File;

class GenerateOffers
{

    const TITLE = 'Название', ADDRESS = 'Адрес', IMAGE = 'Изображение';
    const COUNTRY = 'Россия', CITY =  'Санкт-Петербург',
        COUNTRY_AND_CITY = self::COUNTRY . ', ' . self::CITY . ', ';

    /**
     * @var Product
     */
    protected $product;
    /**
     * @var Layout
     */
    protected $layout;
    /**
     * @var Carbon|Carbon\Carbon
     */
    protected $dateTime;
    /**
     * @var false|string[]
     */
    protected $titleList;
    /**
     * @var int[]
     */
    protected $counter;
    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $now;
    public $productDateTimeList = null;
    /**
     * @var \DateInterval
     */
    protected $intervalOffer;
//    /**
//     * @var Carbon
//     */
    protected $endAt;
    /**
     * @var false|string[]
     */
    protected $addressList;
    protected $assetImages;
    protected $type;
    protected $pathDir;
    protected $titleStr;
    protected $address;
    public $offer;

    protected function configInterval()
    {
        // Интервал выпуска Офферы
        $this->intervalOffer = new \DateInterval('P1D');
    }

    public function make(Product $product)
    {
        $this->product = $product;
        $this->layout = $product->getLayout();
        // получаем дату начала из модели "Шаблоны"
        $this->dateTime = $this->layout->begin_at;
        $this->now = now()->add(new \DateInterval('PT3H'));
        $this->endAt = (clone $this->dateTime)->add(new \DateInterval('P1M'));
        // счетчики
        $this->counter = [self::TITLE => 0, self::ADDRESS => 0, self::IMAGE => 0];

        $this->pathDir = 'app/uploads/public/images/' . $this->product->avito_id;

        $this->type = Product::TYPE[$this->product->type];

        $this->titleList = explode(',', $product->title); // Список значений из формы поля title
        $this->titleStr =  implode(', ', $this->titleList); // Получаем Список в виде строки

        $this->addressList = explode(',', $this->layout->address);

        $this->addProductDateTimeList();
        $this->configInterval();

        return $this;
    }

    public function run()
    {
        // Создаем Офферы в зависимости от выбранного пола
        if($this->product->comment == 'unisex') {
            $this->createOrUpdateOffers('female');
            $this->createOrUpdateOffers('male');
        } else {
            $this->createOrUpdateOffers();
        }
    }

    public function beginCreateOrUpdateOffers()
    {
        return $this;
    }

    protected function createOrUpdateOffers($status=null)
    {
        $status = is_null($status) ? $this->product->comment : $status; // male, female, unisex

        ini_set('max_execution_time', 900); // Увеличим время ожидания загрузки
        foreach ($this->getSizeFootwearList($status) as $sizeId => $sizeValue)
        {
            $sizeConfigKey = array_search($sizeValue, FilterConfig::$sizeClothing[$this->getStatus($status)]);

            $this->createUniqueImages($sizeId);

            $this->beginCreateOrUpdateOffers();

            $this->offer[$this->product->id] = Offer::updateOrCreate(// создание или обновление модели
                [// проверка на существование модели
                    'avito_id' => $this->getAvito($sizeId)
                ],
                [
                    'avito_id' => $this->getAvito($sizeId),
                    'begin_at' => $this->dateTime->format('Y-m-d H:i:s'),
                    'end_at' => $this->endAt->format('Y-m-d H:i:s'),
                    'listing_fee' => '',
                    'ad_status' => '',
                    'allow_email' => '',
                    'manager_name' => '',
                    'contact_phone' => '',
                    'address' => $this->getAddress($this->addressList[$this->counter[self::ADDRESS]]),
                    'latitude' => '',
                    'longitude' => '',
                    'condition' => $this->getCondition($this->layout->category),
                    'category' => $this->layout->category,
                    'goods_type' => ['category' => ['clothes' => $this->getStatus($status)."s_clothing" ]],
                    'ad_type' => $this->layout->ad_type,
                    'apparel' => ["category"=>["goods_type"=>[$this->getStatus($status).'s_clothing'=>$this->layout->apparel]]],
                    'size' => [
                        "category"=>["goods_type"=>["apparel"=>[$this->getStatus($status).'s_clothing_footwear' => $sizeConfigKey]]]
                    ],
                    'title' => $this->titleList[$this->counter[self::TITLE]],
                    'description' => $this->getDescription($sizeId, $sizeValue),
                    'price' => $this->product->price,
                    'video_url' => '',
                ]
            );
            if (Offer::exists($this->getAvito($sizeId))) {
                $offer= $this->offer[$this->product->id];

                $offer->images()->delete();

                $counter = $this->counter[self::IMAGE];
                $this->addImage($offer, $counter);
                $this->addImage($offer, ++$counter);
            }

            $this->addIntervalInDateTimeOffer();
            $this->doNextCounter();
        }

        ini_set('max_execution_time', 30); // default
    }

    protected function addIntervalInDateTimeOffer()
    {
        $this->dateTime->add($this->intervalOffer);
        $this->endAt->add($this->intervalOffer);
    }

    protected function getAddress($address)
    {
        return 'Россия, '.$this->layout->city.', '. $address;
    }

    protected function getCondition($category)
    {
        if(in_array($category, ['baby_clothes', 'goods_for_kids'] ))
            return ['category' => ['children' => 'new_children']];

        return ['category' => ['rest' => 'new_rest']];
    }

    protected function getSizeFootwearList($status)
    {
        return Products::SIZE_FOOTWEAR[$status];
    }


    protected function validateTimeStampForRelevance()
    {
        $isInterval = $this->isInterval();
        while (!$isInterval) {
            $this->validateTimeMaxLimit();
            $this->validateTimeMinLimit();

            $isInterval = $this->isInterval();
        }
    }

    public function addProductDateTimeList()
    {
        $productLayoutId = $this->layout->id;
        if(is_null($this->productDateTimeList)) {
            // получаем дату начала из модели "Шаблоны"
            $this->productDateTimeList[$productLayoutId] = $this->dateTime;
        }
        elseif(! array_key_exists($productLayoutId, $this->productDateTimeList)) {
            $this->productDateTimeList[$productLayoutId] = $this->dateTime;
        }

        $this->dateTime = $this->productDateTimeList[$productLayoutId];

        return $this;
    }

    protected function isMaxLimit()
    {
        $datetimeMaxLimit = (clone $this->dateTime)->setTimeFrom($this->layout->time_limit);

        return $this->dateTime->timestamp > $datetimeMaxLimit->timestamp;
    }

    protected function isInterval()
    {
        $datetimeMinLimit = (clone $this->dateTime)->setTimeFrom($this->layout->begin_at); // 12:00
        $datetimeMaxLimit = (clone $this->dateTime)->setTimeFrom($this->layout->time_limit);// 18:00

        if($datetimeMaxLimit->hour <= $datetimeMinLimit->hour) $datetimeMaxLimit->addDay(1);

        return $datetimeMaxLimit->timestamp >= $this->dateTime->timestamp and
                $this->dateTime->timestamp >= $datetimeMinLimit->timestamp;
    }

    protected function validateTimeMaxLimit()
    {
        if($this->isMaxLimit()) {
            $datetimeMaxLimit = (clone $this->dateTime)->setTimeFrom($this->layout->time_limit);
            $interval = $this->dateTime->timestamp - $datetimeMaxLimit->timestamp;
            $beginAt = $this->layout->begin_at;

            $this->dateTime ->add(new \DateInterval('P1D'));
            $this->dateTime ->setTime($beginAt->hour, $beginAt->minute, $beginAt->second)
                            ->setTimestamp($this->dateTime->timestamp + $interval);

            $this->endAt    ->setTimeFrom($this->dateTime)->setDateFrom($this->dateTime)
                            ->add(new \DateInterval('P1M'));
        }

        return $this;
    }

    protected function validateTimeMinLimit()
    {
        $datetimeMinLimit = (clone $this->dateTime)->setTimeFrom($this->layout->begin_at);

        if($this->dateTime->timestamp < $datetimeMinLimit->timestamp) {
            $beginAt = $this->layout->begin_at;
            $day = new \DateInterval('P1D');

            $datetimeMaxLimit = (clone $this->dateTime)->sub($day)
                                                        ->setTimeFrom($this->layout->time_limit);

            $interval = $this->dateTime->timestamp - $datetimeMaxLimit->timestamp;

            $this->dateTime ->sub($day)->setTime($beginAt->hour, $beginAt->minute, $beginAt->second);
            $this->dateTime ->setTimestamp($this->dateTime->timestamp + $interval);

            $this->endAt    ->setTimeFrom($this->dateTime)->setDateFrom($this->dateTime)
                            ->add(new \DateInterval('P1M'));
        }

        return $this;
    }

    protected function doNextCounter()
    {
        if ($this->counter[self::TITLE] == (count($this->titleList) - 1))   $this->counter[self::TITLE] = 0;
        else $this->counter[self::TITLE]++;

        if ($this->counter[self::ADDRESS] == (count($this->addressList) - 1)) $this->counter[self::ADDRESS] = 0;
        else $this->counter[self::ADDRESS]++;

        if ($this->counter[self::IMAGE] == (count($this->assetImages) - 1)) $this->counter[self::IMAGE] = 0;
        else $this->counter[self::IMAGE]++;
    }

    public function getDescription($id, $size)
    {
        $keyList = ['$id', '$title', '$size', '$type'];
        $valueList = [
            $this->getAvito($id), $this->titleList[$this->counter[self::TITLE]], $size, $this->type
        ];

        return str_replace($keyList, $valueList, $this->layout->description);
    }

    protected function getStatus($status=null)
    {
        if ($status == 'female') $sex = 'women';
        elseif ($status == 'male') $sex = 'men';

        return $sex ?? null;
    }

    public function getAvito($id)
    {
        return $this->product->avito_id . $id;
    }

    protected function getFullAddress($id)
    {
        return self::COUNTRY_AND_CITY . $this->layout->getAddress($id);
    }

    protected function addImage(Offer $offer, $counter)
    {
        $countImages = count($this->assetImages) - 1;
        if($countImages === 0 and $counter > 0) return;

        if($counter > $countImages) {
            --$counter;
            $counter = $counter - $countImages;
        }


        $file = new File;
        $file->fromFile($this->assetImages[$counter]["path"]);
        $offer->images()->add($file);
    }

    protected function createUniqueImages($id)
    {
        $assetImages = array();

        // если изоброжений отсустует, то выдаем пустой массив
        if (!is_null($this->product->images) and count($this->product->images) == 0) return $assetImages;

        if($this->layout->logo)
            $logo = Image::make($this->layout->logo->getLocalPath())
                ->resizeCanvas(150, 150)
                ->opacity(40);// logo с прозрачностью 60%

        foreach ($this->product->images as $image) {
            $img = Image::make($image->getLocalPath());
            $pathDir = $this->pathDir . '/' . $this->counter[self::TITLE];
            $storagePathDir = storage_path($pathDir);
            // если путь не сущ, то создаем
            if (!file_exists($storagePathDir)) mkdir($storagePathDir, 0755, TRUE);

            $width = $img->getWidth();
            $height = $img->getHeight();

            if(!Offer::exists($this->getAvito($id)))
                $img->flip();               // Отзеракливание

            $img->brightness(rand(-5, 5))   // яркость
            ->sharpen(rand(0, 20))          // резкость
            ->contrast(rand(-5, 5))         // контраст
            ->resize($width - rand(0, 15), $height - rand(0, 15)); // размер

            if(isset($logo))
                $img->insert($logo, 'top-left', rand(0, $width - 150), rand(0, $height - 150)); // Лого в изображении

            $img->save("$storagePathDir/$img->basename");

            $assetImages[] = [
                "asset" => asset("storage/$pathDir/$img->basename"),
                "path" => "$storagePathDir/$img->basename"];
        }

        return $this->assetImages = $assetImages;
    }
}