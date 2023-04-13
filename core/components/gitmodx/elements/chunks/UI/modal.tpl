<div data-msCalcDelivery-Pickup-Modal class="map-modal auten-modal">
    <div class="auten-modal__wrapper auten-pickup-points">
        <div class="auten-modal__title">
            Пункты выдачи
        </div>
        <div class="auten-modal__close"></div>
        <div class="auten-modal__inner">
            <div class="auten-pickup-points__map" data-msCalcDelivery-Pickup-Map data-city="{$city}" data-start="{$pickupPoints[0].coordX},{$pickupPoints[0].coordY}"></div>
            <div class="auten-pickup-points__items"  data-msCalcDelivery-Pickup-Points>
                {foreach $pickupPoints as $point}
                    <label data-msCalcDelivery-Pickup-Point class="auten-pickup-point" data-coord="{$point.coordX},{$point.coordY}"
                           data-name="{$point.Name}, {$point.FullAddress}" data-code="{$point.Code}">
                        <input type="hidden" name="point" value="{$point}">
                        <div class="auten-pickup-point__name">
                            {$point.Name}
                        </div>
                        <div class=" auten-pickup-point__work-time">
                            {$point.WorkTime}
                        </div>
                        <div class="auten-pickup-point__address">
                            {$point.FullAddress}
                        </div>
                        <div class="auten-pickup-point__phone">
                            {$point.Phone}
                        </div>
                        <a href="mailto:{$point.Email}" class="auten-pickup-point__email">
                            {$point.Email}
                        </a>
                    </label>
                {/foreach}
            </div>
        </div>
    </div>
</div>