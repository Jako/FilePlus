<?php
/**
 * Abstract plugin
 *
 * @package fileplus
 * @subpackage plugin
 */

namespace TreehillStudio\FilePlus\Plugins;

use modX;
use TreehillStudio\FilePlus\FilePlus;

/**
 * Class Plugin
 */
abstract class Plugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var FilePlus $fileplus */
    protected $fileplus;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    /**
     * Plugin constructor.
     *
     * @param $modx
     * @param $scriptProperties
     */
    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties = &$scriptProperties;
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('fileplus.core_path', null, $this->modx->getOption('core_path') . 'components/fileplus/');
        $this->fileplus = $this->modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
            'core_path' => $corePath
        ]);
    }

    /**
     * Run the plugin event.
     */
    public function run()
    {
        $init = $this->init();
        if ($init !== true) {
            return;
        }

        $this->process();
    }

    /**
     * Initialize the plugin event.
     *
     * @return bool
     */
    public function init()
    {
        return true;
    }

    /**
     * Process the plugin event code.
     *
     * @return mixed
     */
    abstract public function process();
}
