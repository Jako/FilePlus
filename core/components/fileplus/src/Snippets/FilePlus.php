<?php
/**
 * FilePlus Snippet as alternative to the File+ TV Output Type
 *
 * @package fileplus
 * @subpackage snippet
 */

namespace TreehillStudio\FilePlus\Snippets;

use modTemplateVar;
use xPDO;

class FilePlus extends Snippet
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'value' => '',
            'tvname' => '',
            'source::int' => 0,
            'docid::int' => (isset($this->modx->resource)) ? $this->modx->resource->get('id') : 0,
            'tpl::allowEmpty' => 'FilePlus.file',
            'debug::bool' => $this->fileplus->getOption('debug')
        ];
    }

    /**
     * Execute the snippet and return the result.
     *
     * @return string
     */
    public function execute()
    {
        $value = $this->getProperty('value');
        $tvname = $this->getProperty('tvname');
        $docid = $this->getProperty('docid');
        $debug = $this->getProperty('debug');

        $tv = null;
        if (!$value) {
            // The value is retrieved from the template variable
            /** @var modTemplateVar $tv */
            $tv = $this->modx->getObject('modTemplateVar', ['name' => $tvname]);
            if (!$tv) {
                if ($debug) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Template Variable '$tvname' not found.", '', 'File+');
                    return "Template Variable '$tvname' not found.";
                }
                return '';
            }
            // Get the raw content of the TV
            $value = $tv->getValue($docid);
            $value = $tv->processBindings($value, $docid);
        }

        // Render the template chunk or return the url of the file without a template chunk
        $properties = $this->getProperties();
        unset($properties['value'], $properties['tpl'], $properties['debug']);
        return $this->fileplus->getFileOutput($value, array_merge($properties, [
            'outputChunk' => $this->getProperty('tpl'),
        ]), $tv);
    }
}
