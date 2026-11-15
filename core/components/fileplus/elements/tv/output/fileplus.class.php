<?php
/**
 * File+ Output Render
 *
 * @package fileplus
 * @subpackage output_render
 */

class FilePlusOutputRender extends modTemplateVarOutputRender
{
    /**
     * Process Output Render
     *
     * @param string $value
     * @param array $params
     * @return string
     */
    public function process($value, array $params = [])
    {
        $corePath = $this->modx->getOption('fileplus.core_path', null, $this->modx->getOption('core_path') . 'components/fileplus/');
        /** @var FilePlus $fileplus */
        $fileplus = $this->modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
            'core_path' => $corePath
        ]);

        $params = array_merge([
            'docid' => ($this->modx->resource) ? $this->modx->resource->get('id') : 0
        ], $params);

        return $fileplus->getFileOutput($value, $params, $this->tv);
    }
}

return 'FilePlusOutputRender';
