[[!msFavorites.initialize]]

<p>
    <a href="/favorites.html" class="msfavorites-total-all visible"
       data-data-list="default"
       data-data-type="resource"
       data-msfavorites-animation
    >В избранное
        <span class="msfavorites-total" data-data-list="default" data-data-type="resource">0</span>
    </a>
</p>

<div id="pdopage">
    <div class="rows">
        [[!pdoPage?
        &parents=`0`
        &showUnpublished=`0`
        &showDeleted=`0`
        &element=`msProducts`
        &tpl=`@INLINE
        <a class="msfavorites"
           data-click
           data-data-list="default"
           data-data-type="resource"
           data-data-key="[[+id]]"
           data-msfavorites-animation="like.png"
        >
            <i class="msfavorites-icon-heart"></i>
        </a>
        <span class="msfavorites-total"
              data-data-list="default"
              data-data-type="resource"
        >0</span>
        [[+id]] - [[+pagetitle]]<br><br>
        `
        &ajaxMode=`default`
        ]]
    </div>
    [[!+page.nav]]
</div>