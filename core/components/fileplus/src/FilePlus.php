<?php
/**
 * File+
 *
 * Copyright 2026 by Thomas Jakobi <office@treehillstudio.com>
 *
 * @package fileplus
 * @subpackage classfile
 */

namespace TreehillStudio\FilePlus;

use modMediaSource;
use modTemplateVar;
use modX;
use stdClass;
use xPDO;

/**
 * Class FilePlus
 */
class FilePlus
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'fileplus';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'FilePlus';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.0.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * The cached context keys of resources
     * @var array $resourceContextKeys
     */
    private $resourceContextKeys = [];

    /**
     * The cached map of the media source urls
     * @var array|null $sourceMap
     */
    private $sourceMap = null;

    /**
     * FilePlus constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'processorsPath' => $corePath . 'processors/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'img/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ], $options);

        // Add default options
        $this->options = array_merge($this->options, [
            'debug' => (bool)$this->modx->getOption($this->namespace . '.debug', null, '0') == 1,
            'modxversion' => $modxversion['version'],
        ]);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }

    /**
     * Get a map of MediaSource id => baseUrl
     *
     * @return array
     */
    private function loadSourceMap()
    {
        // The map is loaded once per request, the scripts are included on every manager page and by each template variable
        if ($this->sourceMap !== null) {
            return $this->sourceMap;
        }
        $sources = $this->modx->getCollection('sources.modMediaSource');
        $sourceMap = [];
        foreach ($sources as $source) {
            /** @var modMediaSource $source */
            $source->initialize();
            $sourceMap[$source->get('id')] = new stdClass();
            $sourceMap[$source->get('id')]->url = $source->getBaseUrl();
        }
        $this->sourceMap = $sourceMap;
        return $sourceMap;
    }

    /**
     * Register javascripts in the controller
     */
    public function includeScriptAssets()
    {
        // The scripts are included once per request, also if the page contains several File+ template variables
        if (!empty($this->options['scriptsIncluded'])) {
            return;
        }
        $this->options['scriptsIncluded'] = true;

        $assetsUrl = $this->getOption('assetsUrl');
        $jsUrl = $this->getOption('jsUrl') . 'mgr/';
        $jsSourceUrl = $assetsUrl . '../../../source/js/mgr/';
        $cssUrl = $this->getOption('cssUrl') . 'mgr/';
        $cssSourceUrl = $assetsUrl . '../../../source/css/mgr/';

        if ($this->getOption('debug') && $assetsUrl != MODX_ASSETS_URL . 'components/fileplus/') {
            $this->modx->controller->addJavascript($jsSourceUrl . 'fileplus.js?v=v' . $this->version);
            $this->modx->controller->addJavascript($jsSourceUrl . 'fileplus.util.js?v=v' . $this->version);
            $this->modx->controller->addJavascript($jsSourceUrl . 'fileplus.uploadbrowser.js?v=v' . $this->version);
            $this->modx->controller->addJavascript($jsSourceUrl . 'fileplus.panel.input.js?v=v' . $this->version);
            $this->modx->controller->addJavascript($jsSourceUrl . 'fileplus.columnrenderer.js?v=v' . $this->version);
            $this->modx->controller->addCss($cssSourceUrl . 'fileplus.css?v=v' . $this->version);
        } else {
            $this->modx->controller->addJavascript($jsUrl . 'fileplus.min.js?v=v' . $this->version);
            $this->modx->controller->addCss($cssUrl . 'fileplus.min.css?v=v' . $this->version);
        }
        $config = $this->options;
        unset($config['scriptsIncluded']);
        $this->modx->controller->addHtml('<script type="text/javascript">' .
            'FilePlus.config = ' . json_encode(array_merge($config, [
                // The urls of the media sources are only needed by the manager scripts
                'sources' => $this->loadSourceMap()
            ]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . ';' .
            '</script>');
    }

    /**
     * Prepare a value and return a valid JSON encoded File+ object
     *
     * The File+ object contains the file path in the media source and the title/description text:
     * {"file": "", "title": "", "description": ""}
     * A plain file path (i.e. of a file template variable, that was changed to File+) is converted to that format.
     *
     * @param string $value
     * @return string
     */
    public function prepareTvValue($value)
    {
        $data = $this->parseValue($value);
        return ($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
    }

    /**
     * Get the File+ data array of a value
     *
     * @param string $value
     * @return array The data array or an empty array, if the value does not contain a file
     */
    public function parseValue($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return [];
        }
        $data = json_decode($value, true);
        if (!is_array($data)) {
            $data = ['file' => $value];
        }
        if (empty($data['file']) || !is_string($data['file'])) {
            return [];
        }
        return [
            'file' => $data['file'],
            'title' => (string)($data['title'] ?? ''),
            'description' => (string)($data['description'] ?? ''),
        ];
    }

    /**
     * Get the output of an File+ value: the url of the file or the parsed output chunk
     *
     * @param string $value
     * @param array $opts The outputChunk option contains the name of the output chunk, docid the resource id and
     * source the media source id, if no template variable is used.
     * @param modTemplateVar|null $tv
     * @return string
     */
    public function getFileOutput($value, $opts = [], $tv = null)
    {
        $data = $this->parseValue($value);
        if (!$data) {
            return '';
        }
        $source = $this->getTvSource($tv, $opts['docid'] ?? 0, $opts['source'] ?? 0);
        if (!$source) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Invalid Media Source', '', 'File+');
            return '';
        }
        $source->initialize();

        $placeholders = $this->getFilePlaceholders($data, $source);
        if (empty($opts['outputChunk'])) {
            return $placeholders['url'];
        }
        $chunkProperties = $opts;
        unset($chunkProperties['outputChunk']);
        return $this->modx->getChunk($opts['outputChunk'], array_merge($chunkProperties, $placeholders));
    }

    /**
     * Get the placeholders of a file in a media source
     *
     * @param array $data The parsed value
     * @param modMediaSource $source The initialized media source
     * @return array
     */
    public function getFilePlaceholders(array $data, $source)
    {
        $file = $data['file'];
        // The size is only available for files in the file system, i.e. not for files in an S3 media source
        $basePath = (string)$source->getBasePath();
        $path = $basePath . $file;
        $size = ($basePath !== '' && strpos($file, "\0") === false && is_file($path)) ? (int)filesize($path) : 0;
        return [
            'url' => $this->getSourceObjectUrl($source, $file),
            'file' => $file,
            'filename' => basename($file),
            'name' => pathinfo($file, PATHINFO_FILENAME),
            'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
            'size' => $size,
            'size_formatted' => $this->formatSize($size),
            'title' => $data['title'],
            'description' => $data['description'],
        ];
    }

    /**
     * Format a file size in bytes with a unit
     *
     * @param int $bytes
     * @return string
     */
    public function formatSize($bytes)
    {
        $bytes = (int)$bytes;
        if ($bytes <= 0) {
            return '';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int)floor(log($bytes, 1024)), count($units) - 1);
        $size = $bytes / pow(1024, $power);
        // The lexicon returns the key, if the lexicon topic is not loaded
        $separator = $this->modx->lexicon('fileplus.decimal_separator');
        $separator = ($separator !== 'fileplus.decimal_separator') ? $separator : '.';
        return (($power === 0) ? $size : number_format($size, 1, $separator, '')) . ' ' . $units[$power];
    }

    /**
     * Get the media source of a template variable or the default media source
     *
     * The media source of the template variable depends on the context of the resource. Without a resource id, the
     * context of the current resource is used. Without a template variable (i.e. for a MIGX value), the media source
     * with the source id or the default media source is used.
     *
     * @param modTemplateVar|null $tv
     * @param int $docid The id of the resource, that contains the template variable value
     * @param int $sourceId The id of the media source, that is used without a template variable
     * @return modMediaSource|null
     */
    public function getTvSource($tv = null, $docid = 0, $sourceId = 0)
    {
        if ($tv) {
            return $tv->getSource($this->getResourceContextKey($docid));
        }
        return $this->modx->getObject('sources.modMediaSource', (int)$sourceId ?: $this->modx->getOption('default_media_source'));
    }

    /**
     * Get the context key of a resource, the context key of the current resource or mgr
     *
     * @param int $docid
     * @return string
     */
    private function getResourceContextKey($docid = 0)
    {
        $docid = (int)$docid;
        if ($this->modx->resource && (!$docid || (int)$this->modx->resource->get('id') === $docid)) {
            return $this->modx->resource->get('context_key');
        }
        if ($docid) {
            if (!isset($this->resourceContextKeys[$docid])) {
                $c = $this->modx->newQuery('modResource', ['id' => $docid]);
                $c->select('context_key');
                $this->resourceContextKeys[$docid] = (string)$this->modx->getValue($c->prepare());
            }
            if ($this->resourceContextKeys[$docid] !== '') {
                return $this->resourceContextKeys[$docid];
            }
        }
        return 'mgr';
    }

    /**
     * Get the url of an object in a media source
     *
     * @param modMediaSource $source
     * @param string $path
     * @return string
     */
    public function getSourceObjectUrl($source, $path)
    {
        $url = $source->getObjectUrl($path);
        if (!preg_match('#^(https?:)?//#', $url) && strpos($url, '/') !== 0) {
            $url = $this->modx->getOption('base_url') . $url;
        }
        return $url;
    }

    /**
     * Get the allowed file extensions of a template variable. The allowed file types input option overrides the
     * fileplus.allowed_file_types system setting. Without both, the upload_files system setting is used.
     *
     * @param string $allowedFileTypes The allowed file types input option
     * @return string[]
     */
    public function getAllowedExtensions($allowedFileTypes = '')
    {
        $types = trim((string)$allowedFileTypes);
        if ($types === '') {
            $types = trim((string)$this->modx->getOption('fileplus.allowed_file_types', null, ''));
        }
        if ($types === '') {
            $types = (string)$this->modx->getOption('upload_files', null, '');
        }
        $extensions = array_map(function ($type) {
            return strtolower(ltrim(trim($type), '.'));
        }, explode(',', $types));
        return array_values(array_unique(array_filter($extensions, function ($extension) {
            return $extension !== '' && preg_match('/^[a-z0-9]+$/', $extension);
        })));
    }

    /**
     * Replace the placeholders in an upload path
     *
     * @param string $path
     * @param int $resourceId
     * @param int $tvId
     * @return string
     */
    public function parsePathPlaceholders($path, $resourceId = 0, $tvId = 0)
    {
        $user = $this->modx->getUser();
        $placeholders = [
            '[[+year]]' => date('Y'),
            '[[+month]]' => date('m'),
            '[[+day]]' => date('d'),
            '[[+user]]' => $user ? $user->get('id') : 0,
            '[[+username]]' => $user ? $user->get('username') : '',
            '[[+resource_id]]' => (int)$resourceId,
            '[[+tv_id]]' => (int)$tvId,
        ];
        return str_replace(array_keys($placeholders), array_values($placeholders), (string)$path);
    }
}
