<div class="au-text-page__container">
    <div class="au-text-page__close-box">
        <button class="au-close  modal-close  au-modal-text-page_close" aria-label="{'stik_modal_close' | lexicon}">
            <span class="au-mobile_xl">{'stik_modal_close' | lexicon}</span>
        </button>
    </div>

    {var $resources = $_modx->getResources(
        ['published' => 1, 'deleted' => 0, 'hidemenu' => 0, 'parent' => 16, 'id:!=' => 2],
        ['sortby' => 'menuindex', 'sortdir' => 'ASC', 'limit' => 0]
    )}

    <div class="au-text-page__nav-box">
        <input class="custom-form__input  au-text-page__tab-input" type="text" id="text_tab_active" readonly>
        <ul class="au-text-page__nav">
            {foreach $resources as $resource index=$index}
                <li class="au-text-page__nav-item">
                    <a class="au-text-page__tab  au-text-tab_js  {$index == 1 ? 'active' : ''}" href="{$resource.alias}" data-text="{$resource.pagetitle}">{$resource.pagetitle}</a>
                </li>
            {/foreach}
        </ul>
    </div>
    
    <div class="au-text-page__content-box">
        {foreach $resources as $resource index=$index}
            <div class="au-text-page__content  au-tab-text-content  {$index == 1 ? 'active' : ''}" data-tab="{$resource.alias}">
                <h3>{$resource.pagetitle}</h3>
                {$resource.content}
            </div>
        {/foreach}
    </div>
</div>