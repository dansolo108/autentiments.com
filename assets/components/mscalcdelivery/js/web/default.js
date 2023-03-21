class msCalcDelivery {
    config = {
        action_url: "/assets/components/mscalcdelivery/action.php",
        selectors: {
            delivery_wrappers: `[data-msCalcDelivery-wrapper]`,
            required_field: `[data-msCalcDelivery-field]:not([disabled])`,
        },
        states:{
            loading: `msCalcDelivery-loading`,
        }
    }
    callbacks = {
        "Order.add.response.success":(response)=>{
            if(response.data["city"] !== undefined){
                this.updateDeliveries()
            }
        },
        "Cart.change.response.success":this.updateDeliveries.bind(this)
    };

    constructor(config) {
        this.config = {...this.config, ...config};
        for (var key in this.callbacks) {
            window.miniShop2.addCallback(key, "msCalcDelivery", this.callbacks[key]);
        }
    }

    // init(){
    //     window.dispatchEvent(new CustomEvent("msCalcDeliveryBeforeInit",{detail:this}));
    //
    // }
    get wrappers() {
        return document.querySelectorAll(this.config.selectors.delivery_wrappers);
    }
    action(action = "", params = {}) {
        return this.fetch(this.config.action_url, {...params, "msCalcDelivery_action": action});
    }
    fetch(url, params = {}, method = "POST", headers = {'X-Requested-With': 'XMLHttpRequest'}) {
        let options = {method, headers};
        if (method === 'GET') {
            url = url + "?" + (new URLSearchParams(params).toString());
        } else if (params && Object.keys(params).length !== 0) {
            options.body = JSON.stringify(params);
        }
        return fetch(url, options);
    }
    getDeliveries(handler) {
        this.action().then(async response => {
            let result = await response.text();
            handler(result);
        });
    }
    updateDeliveries(){
        this.wrappers?.forEach(item => {
            item.classList.add(this.config.states.loading);
        })
        this.getDeliveries((result)=>{
            this.wrappers.forEach(item=>{
                item.innerHTML = result;
                item.classList.remove(this.config.states.loading);
            })
        })
    }

}