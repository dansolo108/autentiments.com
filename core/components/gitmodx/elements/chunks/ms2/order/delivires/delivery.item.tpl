<label class="auten-delivery-method {if $checked}active{/if} {$classes}" >
    {$_modx->getChunk("radio",["checked"=>$checked,"value"=>$id,"name"=>"delivery"])}
    <div class="auten-delivery-method__title">
        {$name}
    </div>
    {if $address}
    <div class="auten-delivery-method__address">
        {$address}
    </div>
    {/if}
    <div class="auten-delivery-method__date">
        {if $min && $max}{$min}-{$max} дней,{/if}{if $cost == 0}бесплатно{else}<span>{$cost}</span> {$_modx->getPlaceholder('msmc.symbol_right')}{/if}
    </div>
    {if $description}
        <div class="auten-delivery-method__info">
            {$description}
        </div>
    {/if}
</label>