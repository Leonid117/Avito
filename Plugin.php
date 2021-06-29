<?php namespace Drabbit\Avito;

use Backend;
use System\Classes\PluginBase;

/**
 * avito Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'avito',
            'description' => 'No description provided yet...',
            'author'      => 'drabbit',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Drabbit\Avito\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'drabbit.avito.some_permission' => [
                'tab' => 'avito',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'avito' => [
                'label'       => 'avito',
                'url'         => Backend::url('drabbit/avito/Products'),
                'icon'        => 'icon-leaf',
                'order'       => 500,
                'sideMenu'    => [
//                    TODO: Переименовать название классов
                    'offers' => [
                        'label'     => 'Оффера',
                        'icon'      => 'icon-list-alt',
                        'url'       => Backend::url('drabbit/avito/Offers')
                    ],
                    'products' => [
                        'label'     => 'Товары',
                        'icon'      => 'icon-list-alt',
                        'url'       => Backend::url('drabbit/avito/Products')
                    ],
                    'layouts' => [
                        'label'     => 'Шаблоны',
                        'icon'      => 'icon-list-alt',
                        'url'       => Backend::url('drabbit/avito/Layouts')
                    ]
                ]
            ],
        ];
    }
}
