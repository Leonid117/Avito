<?php namespace Drabbit\Avito\Models\Offer;

use Drabbit\Avito\Models\Offer;
use SimpleXMLElement;

class GenerateXML
{
    /**
     * @var Offer
     */
    private $offers;

    private $offersList = array();

    const   ID = 'id', CATEGORY = 'category', AD_TYPE = 'ad_type', GOODS_TYPE = 'goods_type', APPAREL = 'apparel',
            SIZE_FOOTWEAR = 'size_footwear', ADDRESS = 'address', TITLE = 'title', DESCRIPTIONS = 'description',
            BEGIN_AT = 'begin_at', PRICE = 'price', IMAGES = 'images', CONDITION='condition';


    private $xmlBasePath;
    private $xmlPath;
    private $xml;

    public function __construct($offers)
    {
        $this->offers = $offers;
        $this->configOffers();
        $this->configXML();
    }

    private function configOffers()
    {
        $this->offers = count($this->offers) > 1 ? $this->offers : array($this->offers);

        foreach ($this->offers as $offer) {
            $this->offersList[$offer->avito_id] = [
                self::ID            => $offer->avito_id,
                self::CATEGORY      => $offer->getCategory(),
                self::AD_TYPE       => $offer->getAdType(),
                self::GOODS_TYPE    => $offer->getGoodsType(),
                self::CONDITION     => $offer->getCondition(),
                self::APPAREL       => $offer->getApparel(),
                self::SIZE_FOOTWEAR => $offer->getSizeFootwear(),
                self::ADDRESS       => $offer->address,
                self::TITLE         => $offer->getTitle(),
                self::DESCRIPTIONS  => $offer->description,
                self::BEGIN_AT      => $offer->begin_at->toIso8601String(),
                self::PRICE         => $offer->price,
                self::IMAGES        => $offer->images,
            ];
        }
    }

    private function configXML()
    {
        $this->xmlBasePath = plugins_path('drabbit/avito/offers_defaults.xml');

        $this->xmlPath = storage_path('app/uploads/public/offers.xml'); //storage_path('app/offers.xml');

        $this->xml = new SimpleXMLElement($this->xmlBasePath, null, True);
    }

    private function addImages($xml, $offer)
    {
        foreach ($offer['images'] as $image) {
            $list[] = $xml->addChild('Image')                     // Добавления Xml блока Image
                          ->addAttribute('url', $image->path);    // Добавление Url в XML Image блок
        }

        return $list ?? [];
    }

    public function appendCDATASection($xml, $content)
    {
        $dom = dom_import_simplexml($xml);
        $owner = $dom->ownerDocument;
        $dom->appendChild($owner->createCDATASection($content));

        return $xml;
    }

    public function make()
    {
        foreach ($this->offersList as $offer) {
            $ad = $this->xml->addChild('Ad');

            $id             = $ad->addChild('Id',         $offer[self::ID]);
            $category       = $ad->addChild('Category',   $offer[self::CATEGORY]);
            $ad_type        = $ad->addChild('AdType',     $offer[self::AD_TYPE]);
            $goods_type     = $ad->addChild('GoodsType',  $offer[self::GOODS_TYPE]);
            $condition      = $ad->addChild('Condition',  $offer[self::CONDITION]);
            $apparel        = $ad->addChild('Apparel',    $offer[self::APPAREL]);
            $sizeFootwear   = $ad->addChild('Size',       $offer[self::SIZE_FOOTWEAR]);
            $address        = $ad->addChild('Address',    $offer[self::ADDRESS]);
            $title          = $ad->addChild('Title',      $offer[self::TITLE]);
            $description    = $ad->addChild('Description');
            $description    = $this->appendCDATASection($description, $offer[self::DESCRIPTIONS]);
            $date_begin     = $ad->addChild('DateBegin',  $offer[self::BEGIN_AT]);
            $price          = $ad->addChild("Price",      $offer[self::PRICE]);
            $images         = $ad->addChild('Images');
            $images         = $this->addImages($images, $offer);
        }
        return $this;
    }

    public function save()
    {
        if(file_exists($this->xmlPath)) unlink($this->xmlPath);
        $this->xml->saveXML($this->xmlPath);
    }

}