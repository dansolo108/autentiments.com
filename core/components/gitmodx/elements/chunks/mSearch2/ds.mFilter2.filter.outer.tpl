{var $key = $filter}
{switch $key}
    {case 'size'}
        {set $class = 'sizes'}
    {case 'hexcolor'}
        {set $class = 'colors'}
    {case 'material'}
        {set $class = 'materials'}
{/switch}
<li id="mse2_{$key}" class="au-filter__col  au-filter__col_{$class}">
    <span class="au-filter__title">{('mse2_filter_' ~ $table ~ '_' ~ $filter) | lexicon}</span>
    <ul class="au-filter__list">
        {$rows}
    </ul>
</li>