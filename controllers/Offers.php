<?php namespace Drabbit\Avito\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Drabbit\Avito\Models\Offer;
use Drabbit\Avito\Models\Offer\GenerateXML;
use Intervention\Image\ImageUnique;
use October\Rain\Support\Facades\Flash;

/**
 * Offers Back-end Controller
 */
class Offers extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Drabbit.Avito', 'avito', 'offers');
    }

    /**
     * Создает XML файл SimpleXMLElement Офферы
     *
     */
    public function onGenerateOffers()
    {
        if (count($offers = Offer::all())) {
            $xml = new GenerateXML($offers);
            $xml->make()->save();

            Flash::success('XML file успешно создан');
        } else Flash::error('Создайте Офферы для генерации XML file');

        return $this->listRefresh(); #october
    }

    public function onDeleteAll()
    {
        Offer::deleteAll();
        Flash::success('Данные удалены');
        return $this->listRefresh();
    }
}
