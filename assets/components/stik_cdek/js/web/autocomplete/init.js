var cityAutocomplete;

function initAutocomplete() {
    cityAutocomplete = new autoComplete({
        selector: 'input[name="city"]',
        minChars: 3,
        source: function (term, response) {
            term = term.toLowerCase();
            $.getJSON('https://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?', {
                q: term,
                name_startsWith: term,
            }, function (data) {
                var cityArr = data.geonames;
                if (cityArr) {
                    var suggestions = [];
                    for (i = 0; i < cityArr.length; i++) {
                        suggestions.push(cityArr[i]);
                    }
                    response(suggestions);
                }
            });
        },
        renderItem: function (item, search) {
            if (item.postCodeArray) {
                if (item.postCodeArray[1]) {
                    var cdek_index = item.postCodeArray[1];
                } else {
                    var cdek_index = item.postCodeArray[0];
                }
            } else {
                var cdek_index = 0;
            }
            
            let cityArr = item.cityName.split(",");

            return '<div class="autocomplete-suggestion" data-index="' + cdek_index + '" data-id="' + item.id + '" data-index="' + cdek_index + '" data-city="' + cityArr[0] + '" data-val="' + cityArr[0] + '">' + item.cityName + ' <small>[' + item.regionName + ']</small></div>';
        },
        onSelect: function (e, term, item) {
            $('[name=city]').val($(item).data('city'));
            setTimeout(function () {
                miniShop2.Order.add('city', $(item).data('city'));
                $('[name=city]').change();
            }, 500);
            setTimeout(function () {
                miniShop2.Order.add('cdek_id', $(item).data('id'));
            }, 100);
        }
    });
}

jQuery(document).ready(function ($) {
    
    initAutocomplete();

    $('*[name="city"]').attr("autocomplete", "tc-city");
    
});