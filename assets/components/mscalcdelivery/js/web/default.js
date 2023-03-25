class msCalcDelivery {
    config = {
        action_url: "/assets/components/mscalcdelivery/action.php",
        selectors: {
            delivery_wrappers: `[data-msCalcDelivery-wrapper]`,
            required_field: `[data-msCalcDelivery-field]:not([disabled])`,
            pickup:`[data-mscalcdelivery-pickup]`,
            pickup_map:`[data-mscalcdelivery-pickup-map]`,
            pickup_points:`[data-mscalcdelivery-pickup-points]`,
            pickup_point:`[data-mscalcdelivery-pickup-point]`,
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
        this.updatePickups();
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
    getDeliveries(handler) {
        this.action("getDeliveries").then(async response => {
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
            this.updatePickups();
        })
    }
    updatePickups(){
        document.querySelectorAll(this.config.selectors.pickup).forEach(this.initPickup.bind(this))
    }
    getPickupPoint(data){
        return this.stringToHtml(`
            <label class="input-parent auten-pickup-point">
                <input type="radio" style="display: none" name="point" value="${data.code}">
                <div class="auten-pickup-point__name">
                    ${data.name}
                </div>
                <div class=" auten-pickup-point__work-time">
                    ${data.work_time}
                </div>
                <div class="auten-pickup-point__address">
                    ${data.nearest_station}
                </div>
                <a href="tel:${data.phones[0]?.number}" class="auten-pickup-point__phone">
                    ${data.phones[0]?.number}
                </a>
                <a href="mailto:${data.email}" class="auten-pickup-point__email">
                    ${data.email}
                </a>
            </label>
        `);
    }
    stringToHtml(string){
        let temp = document.createElement("div");
        temp.innerHTML = string;
        return temp.firstElementChild;
    }
    getDataValue(element){
        for (let key in this.config.selectors){
            let val = element.getAttribute(this.config.selectors[key].replaceAll(/[\[\]]/g,""));
            if(val)
                return val;
        }
        return undefined;
    }
    async initPickup(pickup){
        let deliveryId = this.getDataValue(pickup);
        if(!deliveryId)
            return;
        let response = await this.action("getPickupPoints",{id:deliveryId});
        response = response.json();
        if(!response.success){
            window.miniShop2.Message.error(response.message);
            return;
        }
        console.log(response);
        // let mapWrapper = pickup.querySelector(this.config.selectors.pickup_map);
        // let points = pickup.querySelectorAll(this.config.selectors.pickup_point);
        // if(!mapWrapper || !points)
        //     return;
        //
        // ymaps.ready(()=>{
        //     let map = new ymaps.Map(mapWrapper, {
        //         center: center,
        //         zoom: 11,
        //         controls: []
        //     });
        //     points.forEach(async point=>{
        //         let cords = point.getAttribute(this.config.selectors.pickup_point.replaceAll(/[\[\]]/g,"")).split(",").reverse();
        //         let placemark = new ymaps.Placemark(cords);
        //         point.addEventListener("click",e=>{
        //             map.setCenter(cords,16);
        //         })
        //         placemark.events.add('click', function(event) {
        //             let input = point.querySelector(`input[name=point]`);
        //             input.checked = true;
        //             input.dispatchEvent(new Event("change",{bubbles:true}));
        //             point.scrollIntoView();
        //             map.setCenter(cords,16);
        //         });
        //         map.geoObjects.add(placemark);
        //     })
        //     map.events.add(["actiontickcomplete","wheel"],event=>{
        //         setTimeout(async ()=>{
        //             points.forEach(point=>{
        //                 let cords = point.getAttribute(this.config.selectors.pickup_point.replaceAll(/[\[\]]/g,"")).split(",").reverse();
        //                 if(ymaps.util.bounds.containsPoint(map.getBounds(),cords))
        //                     point.style.display = "";
        //                 else
        //                     point.style.display = "none";
        //             })
        //         },250)
        //     })
        //     pickup.closest(`.auten-modal`)?.addEventListener("modal-open",e=>{
        //         let activePoint = pickup.querySelector(this.config.selectors.pickup_point+`.active`);
        //         if(activePoint){
        //             let cords = activePoint.getAttribute(this.config.selectors.pickup_point.replaceAll(/[\[\]]/g,"")).split(",").reverse();
        //             activePoint.scrollIntoView();
        //             map.setCenter(cords,16);
        //         }
        //     });
        // })
    }
}