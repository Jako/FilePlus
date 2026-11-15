<?php
/**
 * File+ Runtime Hooks
 *
 * @package fileplus
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'TreehillStudio\FilePlus\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('fileplus.core_path', null, $modx->getOption('core_path') . 'components/fileplus/');
/** @var FilePlus $fileplus */
$fileplus = $modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
    'core_path' => $corePath
]);

if ($fileplus) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className . ' could not be initialized!', '', 'FilePlus Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className . ' was not found!', '', 'FilePlus Plugin');
    }
}

return;
