<div class="media">
      <div class="pull-left">
	  <a class="thumbnail" href="{$uri}">
	    <img class="media-object" src="{$_modx->runSnippet("phpThumb", [ 'input' => $_pls['tv.img'] ?: 'tpl/upload/imgtmp01.jpg', 'options' => 'w=90&h=90&zc=1&q=90', 'useResizer' => 1, ])}" alt="txt">
	  </a>
      </div>
      <div class="media-body">
	<h4 class="media-heading"><a href="{$uri}">{$pagetitle}</a></h4>
	{$introtext}
      </div>
</div>