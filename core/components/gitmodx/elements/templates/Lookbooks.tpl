{extends 'template:1'}

{block 'main'}
    <main class="au-lookbooks  page-container" id="pdopage">
        <h1 class="au-h1  au-lookbooks__title">{$_modx->resource.pagetitle}</h1>
        <div class="au-lookbooks__cards rows">
            {'!pdoPage' | snippet : [
                'includeTVs' => 'img,video',
                'prepareTVs' => 'img,video',
                'processTVs' => 'img,video',
                'parents' => $_modx->resource.id,
                'tpl' => 'tpl.lookbookList',
                'sortby' => 'menuindex',
                'sortdir' => 'ASC',
                'limit' => 3,
                'ajaxMode' => 'scroll',
            ]}
        </div>
        {$_modx->getPlaceholder('page.nav')}
    </main>
{/block}