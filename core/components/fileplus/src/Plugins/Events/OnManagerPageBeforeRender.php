<?php
/**
 * @package fileplus
 * @subpackage plugin
 */

namespace TreehillStudio\FilePlus\Plugins\Events;

use TreehillStudio\FilePlus\Plugins\Plugin;

class OnManagerPageBeforeRender extends Plugin
{
    public function process()
    {
        $this->modx->controller->addLexiconTopic('fileplus:default');
        $this->fileplus->includeScriptAssets();
    }
}
