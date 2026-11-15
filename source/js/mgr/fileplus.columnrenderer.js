/**
 * File+ Column Renderer
 *
 * @package fileplus
 * @subpackage script
 * @return {string}
 */

/**
 * Render the filename of an File+ value in a grid column, i.e. in MIGX or Collections
 */
FilePlus.ColumnRenderer = function (value) {
    var data = FilePlus.util.parseFileValue(value);
    if (!data.file) {
        return '';
    }
    var encode = Ext.util.Format.htmlEncode;
    var extension = FilePlus.util.fileExtension(data.file);
    var label = data.title || data.file.split('/').pop();
    return '<span class="fileplus-column" title="' + encode(data.file) + '">' +
        '<i class="icon ' + FilePlus.util.fileIcon(extension) + '"></i> ' + encode(label) + '</span>';
};
