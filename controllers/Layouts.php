<?php namespace Drabbit\Avito\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Intervention\Image\Facades\Image;
use System\Models\File;

/**
 * Layouts Back-end Controller
 */
class Layouts extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
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

        BackendMenu::setContext('Drabbit.Avito', 'avito', 'layouts');
    }

    private function setFieldLogo($model)
    {
        $logoFile = $model->logo;
        if($logoFile){
            $logoPath = $logoFile ? $logoFile->getLocalPath() : null;
            $logoImage = Image::make($logoPath);

            $isSizeValidate = $logoFile->getHeightAttribute() > 150 or $logoFile->getWidthAttribute() > 150;
            if($isSizeValidate) $logoImage->resize(150, 150);

            $logoImage->save();
        }
    }

    public function formAfterSave($model)
    {
        $this->setFieldLogo($model);
    }
}
