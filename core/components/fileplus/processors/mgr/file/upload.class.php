<?php
/**
 * Upload files processor
 *
 * @package fileplus
 * @subpackage processors
 */

use TreehillStudio\FilePlus\FilePlus;

// Compatibility between 2.x/3.x
if (file_exists(MODX_PROCESSORS_PATH . 'browser/file/upload.class.php')) {
    require_once MODX_PROCESSORS_PATH . 'browser/file/upload.class.php';
} elseif (!class_exists('modBrowserFileUploadProcessor')) {
    class_alias(\MODX\Revolution\Processors\Browser\File\Upload::class, \modBrowserFileUploadProcessor::class);
}

class FilePlusBrowserFileUploadProcessor extends modBrowserFileUploadProcessor
{
    /** @var FilePlus $fileplus */
    public $fileplus;

    /**
     * {@inheritDoc}
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    public function __construct(modX &$modx, array $properties = [])
    {
        parent::__construct($modx, $properties);

        $corePath = $this->modx->getOption('fileplus.core_path', null, $this->modx->getOption('core_path') . 'components/fileplus/');
        $this->fileplus = $this->modx->getService('fileplus', 'FilePlus', $corePath . 'model/fileplus/', [
            'core_path' => $corePath
        ]);
    }

    public function initialize()
    {
        $this->setDefaultProperties([
            'source' => $this->modx->getOption('default_media_source', null, 1),
            'path' => false,
        ]);
        return true;
    }

    public function getLanguageTopics()
    {
        $langs = parent::getLanguageTopics();
        $langs[] = 'fileplus:default';
        return $langs;
    }

    public function process()
    {
        if (!$this->getSource()) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        if (!$this->source->checkPolicy('create')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        if (count($_FILES) < 1) {
            return $this->failure($this->modx->lexicon('fileplus.error_file_not_set'));
        }

        // The input options are read from the template variable, so that they can't be changed in the request
        $inputProperties = $this->getTvInputProperties();

        // The upload path input option of the template variable overrides the fileplus.upload_path system setting
        $path = trim((string)($inputProperties['uploadPath'] ?? ''));
        $path = ($path !== '') ? $path : $this->fileplus->getOption('upload_path', null, '');
        $path = $this->fileplus->parsePathPlaceholders($path, $this->getProperty('resource_id'), $this->getProperty('tv_id'));
        $path = trim(preg_replace('/[.]{2,}/', '', $path), '/');
        if ($path !== '') {
            $this->source->createContainer($path, '');
            // Unset errors to prevent issues if the container already exists.
            $this->source->errors = [];
        }
        $files = $this->prepareFiles();
        $error = $this->checkFiles($files, $this->fileplus->getAllowedExtensions($inputProperties['allowedFileTypes'] ?? ''));
        if ($error) {
            return $this->failure($error);
        }
        // The uploaded file is moved by the media source
        $file = reset($files);
        $size = (int)($file['size'] ?? 0);
        $success = $this->source->uploadObjectsToContainer($path, $files);
        if (empty($success)) {
            $msg = '';
            $errors = $this->source->getErrors();
            // Unset $errors['name'] to prevent issues if the container already exists.
            if (isset($errors['name'])) {
                unset($errors['name']);
            }
            if (count($errors) > 0) {
                foreach ($errors as $k => $msg) {
                    $this->modx->error->addField($k, $msg);
                }
                return $this->failure($msg);
            }
        }

        $url = ($path === '') ? $file['name'] : $path . '/' . $file['name'];
        $url = preg_replace('/\/{2,}/', '/', $url);

        $sourceProperties = $this->source->getPropertyList();
        return $this->success('', [
            'relativeUrl' => $url,
            'fullRelativeUrl' => ($sourceProperties['baseUrl'] ?? '') . $url,
            'url' => $this->fileplus->getSourceObjectUrl($this->source, $url),
            'size' => $size,
        ]);
    }

    /**
     * Get the input properties of the File+ template variable in the request
     *
     * @return array
     */
    private function getTvInputProperties()
    {
        $tvId = (int)$this->getProperty('tv_id');
        /** @var modTemplateVar $tv */
        $tv = ($tvId) ? $this->modx->getObject('modTemplateVar', ['id' => $tvId, 'type' => 'fileplus']) : null;
        if (!$tv) {
            return [];
        }
        $properties = $tv->get('input_properties');
        return is_array($properties) ? $properties : [];
    }

    /**
     * Check that the uploaded files have an allowed file extension
     *
     * @param array $files
     * @param string[] $allowedExtensions
     * @return string The error message, if a file has a file extension that is not allowed
     */
    private function checkFiles(array $files, array $allowedExtensions)
    {
        foreach ($files as $file) {
            if (!empty($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
                return $this->modx->lexicon('fileplus.error_upload');
            }
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($extension === '' || ($allowedExtensions && !in_array($extension, $allowedExtensions, true))) {
                return $this->modx->lexicon('fileplus.error_extension', [
                    'extension' => $extension,
                    'allowed' => implode(', ', $allowedExtensions)
                ]);
            }
        }
        return '';
    }

    /**
     * Prepare the file names
     *
     * @return array
     */
    private function prepareFiles()
    {
        $files = $_FILES;

        foreach ($files as &$file) {
            $pathInfo = pathinfo($file['name']);
            $ext = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $pathInfo['extension'] ?? ''));

            $filename = $this->modx->filterPathSegment($pathInfo['filename']);
            $filename = preg_replace('/[^A-Za-z0-9_-]/', '', $filename);
            $filename = preg_replace('/-{2,}/', '-', $filename);
            $filename = trim($filename, '-');
            if ($filename === '') {
                $filename = 'file-' . time();
            }

            $file['name'] = ($ext !== '') ? $filename . '.' . $ext : $filename;
        }
        return $files;
    }
}

return 'FilePlusBrowserFileUploadProcessor';
