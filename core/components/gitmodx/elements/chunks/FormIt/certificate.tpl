<form action="[[~[[*id]]]]" method="post" id="certificate">
    <h2 class="au-h2">
        Данные покупателя
    </h2>
    <div class="custom-form__row au-certificate__user-info">
        <div class="custom-form__group">
            <input type="text" class="custom-form__input" name="name" id="name" value="{$_modx->user.name?:''}">
            <label class="custom-form__label" for="name">Имя</label>
        </div>
        <div class="custom-form__group">
            <input type="text" class="custom-form__input" name="surname" id="surname" value="{$_modx->user.surname?:''}">
            <label class="custom-form__label" for="surname">Фамилия</label>
        </div>
        <div class="custom-form__group">
            <input type="email" class="custom-form__input" name="email" id="email" value="{$_modx->user.email?:''}">
            <label class="custom-form__label" for="email">Почта</label>
        </div>
        <div class="custom-form__group">
            <input type="tel" class="custom-form__input" name="phone" id="phone" value="{$_modx->user.mobilephone?"+"~$_modx->user.mobilephone:''}">
        </div>
    </div>
    <h2 class="au-h2">
        Номинал купона
    </h2>
    <div class="custom-form__row au-certificate__items">
        {'getModifications' | snippet : [
        'where'=>[
        'Modification.product_id'=>$_modx->resource.id,
        ],
        'tpl'=>'@INLINE
                        <div class="certificate">
                            <input type="radio" id="certificate-{$id}" name="certificate" value="{$id}" hidden>
                            <label for="certificate-{$id}">
                                {$price * 1} {$_modx->getPlaceholder("msmc.symbol_right")}
                            </label>
                        </div>
                    ',
        ]}
    </div>
    <button class="au-btn au-certificate__submit">Перейти к оплате</button>
</form>
<script>
    $(document).on('af_complete', function(event, response) {
        var form = response.form;
        if (form.attr('id') === 'certificate') {
            window.location.href = response.data.redirect;
        }
    });
</script>