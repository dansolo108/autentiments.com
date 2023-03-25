//counter
document.addEventListener("click", e => {
    let button = e.target.closest(`.auten-counter__button`);
    if (!button)
        return;
    let counterWrapper = button.closest(`.auten-counter`);
    let input = counterWrapper.querySelector(`.auten-counter__input`);
    if (!input)
        return;
    input.value = Number.parseInt(input.value) + Number.parseInt(button.dataset.count);
    input.dispatchEvent(new Event("change", {bubbles: true}));
})
// input counter validator
document.addEventListener("change", e => {
    if (!e.target.closest(`.ms2_form input[name=count]`))
        return
    let input = e.target.closest(`.ms2_form input[name=count]`);
    let min = parseInt(input.getAttribute("min"));
    let max = parseInt(input.getAttribute("max"));
    if (min && parseInt(input.value) < min) {
        input.value = min;
        e.stopPropagation()
        e.preventDefault();
    }
    if (max && parseInt(input.value) > max) {
        input.value = max;
        e.stopPropagation()
        e.preventDefault();
    }
})
//radio
document.addEventListener("change", e => {
    if (!e.target.closest("input[type=radio]"))
        return;
    let input = e.target;
    let form = input.closest("form");
    if (!form)
        return;
    let otherRadios = form.querySelectorAll(`input[type=radio][name=${input.name}]`)
    otherRadios.forEach(radio => {
        if (radio !== input) {
            radio.dispatchEvent(new CustomEvent("radio-inactive", {
                bubbles: true
            }));
        }
    })
    input.dispatchEvent(new CustomEvent("radio-active", {
        bubbles: true,
    }))
})
document.addEventListener("radio-inactive", e => {
    e.target.closest(".auten-radio")?.classList.remove("checked");
})
document.addEventListener("radio-active", e => {
    e.target.closest(".auten-radio")?.classList.add("checked");
})
document.addEventListener("radio-inactive", e => {
    e.target.closest(`.auten-delivery-method`)?.classList.remove("active")
})
document.addEventListener("radio-active", e => {
    e.target.closest(`.auten-delivery-method`)?.classList.add("active")
})


// select
// при активации радио ставим активным родительский .auten-select-item и .auten-select
document.addEventListener("radio-active", e => {
    let item = e.target.closest(`.auten-select-item`);
    if (!item)
        return
    item.classList.add("checked");
    let select = item.closest(`.auten-select`);
    let active = select.querySelector(`.auten-select__active`);
    active.innerHTML = item.innerText;
    select.classList.remove('active');
})

// при деактивации радио удаляем checked
document.addEventListener("radio-inactive", e => {
    e.target.closest(".auten-select-item")?.classList.remove("checked");
})

// при клике вне .auten-select закрываем все
document.addEventListener("click", e => {
    if (e.target.closest(`.auten-select`))
        return;
    document.querySelectorAll(`.auten-select.active`).forEach(item => {
        item.classList.remove("active")
    })
})

// при клике на .auten-select__active открываем/закрываем
document.addEventListener("click", e => {
    let item = e.target.closest(`.auten-select__active`);
    if (!item)
        return;
    document.querySelectorAll(`.auten-select.active`).forEach(item => {
        item.classList.remove("active")
    })
    item.closest(`.auten-select`).classList.add("active");
})

// при загрузке страницы устанавливаем дефолтные значения
document.querySelectorAll(`.auten-select`).forEach(select => {
    let checked = select.querySelector(`.auten-select-item input:checked`);
    let active = select?.querySelector(`.auten-select__active`);
    if (!select.querySelector(".auten-select__items")?.querySelector(".auten-select-item")) {
        select.classList.add("no-items");
    }
    if (checked) {
        checked.dispatchEvent(new CustomEvent("radio-active", {bubbles: true}))
    } else if (active && active.innerHTML.replace(" ", "") === "") {
        checked = select.querySelector(`.auten-select-item input`);
        checked.dispatchEvent(new CustomEvent("radio-active", {bubbles: true}))
    }
})

//fields
document.addEventListener("focusin", e => {
    let field = e.target.closest(".auten-field");
    if (!field)
        return;
    //let fieldValue = field.querySelector(".auten-field__input").value;
    field.classList.add("focus");
})
document.addEventListener("focusout", e => {
    let field = e.target.closest(".auten-field");
    if (!field)
        return;
    field.classList.remove("focus");
})

// изменение ипнута
document.addEventListener("input", e => {
    let field = e.target.closest(".auten-field.has-error");
    if (!field)
        return
    field.classList.remove("has-error");
})
// модалки
document.addEventListener("click", e => {
    let btn = e.target.closest(`[data-open-modal]`);
    if (!btn)
        return;
    let modalSelector = btn.dataset.openModal;
    let modal = document.querySelector(`.auten-modal${modalSelector}`);
    if (!modal.dispatchEvent(new CustomEvent("modal-open", {bubbles: true})))
        return;

    modal.classList.add(`active`);

})

document.addEventListener("click", e => {
    if (!(e.target.closest(`.auten-modal`) && (e.target === e.target.closest(`.auten-modal`) || e.target.closest(`.auten-modal__close`))))
        return;
    let modal = e.target.closest(`.auten-modal`);
    if (!modal.dispatchEvent(new CustomEvent("modal-close", {bubbles: true})))
        return;
    modal.classList.remove(`active`)
})