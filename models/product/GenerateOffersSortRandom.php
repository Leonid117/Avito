<?php namespace Drabbit\Avito\Models\Product;


use Drabbit\Avito\Controllers\Products;
use Drabbit\Avito\Models\Product;

class GenerateOffersSortRandom extends GenerateOffers
{
    public $productLayoutList;
    private $collection;
    private $intervalPhased;
    private $productDateTimeListRand;
    public $counterDateTime = array();

    public function __construct()
    {
        $this->productLayoutList = null;
        $this->productDateTimeListRand = null;
    }

    public function make(Product $product)
    {
        parent::make($product);

        $this->configPhasedInterval();

        return $this;
    }

    public function isStatus($product)
    {
        return !$product->getLayout()->on_interval and $product->getLayout()->on_random;
    }

    public function addRandList($products)
    {
        foreach ($products as $product) {
            if($this->isStatus($product)) $this->saveProduct($product);
        }

        foreach ($products as $product) {
            if(!$this->isStatus($product)) continue;

            $this->product = $product;
            $this->layout = $product->getLayout();
            $this->dateTime = is_null($this->productDateTimeListRand) ? $this->layout->begin_at : end($this->productDateTimeListRand[$this->layout->id]);
            $this->endAt = (clone $this->dateTime)->add(new \DateInterval('P1M'));
            $this->configPhasedInterval();
            $status = $this->product->comment; // male, female, unisex
            if($status == 'unisex') {
                $this->nextOffer('female');
                $this->nextOffer('male');
            }
            else $this->nextOffer();

            foreach ($this->productDateTimeListRand as $layoutId => $values) {
                $this->counterDateTime[$layoutId] = 0;
            }
            $this->dateTime = $this->getDateTimeRand();
        }

        foreach ($products as $product) {
            if(!$this->isStatus($product)) continue;
            shuffle($this->productDateTimeListRand[$product->getLayout()->id]);
        }

    }

    public function nextOffer($status = null)
    {
        $status = is_null($status) ? $this->product->comment : $status; // male, female, unisex
        foreach ($this->getSizeFootwearList($status) as $sizeId => $sizeValue)
        {
            $this->addProductDateTimeRand();
            $this->addIntervalInDateTimeRand();
        }
    }

    public function addProductDateTimeRand()
    {
        $productLayoutId = $this->layout->id;
        $this->productDateTimeListRand[$productLayoutId][] = $this->dateTime->copy();
    }

    protected function addIntervalInDateTimeRand()
    {
        $addDateTimeInterval = $this->dateTime->timestamp + $this->intervalPhased[$this->layout->id];
        $addEndAtInterval = $this->endAt->timestamp + $this->intervalPhased[$this->layout->id];
        $this->dateTime->setTimestamp($addDateTimeInterval);
        $this->endAt->setTimestamp($addEndAtInterval);

        $this->validateTimeStampForRelevance();
    }

    public function beginCreateOrUpdateOffers()
    {
        $this->dateTime = $this->getDateTimeRand();
        $this->endAt = $this->dateTime->copy();
        $this->endAt->addMonth();
    }

    protected function addIntervalInDateTimeOffer()
    {
        $this->counterDateTime[$this->layout->id]++;
    }

    private function getCounterDateTime()
    {
        return $this->counterDateTime[$this->layout->id];
    }

    private function getDateTimeRand()
    {
        return $this->productDateTimeListRand[$this->layout->id][$this->getCounterDateTime()];
    }

    private function addLayoutList()
    {
        $productId = $this->product->id;
        $productLayoutId = $this->layout->id;

        if(is_null($this->productLayoutList)) {
            // получаем дату начала из модели "Шаблоны"
            $this->productLayoutList[$productLayoutId][$productId] = $this->product;
        }
        elseif(!array_key_exists($productLayoutId, $this->productLayoutList)) {
            $this->productLayoutList[$productLayoutId][$productId] = $this->product;
        }
        elseif(!array_key_exists($productId, $this->productLayoutList[$productLayoutId])) {
            $this->productLayoutList[$productLayoutId][$productId] = $this->product;
        }
    }
    private function addProductCollection()
    {
        foreach ($this->productLayoutList as $key => $layout)
        {
            $this->collection[$key] = collect($layout);
        }
    }

    public function saveProduct(Product $product)
    {
        $this->product = $product;
        $this->layout = $product->getLayout();
        $this->addLayoutList();
        $this->addProductCollection();
    }

    public function getCountSizeFootwear($products)
    {
        $count = 0;
        foreach ($products as $product){
            $status =  $product->comment; // male, female, unisex

            if($status == 'unisex')
                $count = count(Products::SIZE_FOOTWEAR['male']) + count(Products::SIZE_FOOTWEAR['female']);

            else $count += count(Products::SIZE_FOOTWEAR[$status]);
        }

        return $count;
    }

    public function configPhasedInterval()
    {
        foreach ($this->collection as $layoutId => $products)
        {
            $stamp = $this->layout->end_at_cycle->timestamp - $this->layout->begin_at->timestamp;
            $this->intervalPhased[$layoutId] = $stamp / $this->getCountSizeFootwear($products);
        }
    }

}