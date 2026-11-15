<?php
/**
 * FilePlus Snippet as alternative to the File+ TV Output Type
 *
 * @package fileplus
 * @subpackage snippet
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

use TreehillStudio\FilePlus\Snippets\FilePlus;

$corePath = $modx->getOption('fileplus.core_path', null, $modx->getOption('core_path') . 'components/fileplus/');
/** @var FilePlus $fileplus */
$fileplus = $modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
    'core_path' => $corePath
]);

$snippet = new FilePlus($modx, $scriptProperties);
if ($snippet instanceof TreehillStudio\FilePlus\Snippets\FilePlus) {
    return $snippet->execute();
}
return 'TreehillStudio\FilePlus\Snippets\FilePlus class not found';
