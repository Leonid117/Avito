<?php namespace Drabbit\Avito\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Carbon\Carbon;
use Drabbit\Avito\Models\Layout;
use Drabbit\Avito\Models\Offer;
use Drabbit\Avito\Models\Offer\FilterConfig;
use Drabbit\Avito\Models\Product;
use Intervention\Image\ImageManagerStatic as Image;
use MongoDB\Driver\Exception\Exception;
use October\Rain\Exception\ExceptionBase;
use October\Rain\Support\Facades\Flash;
use System\Models\File;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];
    /**
     * @var Layout
     */
    public $dateTime = array();

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    const SIZE_FOOTWEAR = [ // Размеры Обуви и их Идентификатор
        'female' => [
            "01" => "< 35", "02" => "36", "03" => "37", "04" => "38", "05" => "39", "06" => "40", "07" => "> 41",],
        'male' => [
            "08" => "< 40", "09" => "41", "10" => "42", "11" => "43", "12" => "44", "13" => "45", "14" => "> 46",],
    ];

    public $sizeFootwear = self::SIZE_FOOTWEAR;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Drabbit.Avito', 'avito', 'products');
    }


    /**
     * Шаблгизатор(+-)
     * Заменяет ключевые слова в тексте
     *
     * @param $content
     * @param $id
     * @param $title
     * @param $size
     * @param $num
     * @return string|string[]
     */
    public function getDescription($content, $id, $title, $size, $type)
    {
        $keyList = ['$id', '$title', '$size', '$type'];
        $valueList = [$id, $title, $size, $type];

        return str_replace($keyList, $valueList, $content);
    }

    /**
     * Добваляет CDATA секцию в SimpleXMLElement блок
     *
     * @param   $xml
     * @param   $content    string
     * @return
     */
    public function appendCDATASection($xml, $content)
    {
        $dom = dom_import_simplexml($xml);
        $owner = $dom->ownerDocument;
        $dom->appendChild($owner->createCDATASection($content));

        return $xml;
    }

    private function createUniqueImages($id, $model)
    {
        $assetImages = array();

        // если изоброжений отсустует, то выдаем пустой массив
        if (!is_null($model->images) and count($model->images) == 0) return $assetImages;

        $layout = Layout::where('id', $model->layout_id)->first(); // получаем шаблоные данные для офферы

        $logo = Image::make($layout->logo->getLocalPath())
                ->resizeCanvas(150, 150)
                ->opacity(40);// logo с прозрачностью 60%


        foreach ($model->images as $image) {
            $img = Image::make($image->getLocalPath());
            $pathDir = "app/uploads/public/images/$model->avito_id/$id";
            $storagePathDir = storage_path($pathDir);
            if (!file_exists($storagePathDir)) mkdir($storagePathDir, 0755, TRUE); // если путь не сущ, то создаем

            $width = $img->getWidth();
            $height = $img->getHeight();


            $img->flip()                 // Отзеракливание
            ->brightness(rand(-5, 5))   // яркость
            ->sharpen(rand(0, 20))      // резкость
            ->contrast(rand(-5, 5))     // контраст
            ->resize($width - rand(0, 15), $height - rand(0, 15)) // размер
            ->insert($logo, 'top-left', rand(0, $width - 150), rand(0, $height - 150)); // Лого в изображении

            $img->save("$storagePathDir/$img->basename");

            $assetImages[] = [
                "asset" => asset("storage/$pathDir/$img->basename"),
                "path" => "$storagePathDir/$img->basename"];
        }

        return $assetImages;
    }

    /**
     * Создает в SimpleXMLElement, блок "Image"
     * Прикрепляет URL с уникальной копией изображения
     *
     * @return array
     */
    private function addXMLUniqueImages($id, $xml, $model)
    {
        foreach ($this->createUniqueImages($id, $model) as $assetImage) {
            $imageList[] = $xml->addChild('Image')                 // Добавления Xml блока Image
            ->addAttribute('url', $assetImage['asset']); // Добавление Url в XML Image блок
        }

        return $imageList;
    }


    /**
     * Создает SimpleXMLElement Офферу
     *
     * @param $model    Offer
     * @param $layout   Layout
     * @param $xml
     * @param $status   string
     */
    public function addXMLFieldAd($model, $layout, $xml, $status = null)
    {
        $status = is_null($status) ? $model->comment : $status; // male, female, unisex
        $titleList = input('Offer')['title']; // Список значений из формы поля title
        $titleStr = implode(', ', $titleList); // Получаем Список в виде строки

        if ($status == 'female') $goodsType = 'Женщина';
        elseif ($status == 'male') $goodsType = 'Мужчина';

        $counter = 0; // счетчик

        ini_set('max_execution_time', 900); // Увеличим время ожидания загрузки
        foreach ($this->sizeFootwear[$status] as $sizeId => $sizeValue) {
            $ad = $xml->addChild('Ad');
            $avito_id = $model->avito_id . $sizeId;
            $content = $this->getDescription($layout->description, $avito_id, $titleList, $sizeValue, $counter);

            $id = $ad->addChild('Id', $avito_id);
            $category = $ad->addChild('Category', $layout->category);
            $ad_type = $ad->addChild('AdType', $layout->ad_type);
            $goods_type = $ad->addChild('GoodsType', $goodsType);
            $apparel = $ad->addChild('Apparel', $layout->apparel);
            $sizeFootwear = $ad->addChild('Size', $sizeValue);
            $address = $ad->addChild('Address', $layout->address);
            $title = $ad->addChild('Title', $titleStr);
            $description = $ad->addChild('Description');
            $description = $this->appendCDATASection($description, $content);
            $date_begin = $ad->addChild('DateBegin', $layout->begin_at);
            $price = $ad->addChild("Price", $model->price);
            $images = $ad->addChild('Images');
            $images = $this->addXMLUniqueImages($sizeId, $images, $model);

            if ($counter == (count($titleList) - 1)) $counter = 0;
            else $counter++;
        }
        ini_set('max_execution_time', 30); // default
    }


    /**
     *
     * @param $model Offer
     */
    public function formAfterSave($model)
    {
        return;

        $layout = Layout::where('id', $model->layout_id)->first(); // Получаем Родительский класс

        $status = input('Offer')['comment']; // берем данные из формы, поля "comment"
        $xmlPath = storage_path('app/offers.xml'); // путь к xml файлу


        if (file_exists($xmlPath)) { // если xml файл существует
            $xml = new SimpleXMLElement($xmlPath, null, True);

            if ($status == 'unisex') {
                $this->addXMLFieldAd($model, $layout, $xml, 'male');
                $this->addXMLFieldAd($model, $layout, $xml, 'female');
            } else {
                $this->addXMLFieldAd($model, $layout, $xml, $status);
            }

            $xml->saveXML($xmlPath);
        }
    }


    /**
     * События при нажатии на кнопку "Создать офферы"
     *
     * @return mixed
     * @throws \Exception
     */

    public function onCreateOffers()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $products = array();

            $offerSortEndDate = new Product\GenerateOffersSortBetween();
            $offerSortInterval = new Product\GenerateOffersSortDay();
            $offerSortEndDateAndRand = new Product\GenerateOffersSortRandom();
            foreach ($checkedIds as $recordId) {
                if (!$product = Product::find($recordId)) { // если выбранный продукт существует
                    continue;
                }
                $products[] = $product;
                if(!$product->getLayout()->on_interval) {
                    $offerSortEndDate->saveProduct($product);
                }
            }
            $offerSortEndDateAndRand->addRandList($products);
            foreach ($products as $product) {
                if(!$product->getLayout()->on_interval) {
                    if($product->getLayout()->on_random)
                        $offerSortEndDateAndRand->make($product)->run();
                    else $offerSortEndDate->make($product)->run();
                }
                else $offerSortInterval->make($product)->run();
            }

            Flash::success('Офферы созданы');
        }
        else {
            Flash::error('Не получилось создать');
        }
        return $this->listRefresh();
    }

    private function createOrUpdateOffers(Product $product, Layout $layout, Carbon $dateTime, $status=null)
    { // TODO: Разделить на класс
        $endAt = (clone $dateTime)->add(new \DateInterval('P1M'));

        $status = is_null($status) ? $product->comment : $status; // male, female, unisex

        if ($status == 'female') $sex = 'women';
        elseif ($status == 'male') $sex = 'men';

        $titleList = explode(',', $product->title); // Список значений из формы поля title
        $titleStr = implode(', ', $titleList); // Получаем Список в виде строки

        $addressList = explode(',', $layout->address);

        $interval = 'P1D'; // Интервал выпуска продукта(в минутах)

        // счетчик
        $counterTitle = 0;
        $counterAddress = 0;
        $counterImg = 0;

        ini_set('max_execution_time', 900); // Увеличим время ожидания загрузки

        foreach ($this->sizeFootwear[$status] as $sizeId => $sizeValue) {
            $sizeConfigKey = array_search($sizeValue, FilterConfig::$sizeClothing[$sex]);

            $assetImages = $this->createUniqueImages($counterTitle, $product);

            $address = "Россия, $layout->city, ". $addressList[$counterAddress];
            $title = $titleList[$counterTitle];

            $avito_id = $product->avito_id . $sizeId;
            $type = Product::TYPE[$product->type];
            $content = $this->getDescription($layout->description, $avito_id, $title, $sizeValue, $type);

            $isOfferExists = Offer::where('avito_id', $avito_id)->exists();
            $a = Offer::updateOrCreate(// создание или обновление модели
                [// проверка на существование модели
                    'avito_id' => $avito_id
                ],
                [
                    'avito_id' => $avito_id,
                    'begin_at' => $dateTime->format('Y-m-d H:i:s'),
                    'end_at' => $endAt->format('Y-m-d H:i:s'),
                    'listing_fee' => '',
                    'ad_status' => '',
                    'allow_email' => '',
                    'manager_name' => '',
                    'contact_phone' => '',
                    'address' => $address,
                    'latitude' => '',
                    'longitude' => '',
                    'category' => $layout->category,
                    'goods_type' => ['category' => ['clothes' => $sex."s_clothing" ]],
                    'ad_type' => $layout->ad_type,
                    'apparel' => ["category"=>["goods_type"=>[$sex.'s_clothing'=>$layout->apparel]]],
                    'size' => [
                        "category"=>["goods_type"=>["apparel"=>[$sex.'s_clothing_footwear' => $sizeConfigKey]]]
                    ],
                    'title' => $title,
                    'description' => $content,
                    'price' => $product->price,
                    'video_url' => '',
                ]
            );
            if (!$isOfferExists) {
                $counter = $counterImg;
                $this->addImage($a, $counter, $assetImages);
                $this->addImage($a, ++$counter, $assetImages);
            }

            $dateTime->add(new \DateInterval($interval));
            $endAt->add(new \DateInterval($interval));

            if ($counterTitle == (count($titleList) - 1)) $counterTitle = 0;
            else $counterTitle++;
            if ($counterAddress == (count($addressList) - 1)) $counterAddress = 0;
            else $counterAddress++;
            if ($counterImg == (count($assetImages) - 1)) $counterImg = 0;
            else $counterImg++;
        }
        ini_set('max_execution_time', 30); // default
    }

    private function addImage(Offer $offer, $counter, array $imagesList)
    {
        $countImages = count($imagesList) - 1;
        if($counter > $countImages) {
            --$counter;
            $counter = $counter - $countImages;
        }

        $file = new File;
        $file->fromFile($imagesList[$counter]["path"]);
        $offer->images()->add($file);
    }
}
