<?php
/**
 * @package fileplus
 * @subpackage plugin
 */

namespace TreehillStudio\FilePlus\Plugins\Events;

use TreehillStudio\FilePlus\Plugins\Plugin;

class OnTVOutputRenderPropertiesList extends Plugin
{
    public function process()
    {
        $this->modx->event->output($this->fileplus->getOption('corePath') . 'elements/tv/output/options/');
    }
}
