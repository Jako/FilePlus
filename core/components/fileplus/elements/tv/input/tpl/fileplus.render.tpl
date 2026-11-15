<textarea class="fileplus-hidden-textarea" name="tv{$tv->id}" id="tv{$tv->id}">{$tv->value|escape}</textarea>
<div id="fileplus-panel-input-div-{$tv->id}" class="fileplus-panel-input"></div>

<script type="text/javascript">
    // <![CDATA[{literal}
    Ext.onReady(function () {
        MODx.load({{/literal}
            xtype: 'fileplus-panel-input',
            renderTo: 'fileplus-panel-input-div-{$tv->id}',
            options: {$tvConfig},
            hiddenField: 'tv{$tv->id}'{literal}
        });
    });{/literal}
    // ]]>
</script>
