{var $key = $table ~ $delimeter ~ $filter}
{switch $key}
    {case 'msoption|size'}
        {set $class = 'sizes'}
    {case 'ms|hexcolor'}
        {set $class = 'colors'}
    {case 'msoption|material'}
        {set $class = 'materials'}
{/switch}
<fieldset id="mse2_{$key}" class="au-filter__col  au-filter__col_{$class}">
    <span class="au-filter__title">{('mse2_filter_' ~ $table ~ '_' ~ $filter) | lexicon}</span>
    <ul class="au-filter__list">
        {$rows}
    </ul>
</fieldset>