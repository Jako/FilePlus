/**
 * File+ Custom Manager Page Script
 *
 * @package fileplus
 * @subpackage script
 */

var filePlus = function (config) {
    config = config || {};
    filePlus.superclass.constructor.call(this, config);
};

Ext.extend(filePlus, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, form: {}, util: {}
});
Ext.reg('fileplus', filePlus);

FilePlus = new filePlus();
