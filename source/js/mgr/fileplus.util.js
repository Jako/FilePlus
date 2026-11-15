/**
 * File+ Utilities
 *
 * @package fileplus
 * @subpackage script
 */

/**
 * Apply the defaults to the config, if the config value is not set, empty or zero
 */
FilePlus.util.applyDefaults = function (config, defaults) {
    Ext.iterate(defaults, function (key, value) {
        if (config[key] === undefined || config[key] === null || config[key] === '' || config[key] === 0) {
            config[key] = value;
        }
    });
    return config;
};

/**
 * Get the value object from a value, that is an object, a JSON encoded object or a path
 */
FilePlus.util.parseFileValue = function (value) {
    if (typeof value === 'string') {
        if (value.charAt(0) === '{') {
            try {
                return JSON.parse(value) || {};
            } catch (e) {
                return {};
            }
        }
        return value ? {file: value} : {};
    }
    return value || {};
};

/**
 * Get the url of a media source path, that can be used in the manager
 */
FilePlus.util.sourceUrl = function (source) {
    var url = (FilePlus.config.sources && FilePlus.config.sources[source]) ? FilePlus.config.sources[source].url : '';
    return FilePlus.util.sourceObjectUrl(url) || MODx.config.base_url;
};

/**
 * Get the url of a media source object, that can be used in the manager. Root relative and absolute urls are used
 * as they are, other urls are relative to the base url.
 */
FilePlus.util.sourceObjectUrl = function (url) {
    if (!url) {
        return '';
    }
    return (/^(https?:)?\/\//.test(url) || url.charAt(0) === '/') ? url : MODx.config.base_url + url;
};

/**
 * Get the url of a file in a media source
 */
FilePlus.util.fileUrl = function (file, source) {
    if (!file) {
        return '';
    }
    if (/^(https?:)?\/\//.test(file)) {
        return file;
    }
    var baseUrl = FilePlus.util.sourceUrl(source || MODx.config.default_media_source);
    return baseUrl.replace(/\/?$/, '/') + file.replace(/^\//, '').split('/').map(encodeURIComponent).join('/');
};

/**
 * Get the lowercase extension of a file path
 */
FilePlus.util.fileExtension = function (file) {
    var filename = String(file || '').split('/').pop();
    var pos = filename.lastIndexOf('.');
    return (pos > 0) ? filename.substring(pos + 1).toLowerCase() : '';
};

/**
 * Get the Font Awesome icon class of a file extension
 */
FilePlus.util.fileIcon = function (extension) {
    var icons = {
        'file-pdf-o': ['pdf'],
        'file-word-o': ['doc', 'docx', 'odt', 'rtf'],
        'file-excel-o': ['xls', 'xlsx', 'ods', 'csv'],
        'file-powerpoint-o': ['ppt', 'pptx', 'odp'],
        'file-archive-o': ['zip', 'rar', '7z', 'gz', 'tar'],
        'file-image-o': ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp', 'tif', 'tiff'],
        'file-audio-o': ['mp3', 'wav', 'ogg', 'm4a', 'flac'],
        'file-video-o': ['mp4', 'mov', 'webm', 'avi', 'mkv'],
        'file-text-o': ['txt', 'md']
    };
    var icon = 'file-o';
    Ext.iterate(icons, function (key, extensions) {
        if (extensions.indexOf(extension) !== -1) {
            icon = key;
            return false;
        }
    });
    return 'icon-' + icon;
};
