/**
 * File+ Input Panel
 *
 * @package fileplus
 * @subpackage script
 */

FilePlus.panel.input = function (config) {
    config = config || {};

    this.options = config.options;
    this.hiddenField = config.hiddenField;
    this.data = this.getValue();

    this.createFileBrowser();
    this.createTextFields();

    Ext.apply(config, {
        border: false,
        config: config,
        baseCls: 'modx-panel',
        cls: 'fileplus-panel-input',
        items: [
            {
                // The browser renders the preview into its container, so that the fields are placed below the preview
                cls: 'fileplus-file-browser',
                border: false,
                items: [
                    this.fileBrowser
                ]
            },
            this.fieldsPanel = new Ext.Panel({
                cls: 'fileplus-file-fields',
                border: false,
                items: this.textFields
            })
        ],
        listeners: {
            afterrender: {
                fn: function () {
                    // The browser fires change events while it is rendered
                    this.initialized = true;
                    this.observeWidth();
                },
                scope: this
            },
            destroy: {
                fn: function () {
                    if (this.resizeObserver) {
                        this.resizeObserver.disconnect();
                    }
                },
                scope: this
            }
        }
    });
    FilePlus.panel.input.superclass.constructor.call(this, config);

    this.listenForResetEvent();
};
Ext.extend(FilePlus.panel.input, MODx.Panel, {
    // The minimum panel width, that shows the text fields in one line
    inlineFieldsWidth: 700,
    // The gap between the text fields in one line
    inlineFieldsGap: 10,
    // Size the fields to the full width of the template variable area, also when it is shown later in a tab
    observeWidth: function () {
        var update = function () {
            var width = this.el.getWidth(true);
            if (width && width !== this.fieldWidth) {
                this.fieldWidth = width;
                this.fileBrowser.setWidth(width);
                // The enabled text fields are shown in one line, if the panel is wide enough
                var fields = this.textFields.filter(function (field) {
                    return !field.hidden;
                });
                var inline = fields.length > 1 && width >= this.inlineFieldsWidth;
                var fieldWidth = inline ? Math.floor((width - (fields.length - 1) * this.inlineFieldsGap) / fields.length) : width;
                this.fieldsPanel.body[inline ? 'addClass' : 'removeClass']('fileplus-file-fields-inline');
                Ext.each(fields, function (field) {
                    field.items.get(0).setWidth(fieldWidth);
                });
            }
        }.bind(this);
        if (window.ResizeObserver) {
            this.resizeObserver = new ResizeObserver(update);
            this.resizeObserver.observe(this.el.dom);
        } else {
            Ext.EventManager.onWindowResize(update, this);
        }
        update();
    },
    // Listen for TV 'Reset' button
    listenForResetEvent: function () {
        var resourcePanel = Ext.getCmp('modx-panel-resource');
        if (resourcePanel) {
            resourcePanel.on('tv-reset', function (changed) {
                if (parseInt(changed.id) === this.options.tvId) {
                    this.onReset();
                }
            }, this);
        }
    },
    // Create the file upload/browser field
    createFileBrowser: function () {
        this.fileBrowser = MODx.load({
            xtype: 'fileplus-combo-upload-browser',
            value: this.data.file || '',
            submitValue: false,
            hideLabel: true,
            allowBlank: this.options.allowBlank !== false,
            source: this.options.mediaSource,
            wctx: MODx.request.wctx || 'mgr',
            allowedExtensions: this.options.allowedExtensions || [],
            isTV: true,
            resourceId: MODx.request.id || 0,
            tvId: this.options.tvId,
            listeners: {
                change: {
                    fn: this.onFileChange,
                    scope: this
                }
            }
        });
    },
    // Create fields for the title and the description input
    createTextFields: function () {
        this.textFields = [
            this.createTextField('title', this.options.titleOn, 'fileplus.title', 'icon-header'),
            this.createTextField('description', this.options.descriptionOn, 'fileplus.description', 'icon-align-left')
        ];
    },
    createTextField: function (key, enabled, lexicon, icon) {
        var update = function (field) {
            this.data[key] = field.getValue();
            this.updateValue();
        };
        return MODx.load({
            xtype: 'panel',
            cls: 'fileplus-file-field',
            border: false,
            hidden: !enabled,
            dataKey: key,
            items: {
                xtype: enabled ? 'textfield' : 'hidden',
                submitValue: false,
                value: this.data[key] || '',
                listeners: {
                    change: {
                        fn: update,
                        scope: this
                    },
                    afterrender: function (field) {
                        var el = field.getEl();
                        if (el && enabled) {
                            // The value is updated while typing, because the change event is not fired, if the
                            // resource is saved with the keyboard shortcut
                            el.on('input', function () {
                                update.call(this, field);
                            }, this);
                            el.set({'placeholder': _(lexicon)});
                            el.insertSibling({
                                tag: 'span',
                                cls: 'icon ' + icon,
                                style: 'position: absolute; left: 8px; top: 14px; opacity: 0.6',
                                title: _(lexicon)
                            }, 'after', true);
                        }
                    }.bind(this)
                },
                style: {
                    marginTop: '5px',
                    paddingLeft: '25px'
                }
            }
        });
    },
    // Fires when the TV field is reset
    onReset: function () {
        this.data = this.getValue();
        this.fileBrowser.setValue(this.data.file || '');
        Ext.each(this.textFields, function (panel) {
            panel.items.get(0).setValue(this.data[panel.dataKey] || '');
        }, this);
    },
    // Fired when the file is changed in the browser
    onFileChange: function () {
        if (!this.initialized) {
            return;
        }
        this.data.file = this.fileBrowser.getValue() || '';
        this.updateValue();
    },
    // Get the file data
    getFileData: function () {
        return {
            file: this.data.file,
            title: this.data.title || '',
            description: this.data.description || ''
        };
    },
    // Get hidden field value
    getValue: function () {
        return FilePlus.util.parseFileValue(Ext.get(this.hiddenField).getValue());
    },
    // Update hidden field value
    updateValue: function () {
        var hiddenField = document.getElementById(this.hiddenField);
        var newValue = (this.data.file) ? JSON.stringify(this.getFileData(), null, '  ') : '';
        if (hiddenField && hiddenField.value !== newValue) {
            hiddenField.value = newValue;

            // Mark resource as dirty
            MODx.fireResourceFormChange();
        }
    }
});
Ext.reg('fileplus-panel-input', FilePlus.panel.input);
