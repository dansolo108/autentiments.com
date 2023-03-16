
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