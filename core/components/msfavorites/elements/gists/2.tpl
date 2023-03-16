[[!msFavorites.initialize]]

<div id="pdopage" class="msfavorites-parent">
    <div class="rows">
        [[!pdoPage:default=`
        <div><strong>Ваш список избранного пока пуст</strong></div>
        `?
        &parents=`0`
        &resources=`[[!msFavorites.ids]]`
        &element=`msProducts`
        &sortby=``
        &totalVar=`total`
        &tpl=`@INLINE
        <div class="msfavorites-parent">
            <a class="msfavorites"
               data-click
               data-data-list="default"
               data-data-type="resource"
               data-data-key="[[+id]]"
               data-msfavorites-mode="list"
            >
                <i class="msfavorites-icon-star"></i>
                <span class="msfavorites-text-load">секунду</span>
                <span class="msfavorites-text-add">отложить</span>
                <span class="msfavorites-text-remove">удалить</span>
            </a>
            <span class="msfavorites-total" data-data-list="default">0</span>
            [[+id]] - [[+pagetitle]]<br><br>
        </div>
        `
        &ajaxMode=`default`
        ]]
    </div>
    [[!+page.nav]]

    [[!+total:gt=`0`:then=`
    <button class="btn btn-default btn-sm msfavorites msfavorites-action"
            data-click
            data-data-list="default"
            data-data-type="resource"
            data-data-method="clear"
    >
        Очистить список
    </button>
    `:else=``]]
</div>