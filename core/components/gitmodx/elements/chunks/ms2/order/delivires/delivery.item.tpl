<label class="auten-delivery-method {if $checked}active{/if} {$classes}">
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
        {if $min && $max}{$min}-{$max} дней,{/if}{if $cost == 0} бесплатно{else}
            <span>{$cost}</span> {$_modx->getPlaceholder('msmc.symbol_right')}{/if}
    </div>
    {if $description}
        <div class="auten-delivery-method__info">
            {$description}
        </div>
    {/if}
    {if $hasPickupPoints}
        <button class="auten-button auten-delivery-method__pickup" data-open-modal="#delivery-modal-{$id}" type="button">
            Выбрать пункт
        </button>
    {/if}
</label>
{if $hasPickupPoints}
    <div id="delivery-modal-{$id}" data-msCalcDelivery-Pickup="{$id}" class="auten-modal">
        <div class="auten-modal__wrapper fullsize">
            <div class="auten-modal__title">
                Пункты выдачи
            </div>
            <div class="auten-modal__close"></div>
            <div class="auten-modal__inner">
                <div class="auten-pickup-points">
                    <div class="auten-pickup-points__map" data-msCalcDelivery-Pickup-Map></div>
                    <div class="auten-pickup-points__items" data-msCalcDelivery-Pickup-Points></div>
                    <div class="auten-modal__close auten-modal__button" style="display: none;">Выбрать этот пункт</div>
                </div>
            </div>
        </div>
    </div>
{/if}