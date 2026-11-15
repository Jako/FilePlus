<?php
/**
 * File+ Input Options Render
 *
 * @package fileplus
 * @subpackage inputoptions_render
 */

/** @var modX $modx */
$corePath = $modx->getOption('fileplus.core_path', null, $modx->getOption('core_path') . 'components/fileplus/');
/** @var FilePlus $fileplus */
$fileplus = $modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
    'core_path' => $corePath
]);

return $modx->smarty->fetch($corePath . 'elements/tv/input/tpl/fileplus.options.tpl');
