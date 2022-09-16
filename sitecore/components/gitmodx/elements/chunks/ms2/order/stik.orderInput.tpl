<div class="custom-form__group  custom-form__group_{$field}">
    <input class="custom-form__input{($field in list $errors) ? ' error' : ''}"
        type="{if $field == 'email'}email{elseif $field == 'phone'}tel{else}text{/if}"
        name="{$field}" id="{$field}"
        value="{$field == 'email' ? ($form[$field] | filterFakeEmail) : $form[$field]}"
        data-val="{$field == 'email' ? ($form[$field] | filterFakeEmail) : $form[$field]}">
    <label class="custom-form__label" for="{$field}">{('ms2_frontend_' ~ $field) | lexicon}</label>
</div>