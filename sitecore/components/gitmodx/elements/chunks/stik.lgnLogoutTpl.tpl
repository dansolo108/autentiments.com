{* Если шаблон авторизация, то редиректим в ЛК *}
{if $_modx->resource.template == 8}
    {$_modx->sendRedirect(11|url)}
{/if}