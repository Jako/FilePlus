<?php
/**
 * File+ Input Render
 *
 * @package fileplus
 * @subpackage input_render
 */

class FilePlusInputRender extends modTemplateVarInputRender
{
    /**
     * Return the template path to load
     *
     * @return string
     */
    public function getTemplate()
    {
        $corePath = $this->modx->getOption('fileplus.core_path', null, $this->modx->getOption('core_path') . 'components/fileplus/');
        return $corePath . 'elements/tv/input/tpl/fileplus.render.tpl';
    }

    /**
     * Get lexicon topics
     *
     * @return array
     */
    public function getLexiconTopics()
    {
        return ['fileplus:default'];
    }

    /**
     * Process Input Render
     *
     * @param string $value
     * @param array $params
     * @return void
     */
    public function process($value, array $params = [])
    {
        $corePath = $this->modx->getOption('fileplus.core_path', null, $this->modx->getOption('core_path') . 'components/fileplus/');
        /** @var FilePlus $fileplus */
        $fileplus = $this->modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
            'core_path' => $corePath
        ]);
        $fileplus->includeScriptAssets();

        // Get Media Source
        /** @var modMediaSource $source */
        $source = $this->tv->getSource(($this->modx->resource) ? $this->modx->resource->get('context_key') : 'mgr');
        if (!$source || !$source->getWorkingContext()) {
            return;
        }
        $source->setRequestProperties($_REQUEST);
        $source->initialize();

        // For migxResourceMediaPath snippet
        $this->setPlaceholder('mediasource', $source->get('id'));

        // Prepare TV config
        $tvConfig = new stdClass();
        $allowBlank = $params['allowBlank'] ?? true;
        $tvConfig->allowBlank = !($allowBlank === 'false' || $allowBlank === 0 || $allowBlank === false);
        $tvConfig->allowedExtensions = $fileplus->getAllowedExtensions($params['allowedFileTypes'] ?? '');
        $tvConfig->uploadPath = trim((string)($params['uploadPath'] ?? ''));
        $tvConfig->titleOn = $this->isTrue($params['allowTitle'] ?? false);
        $tvConfig->descriptionOn = $this->isTrue($params['allowDescription'] ?? false);
        $tvConfig->mediaSource = $source->get('id');
        $tvConfig->tvId = $this->tv->get('id');

        $this->setPlaceholder('tvConfig', json_encode($tvConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG));

        $this->tv->value = $fileplus->prepareTvValue($this->tv->value);
    }

    /**
     * Check a boolean input option value, combo-boolean options are saved as true/false strings
     *
     * @param mixed $value
     * @return bool
     */
    private function isTrue($value)
    {
        return in_array($value, [true, 1, '1', 'true'], true);
    }
}

return 'FilePlusInputRender';
