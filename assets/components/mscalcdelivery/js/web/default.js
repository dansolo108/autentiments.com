class msCalcDelivery{
    config = {
        action_url:"/assets/components/mscalcdelivery/action.php",
        selectors:{
            delivery_items:`[data-msCalcDelivery-items]`,
            required_field:`[data-msCalcDelivery-field]:not([disabled])`
        }
    }
    constructor(config) {
        this.config = {...this.config,...config};

        window.miniShop2.addCallback("Order.add.response.success","msCalcDelivery",(response)=>{
            if(response.data.city){
                this.items.forEach(item=>{
                    item.classList.add("loading");
                })
                this.fetch(this.config.action_url,{},"GET").then(async response=>{
                    let text = await response.text();
                    if(text){
                        this.items.forEach(item=>{
                            item.innerHTML = text;
                            item.classList.remove("loading");
                        })
                    }
                });
            }
        })
    }
    get items(){
        return document.querySelectorAll(this.config.selectors.delivery_items);
    }
    fetch(url, params = {}, method = "POST", headers = {'X-Requested-With': 'XMLHttpRequest'}) {
        let options = {method, headers};
        if (method === 'GET') {
            url = url + "?" + (new URLSearchParams(params).toString());
        } else if(params && Object.keys(params).length !== 0){
            options.body = JSON.stringify(params);
        }
        return fetch(url, options);
    }
}