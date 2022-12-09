var cityAutocomplete;

function initAutocomplete() {
    var dadataToken = "da659a5364a0d433b8a5e2641e6d7f70390f8606";
    cityAutocomplete = new autoComplete({
        selector: 'input[name="city"]',
        minChars: 3,
        source: function (term, response) {
            term = term.toLowerCase();
            let body = {
                query: term,
                from_bound: { "value": "city" },
                to_bound: { "value": "city" },
            };
            let countryElem = $('#country');
            body.locations = [

            ];
            if(countryElem.val()){
                body.locations.push({
                    "country":countryElem.val(),
                });
            }
            let options = {
                method: "POST",
                mode: "cors",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Token " + dadataToken
                },
                body: JSON.stringify(body)
            }
            fetch("https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address", options)
                .then(response => response.text())
                .then(result => {
                    result = JSON.parse(result);
                    result.suggestions.forEach(item=>{
                        //console.log(item.data.region_type_full,item.data.area_type_full,item.data.city_type_full,item.data.settlement_type_full,item.data.street_type_full);
                    })

                    response(result.suggestions);
                })
                .catch(error => console.log("error", error));
        },
        renderItem: function (item, search) {
            return `<div class="autocomplete-suggestion" data-index="${item.data.postal_code}" data-id="${item.data.kladr_id}" data-city="${item.data.city}" data-val="${(item.data.settlement || item.data.city)}">${(item.data.settlement_with_type || item.data.city_with_type)} <small>[${item.data.region_with_type}]</small></div>`;
        },
        onSelect: function (e, term, item) {
            $('[name=city]').val($(item).data('city')).change();
            $('[name=city]').change();
            $('[name=index]').val($(item).data('index'));
            $('[name=index]').change();
            let options = {
                method: "POST",
                mode: "cors",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Token " + dadataToken
                },
                body: JSON.stringify({
                    query: $(item).data('id')
                })
            }
            fetch("https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/delivery", options)
                .then(response => response.text())
                .then(result => {
                    result = JSON.parse(result);
                    if(result.suggestions){
                        miniShop2.Order.add('cdek_id', result.suggestions[0].data.cdek_id);
                    }
                })
                .catch(error => console.log("error", error));

        }
    });
}

jQuery(document).ready(function ($) {
    
    initAutocomplete();

    $('*[name="city"]').attr("autocomplete", "tc-city");
    
});