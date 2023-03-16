class modMaxma {
    listeners = {
        action:{
            error:({response})=>{
                window.miniShop2.Message.error(response.message)
            },
            success:({prefix})=>{
                if(prefix === "set"){
                    this.getAction("order_bonuses")
                }
            }
        },
        get: {
            order_bonuses: {
                success:({response}) => {
                    document.querySelectorAll(`.maxma_order_bonuses_amount`).forEach(item=>{
                        item.innerText = response.data;
                    })
                },
            }
        },
        set: {
            bonuses: {
                success:({response}) => {
                    this.ms2.Order.getcost();
                    let bonusesBlocks = document.querySelectorAll(`.auten-bonuses`);
                    if(response.data.bonuses){
                        bonusesBlocks.forEach((item)=>{
                            item.classList.add("applied");

                        });
                        document.querySelectorAll(`.maxma_bonuses_applied`).forEach(item=>{
                            item.innerText = response.data.bonuses;
                        })
                    }else{
                        bonusesBlocks.forEach((item)=>{
                            item.classList.remove("applied");
                        });
                    }
                },
            },
            promocode:{
                before:()=>{
                    let inputs = document.querySelectorAll(`input[name=promocode]`);
                    inputs.forEach((item)=>{
                        item.closest(`.maxma_field`).classList.add("inactive");
                    });
                },
                success:({response})=>{
                    let inputs = document.querySelectorAll(`input[name=promocode]`);
                    if(response.data.promocode){
                        inputs.forEach((item)=>{
                            item.closest(`.maxma_field`).classList.add("inactive");
                        });
                    }else{
                        inputs.forEach((item)=>{
                            item.value = "";
                            item.closest(`.maxma_field`).classList.remove("inactive");
                        });
                    }
                    this.ms2.Order.getcost();
                },
                error:({response})=>{
                    let inputs = document.querySelectorAll(`input[name=promocode]`);
                    inputs.forEach((item)=>{
                        item.value = "";
                        item.closest(`.maxma_field`).classList.remove("inactive");
                    });
                }
            }
        },
    }
    config = {
        action_url: "/assets/componenets/modmaxma/action.php",
    }
    ms2;

    constructor(config) {
        this.config = {...this.config, ...config};
        this.ms2 = window.miniShop2;
        document.addEventListener("submit", this.submit.bind(this));
        this.ms2.addCallback("Cart.change.response.success","modMaxma",(response)=>{
            this.getAction("order_bonuses");
        })
        this.ms2.addCallback("Order.getcost.response.success","modMaxma",(response)=>{
            if(response.data.bonuses_discount){
                document.querySelectorAll(`.maxma_bonuses_applied`).forEach(item=>{
                    item.innerText = new Intl.NumberFormat(undefined, {
                        maximumFractionDigits:0,
                    }).format(-response.data.bonuses_discount);
                })
            }
        })
    }

    submit(e) {
        let form = e.target.closest(`.maxma_form`);
        if (!form)
            return;
        let button = e.submitter;
        if (!button)
            return;
        let action = button.dataset.action;
        if (!action)
            return;
        let value = undefined;
        if (button.dataset.value !== undefined){
            value = button.dataset.value;
        }else{
            value = form.querySelector(`input[name=${action}]`)?.value;
        }
        if (value == undefined || value == null)
            return;

        e.preventDefault();
        this.setAction(action,value);
    }

    getAction(action) {
        this.action(action, {},"GET",`get`);
    }

    setAction(action, value) {
        this.action(action, {value: value},"POST",`set`);
    }

    action(action = "", params = {}, method = "POST",prefix=``) {
        let path = (prefix?prefix+`.`:``)+`${action}`;
        this.dispatchEvent([`action.before`,`${path}.before`],{
            action,params,method,prefix
        })
        this.fetch(this.config.action_url, {...params, "modmaxma_action": action}, method).then(async response => {
            if (!response.ok)
                return;
            response = await response.json();
            if (response.success) {
                this.dispatchEvent([`action.success`,`${path}.success`],{
                    response,
                    action,
                    params,
                    method,
                    prefix,
                });
            } else {
                this.dispatchEvent([`action.error`,`${path}.error`],{
                    response,
                    action,
                    params,
                    method,
                    prefix,
                });
            }
        });
    }

    fetch(url, params = {}, method = "POST", headers = {'X-Requested-With': 'XMLHttpRequest'}) {
        let options = {method, headers};
        if (method === 'GET') {
            url = url + "?" + (new URLSearchParams(params).toString());
        } else if (params && Object.keys(params).length !== 0) {
            if(params instanceof FormData){
                options.body = params;
            }else{
                options.body = new FormData();
                for (let key in params){
                    options.body.set(key, params[key]);
                }
            }

        }
        return fetch(url, options);
    }
    dispatchEvent(path,data){
        if(path instanceof Array){
            path.forEach(path=>{
                this.dispatchEvent(path,data);
            })
            return;
        }
        path = path.split(".");
        let element = this.listeners;
        path.forEach(item=>{
            element = element[item];
        })
        if(!element){
            return;
        }
        if(typeof element == "function"){
            element = [element];
        }
        if(element instanceof Array){
            element.forEach(func=>{
                func(data);
            });
        }
    }
    addEventListener(path,func){
        path = path.split(".");
        let element = this.listeners;
        path.forEach(item=>{
            element = element[item];
        })
        if(element instanceof Array){
            element.push(func);
        }
        else{
            element = [func];
        }
    }
}