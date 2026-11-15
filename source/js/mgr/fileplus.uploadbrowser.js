/**
 * File+ UploadBrowser Component
 *
 * @package fileplus
 * @subpackage script
 */

/**
 * A trigger field with several triggers. Each entry in triggerActions creates a trigger, that calls the method
 * on<Action>Trigger on click and gets the css class from triggerClasses.
 */
FilePlus.form.MultiTriggerField = Ext.extend(Ext.form.TriggerField, {
    triggerActions: [],
    triggerClasses: {},
    initComponent: function () {
        FilePlus.form.MultiTriggerField.superclass.initComponent.call(this);
        var count = {2: 'double', 3: 'triple', 4: 'quadruple'}[this.triggerActions.length] || 'multi';
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-form-' + count + '-triggers',
            cn: this.triggerActions.map(function (action) {
                return {tag: 'div', cls: 'x-form-trigger ' + (this.triggerClasses[action] || ''), 'data-action': action};
            }, this)
        };
    },
    initTrigger: function () {
        var triggerSelect = this.trigger.select('.x-form-trigger', true);
        triggerSelect.each(function (trigger) {
            var action = trigger.dom.dataset.action;
            var handler = this['on' + action.charAt(0).toUpperCase() + action.slice(1) + 'Trigger'] || Ext.emptyFn;
            this.mon(trigger, 'click', handler, this, {preventDefault: true});
            trigger.addClassOnOver('x-form-trigger-over');
            trigger.addClassOnClick('x-form-trigger-click');
        }, this);
        this.triggers = triggerSelect.elements;
    },
    getTriggerWidth: function () {
        var triggerWidth = 0;
        Ext.each(this.triggers, function (trigger) {
            triggerWidth += trigger.getWidth() || this.defaultTriggerWidth;
        }, this);
        return triggerWidth;
    },
    onDestroy: function () {
        Ext.destroy(this.triggers);
        FilePlus.form.MultiTriggerField.superclass.onDestroy.call(this);
    }
});

FilePlus.form.FileField = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        buttonText: _('fileplus.select_file'),
        disabled: true,
        accept: '',
        baseParams: {},
        value: '',
        listeners: {},
    });
    Ext.apply(config.listeners, {
        afterrender: {
            fn: this.onAfterRender,
            scope: this,
        }
    });
    FilePlus.form.FileField.superclass.constructor.call(this, config);
};
Ext.extend(FilePlus.form.FileField, Ext.form.TextField, {
    onAfterRender: function () {
        var fileInput = {
            id: this.id + '-fileinput',
            name: this.name || 'upload',
            cls: 'x-form-file',
            tag: 'input',
            type: 'file',
        };
        if (this.accept) {
            fileInput.accept = this.accept;
        }
        this.fileInput = this.el.wrap().createChild(fileInput);
        this.fileInput.on('change', this.onFileSelected, this);
        this.fileInput.hide();
    },
    click: function () {
        this.fileInput.dom.click();
    },
    onFileSelected: function () {
        var v = this.fileInput.dom.value;
        this.setValue(v);
        this.fireEvent('change', this, v);
    },
    /**
     * Clear the chosen file, so that the change event is fired again, if the same file is chosen
     */
    clearFile: function () {
        if (this.fileInput) {
            this.fileInput.dom.value = '';
        }
        this.setValue('');
    }
});
Ext.reg('fileplus-form-filefield', FilePlus.form.FileField);

/**
 * A field to upload a file or to select it in the media browser. The value is the path of the file in the media
 * source.
 */
FilePlus.combo.UploadBrowser = function (config) {
    config = config || {};
    this.ident = 'fileplus-combo-upload-browser-' + Ext.id();
    FilePlus.util.applyDefaults(config, {
        width: 400,
        triggerActions: ['clear', 'upload', 'browse'],
        triggerClasses: {
            clear: 'x-form-clear-trigger',
            upload: 'x-form-upload-trigger',
            browse: 'x-form-file-trigger',
        },
        source: MODx.config.default_media_source,
        allowedExtensions: [],
        wctx: 'web',
        openTo: '',
        rootId: '/',
        hideSourceCombo: true,
        isTV: false,
        resourceId: '0',
        tvId: '0',
    });
    FilePlus.combo.UploadBrowser.superclass.constructor.call(this, config);
};
Ext.extend(FilePlus.combo.UploadBrowser, FilePlus.form.MultiTriggerField, {
    onRender: function (ct, position) {
        FilePlus.combo.UploadBrowser.superclass.onRender.call(this, ct, position);
        // The real field holds the value, the visible fake field shows the path
        this.el.dom.style.position = 'absolute';
        this.el.dom.style.left = '-100vw';
        this.fakeEl = Ext.DomHelper.insertBefore(this.el, {
            tag: 'input', type: 'text', cls: 'x-form-text x-form-field',
        }, true);
        this.fakeEl.on('change', function (event, target) {
            this.setValue(target.value.trim());
            if (this.isTV) {
                MODx.fireResourceFormChange();
            }
        }, this);
        this.previewEl = Ext.DomHelper.append(this.container, {
            tag: 'div', cls: 'x-form-fileplus-preview',
        }, true);
        this.createUploadForm();
        this.updatePreview(this.getValue());
    },
    onResize: function (aw, ah, w, h) {
        FilePlus.combo.UploadBrowser.superclass.onResize.call(this, aw, ah, w, h);
        this.fakeEl.dom.style.width = this.el.dom.style.width;
    },
    onDestroy: function () {
        // The upload form is rendered outside of the field, the fake field and the preview next to it
        Ext.destroy(this.uploadForm, this.fakeEl, this.previewEl);
        FilePlus.combo.UploadBrowser.superclass.onDestroy.call(this);
    },
    onClearTrigger: function () {
        this.setValue('');
        if (this.isTV) {
            MODx.fireResourceFormChange();
        }
    },
    onUploadTrigger: function () {
        this.uploadField.click();
    },
    onBrowseTrigger: function (event) {
        var browserWindow = MODx.load({
            xtype: 'modx-browser',
            closeAction: 'close',
            id: Ext.id(),
            multiple: true,
            source: this.source,
            allowedFileTypes: this.allowedExtensions.join(','),
            openTo: this.getOpenTo(),
            rootId: this.rootId,
            hideSourceCombo: this.hideSourceCombo,
            listeners: {
                select: {
                    fn: this.onFileChosen,
                    scope: this,
                }
            }
        });
        browserWindow.show(event);
    },
    /**
     * The media browser opens the folder of the current file
     */
    getOpenTo: function () {
        var file = this.getValue() || '';
        return (file.lastIndexOf('/') > 0) ? file.substring(0, file.lastIndexOf('/')) : this.openTo;
    },
    createUploadForm: function () {
        this.uploadForm = MODx.load({
            xtype: 'modx-formpanel',
            id: this.ident + '-upload-form',
            renderTo: 'modx-content',
            isUpload: true,
            hidden: true,
            saveMsg: _('fileplus.uploading'),
            url: FilePlus.config.connectorUrl,
            baseParams: {
                wctx: this.wctx,
                action: 'mgr/file/upload',
                source: this.source,
                resource_id: this.resourceId,
                tv_id: this.tvId,
            },
            items: [{
                xtype: 'fileplus-form-filefield',
                id: this.ident + '-upload-filefield',
                name: this.name,
                accept: this.allowedExtensions.map(function (extension) {
                    return '.' + extension;
                }).join(','),
                listeners: {
                    change: {
                        fn: this.onUploadFileSelected,
                        scope: this,
                    }
                }
            }],
            listeners: {
                success: {
                    fn: function (o) {
                        this.onFileChosen(o.result.object);
                        if (this.isTV) {
                            MODx.fireResourceFormChange();
                        }
                    },
                    scope: this
                }
            },
        });
        this.uploadField = Ext.getCmp(this.ident + '-upload-filefield');
        this.uploadForm.form.el.dom.setAttribute('enctype', 'multipart/form-data');
        this.relayEvents(this.uploadForm.form, ['failure', 'submit']);
        // The file is cleared after the upload, otherwise the same file can't be uploaded again
        this.uploadForm.form.on({
            actioncomplete: this.uploadField.clearFile,
            actionfailed: this.uploadField.clearFile,
            scope: this.uploadField
        });
    },
    onUploadFileSelected: function () {
        this.uploadForm.form.baseParams.file = this.uploadField.getValue();
        this.uploadForm.submit();
    },
    /**
     * Called with the file data of the media browser or of the upload processor
     */
    onFileChosen: function (data) {
        this.setValue(data.relativeUrl || '');
    },
    setValue: function (value) {
        value = (typeof value === 'string') ? value : '';
        FilePlus.combo.UploadBrowser.superclass.setValue.call(this, value);
        if (this.el && this.fakeEl) {
            this.fakeEl.dom.value = value;
            this.validate();
            this.updatePreview(value);
            this.fireEvent('change');
        }
    },
    markInvalid: function (msg) {
        FilePlus.combo.UploadBrowser.superclass.markInvalid.call(this, msg);
        // The real field is hidden, so the visible field shows the invalid state
        if (this.fakeEl) {
            this.fakeEl.addClass(this.invalidClass);
        }
    },
    clearInvalid: function () {
        FilePlus.combo.UploadBrowser.superclass.clearInvalid.call(this);
        if (this.fakeEl) {
            this.fakeEl.removeClass(this.invalidClass);
        }
    },
    /**
     * Show the file with an icon of its file type and a link to open it
     */
    updatePreview: function (file) {
        if (!this.previewEl) {
            return;
        }
        this.previewEl.update('');
        if (Ext.isEmpty(file)) {
            this.previewEl.hide();
            return;
        }
        this.previewEl.show();
        var extension = FilePlus.util.fileExtension(file);
        var filename = file.split('/').pop();
        var encode = Ext.util.Format.htmlEncode;
        // The attributes are not escaped by the DomHelper
        this.previewEl.createChild({
            tag: 'a',
            cls: 'x-form-fileplus-preview-file',
            href: encode(FilePlus.util.fileUrl(file, this.source)),
            target: '_blank',
            title: encode(_('fileplus.open_file')),
            html: '<i class="icon ' + FilePlus.util.fileIcon(extension) + '"></i>' +
                '<span class="x-form-fileplus-preview-name">' + encode(filename) + '</span>' +
                (extension ? '<span class="x-form-fileplus-preview-extension">' + encode(extension) + '</span>' : '') +
                '<i class="icon icon-external-link"></i>'
        });
    }
});
Ext.reg('fileplus-combo-upload-browser', FilePlus.combo.UploadBrowser);
