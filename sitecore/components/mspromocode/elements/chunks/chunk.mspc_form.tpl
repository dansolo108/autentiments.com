<!--@formatter:off-->
<style>
	.mspc_form {
		width: 100%;
	}
</style>

<div class="row">
	<div class="mspc_form">
		<div class="col-7 col-xs-7">
			<div>
				<div class="input-group">
					<span class="input-group-prepend input-group-addon">
						<span class="input-group-text">[[%mspromocode_promocode]]</span>
					</span>
					<input type="text" class="mspc_field form-control [[+coupon:notempty=`[[+disfield]]`]]"
						   [[+coupon:notempty=`disabled`]]
						   value="[[+coupon]]" placeholder="[[%mspromocode_enter_promocode]]" />
					<span class="input-group-append input-group-btn">
						<button type="button" class="mspc_btn btn btn-default btn-secondary">[[+btn]]</button>
					</span>
				</div>
				<div class="mspc_coupon_description" style="display: none;">[[+coupon_description]]</div>
			</div>
			<div class="mspc_msg"></div>
		</div>
		<div class="col-5 col-xs-5">
			<div class="mspc_discount_amount" style="display:none; margin-top:7px;"><b>[[%mspromocode_discount_amount]]</b>: <span>[[+discount_amount]]</span> [[%ms2_frontend_currency]]</div>
		</div>
	</div>
</div>