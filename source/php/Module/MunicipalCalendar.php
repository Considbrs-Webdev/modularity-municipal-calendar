<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar\Module;

/**
 * Class MunicipalCalendar
 * @package ModularityMunicipalCalendar\Module
 */
class MunicipalCalendar extends \Modularity\Module
{
    public $slug = 'municipal-calendar';
    public $supports = [];

    public function init(): void
    {
        $this->nameSingular = __('MunicipalCalendar', 'modularity-municipal-calendar');
        $this->namePlural = __('MunicipalCalendar', 'modularity-municipal-calendar');
        $this->description = __('A municipal-calendar module.', 'modularity-municipal-calendar');
    }

    /**
     * Data array
     * @return array $data
     */
    public function data(): array
    {
        $data = [];

        // Append field config
        $data = array_merge($data, (array) \Modularity\Helper\FormatObject::camelCase(
            $this->getFields(),
        ));

        return $data;
    }

    /**
     * Blade Template
     * @return string
     */
    public function template(): string
    {
        return 'municipal-calendar.blade.php';
    }

    /**
     * Style - Register & adding css
     * @return void
     */
    public function style(): void
    {
        $this->wpEnqueue?->add('css/modularity-municipal-calendar.css', [], '1.0.0');
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}

