<div class="msfavorites-parent">
    <a class="msfavorites"
       data-click
       data-msfavorites-mode="list"
       data-data-id="[[+id]]"
       data-data-list="default">
        <i class="msfavorites-icon-heart"></i>
        <span class="msfavorites-text-load">секунду</span>
        <span class="msfavorites-text-add">отложить</span>
        <span class="msfavorites-text-remove">удалить</span>
    </a>

    <form class="msfavorites"
          data-formchange
          data-msfavorites-mode="list"
          data-data-id="[[+id]]"
          data-data-list="default">

        {var $row = '!msFavorites.properties'|snippet:['rid' => $id,'return'=>'data']}

        <input type="number" name="properties[count]" value="{$row.count?:1}">
        <input type="hidden" name="method" value="add">

        <span class="msfavorites-total" data-data-list="default">0</span>
        [[+pagetitle]]
        <br><br>
    </form>
</div>