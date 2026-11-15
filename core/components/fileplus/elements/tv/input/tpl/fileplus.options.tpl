<script type="text/javascript">
    // <![CDATA[{literal}
    var params = {
        {/literal}{foreach from=$params key=k item=v name='p'}
        '{$k}': '{$v|escape:"javascript"}'{if NOT $smarty.foreach.p.last}, {/if}
        {/foreach}{literal}
    };
    var oc = {
        change: {
            fn: function () {
                Ext.getCmp('modx-panel-tv').markDirty();
            }, scope: this
        }
    };
    var isTrue = function (value) {
        return value === 1 || value === '1' || value === true || value === 'true';
    };
    MODx.load({
        xtype: 'panel',
        layout: 'form',
        applyTo: 'modx-input-props',
        cls: 'fileplus-props',
        border: false,
        labelAlign: 'top',
        listeners: {
            afterrender: function (component) {
                // The input option values are not used by File+
                var elements = Ext.getCmp('modx-tv-elements');
                if (elements) {
                    elements.hide().nextSibling().hide();
                    if (FilePlus.config.modxversion === '2') {
                        // MODX 2 shows only the field again, when another type is selected
                        Ext.getCmp('modx-tv-type').on('select', function () {
                            elements.nextSibling().show();
                        }, null, {single: true});
                    }
                }
                var tvTabId = (FilePlus.config.modxversion === '2') ? 'modx-tv-tabs' : 'modx-tv-editor-tabs';
                Ext.getCmp('modx-panel-tv-input-properties').addListener('resize', function () {
                    component.setWidth(Ext.getCmp('modx-input-props').getWidth()).doLayout();
                });
                Ext.getCmp(tvTabId).addListener('tabchange', function () {
                    component.setWidth(Ext.getCmp('modx-input-props').getWidth()).doLayout();
                });
            },
        },
        items: [{
            cls: 'x-form-item fileplus-section',
            html: '<label class="x-form-item-label" style="padding-bottom: 0">' +
                _('fileplus.input_section') + '</label><label class="desc-under" style="">' +
                _('fileplus.input_section_desc') + '</label>'
        }, {
            layout: 'column',
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                labelAlign: 'top',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('fileplus.allowedFileTypes'),
                    description: MODx.expandHelp ? '' : _('fileplus.allowedFileTypes_desc'),
                    name: 'inopt_allowedFileTypes',
                    id: 'inopt_allowedFileTypes{/literal}{$tv}{literal}',
                    value: params['allowedFileTypes'] || '',
                    anchor: '100%',
                    listeners: oc
                }, {
                    xtype: MODx.expandHelp ? 'label' : 'hidden',
                    forId: 'inopt_allowedFileTypes{/literal}{$tv}{literal}',
                    html: _('fileplus.allowedFileTypes_desc'),
                    cls: 'desc-under'
                }]
            }, {
                columnWidth: 0.5,
                layout: 'form',
                labelAlign: 'top',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('fileplus.uploadPath'),
                    description: MODx.expandHelp ? '' : _('fileplus.uploadPath_desc'),
                    name: 'inopt_uploadPath',
                    id: 'inopt_uploadPath{/literal}{$tv}{literal}',
                    value: params['uploadPath'] || '',
                    anchor: '100%',
                    listeners: oc
                }, {
                    xtype: MODx.expandHelp ? 'label' : 'hidden',
                    forId: 'inopt_uploadPath{/literal}{$tv}{literal}',
                    html: _('fileplus.uploadPath_desc'),
                    cls: 'desc-under'
                }]
            }]
        }, {
            layout: 'column',
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                labelAlign: 'top',
                items: [{
                    xtype: 'combo-boolean',
                    fieldLabel: _('fileplus.allowTitle'),
                    description: MODx.expandHelp ? '' : _('fileplus.allowTitle_desc'),
                    name: 'inopt_allowTitle',
                    hiddenName: 'inopt_allowTitle',
                    id: 'inopt_allowTitle{/literal}{$tv}{literal}',
                    value: isTrue(params['allowTitle']),
                    anchor: '100%',
                    listeners: oc
                }, {
                    xtype: MODx.expandHelp ? 'label' : 'hidden',
                    forId: 'inopt_allowTitle{/literal}{$tv}{literal}',
                    html: _('fileplus.allowTitle_desc'),
                    cls: 'desc-under'
                }]
            }, {
                columnWidth: 0.5,
                layout: 'form',
                labelAlign: 'top',
                items: [{
                    xtype: 'combo-boolean',
                    fieldLabel: _('fileplus.allowDescription'),
                    description: MODx.expandHelp ? '' : _('fileplus.allowDescription_desc'),
                    name: 'inopt_allowDescription',
                    hiddenName: 'inopt_allowDescription',
                    id: 'inopt_allowDescription{/literal}{$tv}{literal}',
                    value: isTrue(params['allowDescription']),
                    anchor: '100%',
                    listeners: oc
                }, {
                    xtype: MODx.expandHelp ? 'label' : 'hidden',
                    forId: 'inopt_allowDescription{/literal}{$tv}{literal}',
                    html: _('fileplus.allowDescription_desc'),
                    cls: 'desc-under'
                }]
            }]
        }, {
            cls: "treehillstudio_about",
            html: '<img width="146" height="40" src="' + FilePlus.config.assetsUrl + 'img/mgr/treehill-studio-small.png"' + ' srcset="' + FilePlus.config.assetsUrl + 'img/mgr/treehill-studio-small@2x.png 2x" alt="Treehill Studio">',
            listeners: {
                afterrender: function (component) {
                    component.getEl().select('img').on('click', function () {
                        var msg = '<span style="display: inline-block; text-align: center;">' +
                            '<img src="' + FilePlus.config.assetsUrl + 'img/mgr/treehill-studio.png" srcset="' + FilePlus.config.assetsUrl + 'img/mgr/treehill-studio@2x.png 2x" alt="Treehill Studio"><br>' +
                            '&copy; 2026 by <a href="https://treehillstudio.com" target="_blank">treehillstudio.com</a></span>';
                        Ext.Msg.show({
                            title: _('fileplus') + ' ' + FilePlus.config.version,
                            msg: msg,
                            buttons: Ext.Msg.OK,
                            cls: 'treehillstudio_window',
                            width: 358
                        });
                    });
                }
            }
        }]
    });
    MODx.helpUrl = 'https://jako.github.io/FilePlus/usage/';
    // ]]>
</script>
{/literal}
