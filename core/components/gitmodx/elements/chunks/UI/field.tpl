<label class="input-parent auten-field {($classes is array)?($classes | join :" "):$classes} {if $error}has-error{/if}" {if $style}style="{$style}"{/if}>
    <div class="auten-field__title">
        {$title}
    </div>
    <input type="text" name="{$name}" value="{$value}" placeholder="{$placeholder}" class="auten-field__input">
    <span class="auten-field__error">{$error}</span>
</label>