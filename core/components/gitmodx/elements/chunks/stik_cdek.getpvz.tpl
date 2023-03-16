{*
	<input type="hidden" name="point"> 
	input должен находится в форме заказа (id="msOrder")
	Полный список переменных смотрите распечатав массив {$pvz | print}
*}
<div class="cdek2_container">
    <div class="pvz_error">{'stik_cdek_choose_pvz_error' | lexicon}</div>
	<a data-fancybox data-touch="false" data-src="#cdek2_map_modal" href="javascript:;" class="au-btn">Выбрать пункт выдачи</a>
	<span class="point-address"></span>
	<div id="cdek2_map_modal" style="display: none;">
	    <div class="map-modal-points">
	        Пункты выдачи
	    </div>
		<div class="cdek2_map_container">
			<div id="cdek2_map" data-city="{$city}" data-start="{$pvz[0].coordX},{$pvz[0].coordY}" data-coords="{$coords}"></div>
			<div class="cdek2_map-points">
				{foreach $pvz as $point}
					<div class="cdek2_map-point" data-coord="{$point.coordX},{$point.coordY}" data-name="{$point.Name}, {$point.FullAddress}" data-code="{$point.Code}">
						<div class="cdek2_map-point__name">
							{$point.Name}
						</div>
						<div class="cdek2_map-point__worktime">
							{$point.WorkTime}
						</div>
						<div class="cdek2_map-point__adress">
							{$point.FullAddress}
						</div>
						<div class="cdek2_map-point__phones">
							{$point.Phone}
						</div>
						<div class="cdek2_map-point__email">
							<a href="mailto:{$point.Email}">{$point.Email}</a>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
	</div>
</div>