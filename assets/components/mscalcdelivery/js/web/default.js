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
    async action(action = "", params = {}) {
        let response = await this.fetch(this.config.action_url, {...params, "msCalcDelivery_action": action});
        response = await response.json();
        if(!response.success){
            if(window.miniShop2.Message){
                window.miniShop2.Message.error(response.message);

            }else{
                window.addEventListener("miniShop2Initialize",e=>{
                    window.miniShop2.Message.error(response.message);
                })
            }
        }
        return response;
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
    async updateDeliveries(){
        this.wrappers?.forEach(item => {
            item.classList.add(this.config.states.loading);
        })
        let response = await this.action("getDeliveries");
        if(!response.success)
            return;
        this.wrappers.forEach(item=>{
            item.innerHTML = response.data;
            item.classList.remove(this.config.states.loading);
        })
        this.updatePickups();
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
        let mapWrapper = pickup.querySelector(this.config.selectors.pickup_map);
        let pointsWrapper = pickup.querySelector(this.config.selectors.pickup_points);
        if(!mapWrapper || !pointsWrapper)
            return;
        let response = await this.action("getPickupPoints",{delivery_id:deliveryId});
        if(!response.success)
            return;
        let points = response.data.points;
        let activePoint = response.data.active;
        ymaps.ready(()=>{
            let center = [points[0].location.latitude,points[0].location.longitude];
            let map = new ymaps.Map(mapWrapper, {
                center:center,
                zoom: 11,
                controls: []
            });
            pickup.addEventListener("mscalcdelivery-pickup-open",e=>{
                map.container.fitToViewport();
            });
            points.forEach((point,index,array)=>{
                let cords = [point.location.latitude,point.location.longitude];
                let pointElement = pointsWrapper.appendChild(this.getPickupPoint(point));
                let pointInput = pointElement.querySelector(`input[type=radio][name=point]`);
                let placemark = new ymaps.Placemark(cords);
                pointElement.addEventListener("radio-active",e=>{
                    map.setCenter(cords,16);
                })
                placemark.events.add('click', function(event) {
                    pointInput.checked = true;
                    pointInput.dispatchEvent(new Event("change",{bubbles:true}));
                    pointElement.scrollIntoView();
                    map.setCenter(cords,16);
                });
                map.geoObjects.add(placemark);
                if(activePoint === point.code){
                    pointInput.checked = true;
                    pointInput.dispatchEvent(new Event("change",{bubbles:true}));
                    pointElement.scrollIntoView();
                    map.setCenter(cords,16);
                }
                if(array.length - 1 === index)
                    map.container.fitToViewport();
            })
            map.events.add(["actiontickcomplete","wheel"],event=>{
                setTimeout(async ()=>{
                    points.forEach(point=>{
                        let cords = [point.location.latitude,point.location.longitude];
                        let pointElement = pointsWrapper.querySelector(`input[name=point][value="${point.code}"]`).closest(`.input-parent`)
                        if(ymaps.util.bounds.containsPoint(map.getBounds(),cords))
                            pointElement.style.display = "";
                        else
                            pointElement.style.display = "none";
                    })
                },250)
            })
        })
    }
}