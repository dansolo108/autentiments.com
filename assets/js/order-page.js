
// берем у нажатой с аттрибутом кнопки значение аттрибута и прокидываем step
document.addEventListener("click", e => {
    let button = e.target.closest(`[data-go-step]`);
    if (button?.dataset.goStep) {
        location.hash = button?.dataset.goStep;
    }
})

// кнопка назад
document.addEventListener("click", e=> {
    let backButton = e.target.closest(".auten-back");
    if (!backButton)
        return;
    e.preventDefault();
    window.history.back();
})
// назад-вперед браузера
window.addEventListener("popstate", e=> {
    let id = location.hash?.replace("#","");
    if(!id)
        id = "cart"
    let steps = document.querySelectorAll(`[data-step][data-step-open]`);
    steps.forEach(step => {
        step.removeAttribute("data-step-open");
    })
    let stepsOn = document.querySelectorAll(`[data-step="${id}"]`);
    stepsOn.forEach(step => {
        step.setAttribute("data-step-open", true);
    })
})

// перезагрузка страницы, отображает элементы с хэша ссылки или же загружает элементы по умолчанию (cart)
window.addEventListener("DOMContentLoaded", e => {
    if (!location.hash) {
        location.hash = "cart";
    }
    window.dispatchEvent(new PopStateEvent("popstate"));
})
document.addEventListener("click",e=>{
    let button = e.target.closest(`.auten-promo-mobile__pre`);
    if(!button)
        return;
    let parent = button.closest(`.auten-promo-mobile`);
    parent.classList.add(`active`);
    parent.querySelector(`input[name=promocode]`).focus();
})
// исправляем обновление доставки при вводе
let cityTimeOut;
let lastChangeVal;
document.addEventListener("input",e=>{
    let input = e.target.closest(`input[name=city]`)
    if (!input)
        return;
    if(cityTimeOut){
        clearTimeout(cityTimeOut);
    }
    cityTimeOut = setTimeout(()=>{
        input.dispatchEvent(new Event("change",{
            bubbles:true,
        }));
    },500);
})
document.addEventListener("change",e=>{
    let input = e.target.closest(`input[name=city]`)
    if (!input)
        return;
    if(cityTimeOut){
        clearTimeout(cityTimeOut);
    }
    if (input.value === lastChangeVal)
        return;
    lastChangeVal = input.value;
})
// выбор пвз
document.addEventListener("radio-active",e=>{
    let point = e.target.closest(`.auten-pickup-point`);
    if(!point)
        return;
    point?.classList.add(`active`);
    let btn = document.createElement("div");
    btn.classList.add("auten-modal__close","auten-modal__button");
    btn.textContent = "Выбрать этот пункт";
    point.insertAdjacentElement('afterend', btn);
    let delivery = document.querySelector(`input[name=delivery]:checked`);
    let method = delivery.closest(`.auten-delivery-method`);
    // method?.querySelector(`.auten-delivery-method__pickup`)?.classList.add("outline");
    method.querySelector(`.auten-delivery-method__pickup`).textContent = "Изменить пункт";
})
document.addEventListener("radio-inactive",e=> {
    let target = e.target.closest(`.auten-pickup-point`)
    target?.classList.remove(`active`);
    target.nextElementSibling?.remove();
})
document.addEventListener("change",e=>{
    let method = e.target.closest(`.auten-delivery-method`);
    if(!method || !e.target.closest(`input[name=delivery]`))
        return;
    // method.querySelector(`.auten-delivery-method__pickup`)?.classList.remove("outline");
    method.querySelector(`.auten-delivery-method__pickup`).textContent = "Выбрать пункт";
})

document.addEventListener("modal-open",e=>{
    let pickup = e.target.querySelector(`[data-mscalcdelivery-pickup]`)
    if(!pickup)
        pickup = e.target.closest(`[data-mscalcdelivery-pickup]`)
    if(!pickup)
        return;
    pickup.dispatchEvent(new CustomEvent("mscalcdelivery-pickup-open",{bubbles:true}))
})