<div id="polylang-window-polylangcontent-tab-tvs-div" class="x-form-label-top">
    {foreach from=$categories item=category}
        {if count($category.tvs) > 0}
            <div id="polylangcontent-tv-tab{$category.id}" class="x-tab{if $category.hidden}-hidden{/if}"
                 title="{$category.category}">
                {foreach from=$category.tvs item=tv name='tv'}
                    {if $tv->type NEQ "hidden"}
                        <div class="modx-tv-type-{$tv->type} x-form-item x-tab-item {cycle values=",alt"} modx-tv{if $smarty.foreach.tv.first} tv-first{/if}{if $smarty.foreach.tv.last} tv-last{/if}"
                             id="tv{$tv->id}-tr">
                            <label for="tv{$tv->id}" class="x-form-item-label modx-tv-label">
                                <div class="modx-tv-label-title">
                                    {if $showCheckbox|default}<input type="checkbox" name="tv{$tv->id}-checkbox"
                                                                     class="modx-tv-checkbox" value="1" />{/if}
                                    <span class="modx-tv-caption"
                                          id="tv{$tv->id}-caption">{if $tv->caption}{$tv->caption}{else}{$tv->name}{/if}</span>
                                </div>
                                {if $tv->polylang_translate}
                                    <a id="polylang-translate-{$tv->name}-PolylangTv"
                                       class="polylang-translate enabled polylang-tv-translate {if $showTranslateBtn}show{/if}"
                                       data-id="{$tvIds[$tv->id]}"
                                       data-key="{$tv->name}"
                                       data-target="{$tv->id}"
                                       data-source="PolylangTv"
                                       title="{$_lang.polylang_translator_translate}"></a>
                                {/if}
                                {if $tv->description}
                                    <span class="modx-tv-label-description">{$tv->description}</span>
                                {/if}
                            </label>
                            {if $tv->inherited}<span class="modx-tv-inherited">{$_lang.tv_value_inherited}</span>{/if}
                            <div class="x-form-element modx-tv-form-element">
                                <input type="hidden" id="tvdef{$tv->id}" value="{$tv->default_text|escape}"/>
                                {$tv->get('formElement')}
                            </div>
                        </div>
                        <script type="text/javascript">{literal}Ext.onReady(function () {
                                new Ext.ToolTip({
                                    {/literal}target: 'tv{$tv->id}-caption',
                                    html: '[[*{$tv->name}]]'{literal}});
                            });{/literal}</script>
                    {else}
                        <input type="hidden" id="tvdef{$tv->id}" value="{$tv->default_text|escape}"/>
                        {$tv->get('formElement')}
                    {/if}
                {/foreach}

                <div class="clear"></div>

            </div>
        {/if}
    {/foreach}
</div>
{literal}
<script type="text/javascript">
    // <![CDATA[
    Ext.onReady(function () {
        {/literal}{if $tvcount GT 0}{literal}
        var polylang = Ext.getCmp('polylang-window-polylangcontent');
        Ext.select('.polylang-tv-translate').on('click', function (e, t, o) {
            if (!polylang) return;
            if (polylang.isEnableBtnTranslate(t.id)) {
                polylang.translate([polylang.getBtnTranslateData(t)]);
            }
        });

        setTimeout(function () {
            polylang.loadEditors('modx-richtext');
        }, 500);

        MODx.load({
            xtype: 'modx-vtabs',
            applyTo: 'polylang-window-polylangcontent-tab-tvs-div',
            autoTabs: true,
            border: false,
            plain: true,
            deferredRender: false,
            id: 'polylang-window-polylangcontent-tab-vtabs',
            headerCfg: {
                tag: 'div',
                cls: 'x-tab-panel-header vertical-tabs-header',
                id: 'polylang-window-polylangcontent-vtabs-header',
                html: MODx.config.show_tv_categories_header === true ? '<h4 id="modx-resource-vtabs-header-title">' + _('categories') + '</h4>' : ''
            }
        });
        {/literal}{/if}
        {literal}
    });
    // ]]>
</script>
{/literal}
<div class="clear"></div>
