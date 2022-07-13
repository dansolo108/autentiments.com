<?php
$items = $modx->cacheManager->get('stik_counries_'.$lang);
if (empty($items)) {
    $q = $modx->newQuery('stikCountry');
    $q->select('stikCountry.code as code,stikCountry.name as name,stikCountry.ru_name as ru_name,stikCountry.sort as sort,stikCountry.ru_sort as ru_sort');
    
    if ($lang == 'ru') {
        $q->sortby('ru_sort', 'desc');
        $q->sortby('ru_name', 'asc');
    } else {
        $q->sortby('sort', 'desc');
        $q->sortby('name', 'asc');
    }

    if ($q->prepare() && $q->stmt->execute()) {
        $items = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $modx->cacheManager->set('stik_counries_'.$lang, $items, 3600);
}

$output = '';
if ($lang == 'ru') {
    $output .= '<option value="" hidden>Выберите страну</option>';
    $output .= '<option value="" disabled>Популярные страны: </option>';
    foreach($items as $item){
        if ($item['ru_sort'] == 0 && $other !== true) {
            $output .= '<option value="" disabled>Остальные страны: </option>';
            $other = true;
        }
        if ($selected == $item['ru_name']){
            $output .= '<option value="'.$item['ru_name'].'" selected>'.$item['ru_name'].'</option>';
        } else {
            $output .= '<option value="'.$item['ru_name'].'">'.$item['ru_name'].'</option>';
        }
    }
} else {
    $output .= '<option value="" hidden>Select a country</option>';
    $output .= '<option value="" disabled>Popular countries: </option>';
    foreach($items as $item){
        if ($item['sort'] == 0 && $other !== true) {
            $output .= '<option value="" disabled>Other countries: </option>';
            $other = true;
        }
        if ($selected == $item['name']){
            $output .= '<option value="'.$item['name'].'" selected>'.$item['name'].'</option>';
        } else {
            $output .= '<option value="'.$item['name'].'">'.$item['name'].'</option>';
        }
    }
}
return $output;