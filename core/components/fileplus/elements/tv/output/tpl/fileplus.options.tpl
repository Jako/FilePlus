<script type="text/javascript">
    // <![CDATA[{literal}
    var params = {
        {/literal}{foreach from=$params key=k item=v name='p'}
        '{$k}': '{$v|escape:"javascript"}'{if NOT $smarty.foreach.p.last}, {/if}
        {/foreach}{literal}
    };
    var oc = {
        'change': {
            fn: function () {
                Ext.getCmp('modx-panel-tv').markDirty();
            }, scope: this
        }
    };
    MODx.load({
        xtype: 'panel',
        layout: 'form',
        applyTo: 'modx-widget-props',
        cls: 'fileplus-props',
        border: false,
        labelAlign: 'top',
        listeners: {
            afterrender: function (component) {
                var tvTabId = (FilePlus.config.modxversion === '2') ? 'modx-tv-tabs' : 'modx-tv-editor-tabs';
                Ext.getCmp('modx-panel-tv-output-properties').addListener('resize', function () {
                    component.setWidth(Ext.getCmp('modx-widget-props').getWidth()).doLayout();
                });
                Ext.getCmp(tvTabId).addListener('tabchange', function () {
                    component.setWidth(Ext.getCmp('modx-widget-props').getWidth()).doLayout();
                });
            },
        },
        items: [{
            xtype: 'modx-combo',
            url: MODx.config.connectors_url + 'index.php',
            baseParams: {
                action: (FilePlus.config.modxversion === '2') ? 'element/chunk/getlist' : 'Element/Chunk/GetList'
            },
            pageSize: 20,
            fields: ['name', 'id'],
            displayField: 'name',
            valueField: 'name',
            typeAhead: true,
            editable: true,
            fieldLabel: _('fileplus.outputChunk'),
            description: MODx.expandHelp ? '' : _('fileplus.outputChunk_desc'),
            name: 'prop_outputChunk',
            id: 'prop_outputChunk{/literal}{$tv}{literal}',
            value: params['outputChunk'] || '',
            anchor: '100%',
            listeners: oc
        }, {
            xtype: MODx.expandHelp ? 'label' : 'hidden',
            forId: 'prop_outputChunk{/literal}{$tv}{literal}',
            html: _('fileplus.outputChunk_desc'),
            cls: 'desc-under'
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
        }],
    });
    MODx.helpUrl = 'https://jako.github.io/FilePlus/usage/';
    // ]]>
</script>
{/literal}
