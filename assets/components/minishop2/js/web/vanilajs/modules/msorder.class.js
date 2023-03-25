export default class MsOrder {
    constructor(minishop) {
        this.minishop = minishop;
        this.config = minishop.miniShop2Config;

        this.callbacks = {
            add: this.config.callbacksObjectTemplate(),
            getcost: this.config.callbacksObjectTemplate(),
            clean: this.config.callbacksObjectTemplate(),
            submit: this.config.callbacksObjectTemplate(),
            getrequired: this.config.callbacksObjectTemplate(),
        };

        this.order = document.querySelector('#msOrder');
        this.deliveryInput = 'input[name="delivery"]';
        this.inputParent = '.input-parent';
        this.paymentInput = 'input[name="payment"]';
        this.paymentInputUniquePrefix = '#payment_';
        this.deliveryInputUniquePrefix = '#delivery_';

        this.orderInfoSelector = `.ms2_order_`;

        this.changeEvent = new Event('change', {bubbles: true, cancelable: true});
        this.clickEvent = new Event('click', {bubbles: true, cancelable: true});

        this.initialize();
    }

    initialize() {
        if (this.order) {
            const cleanBtn = this.order.querySelector(`[name="${this.minishop.actionName}"][value="order/clean"]`);

            if (cleanBtn) {
                cleanBtn.addEventListener('click', e => {
                    e.preventDefault();
                    this.clean();
                });
            }
            document.addEventListener("change", e => {
                if (!e.target.closest(`#msOrder`) || (!e.target.closest(`input`) && !e.target.closest(`textarea`)))
                    return;
                e.preventDefault();
                let input = e.target.closest(`input`) || e.target.closest(`textarea`);
                input.value && this.add(input.name, input.value);
            })

            // const deliveryInputChecked = this.order.querySelector(this.deliveryInput + ':checked');
            // if (deliveryInputChecked) {
            //     deliveryInputChecked.dispatchEvent(this.changeEvent);
            // }
        }
    }

    updatePayments(payments) {
        payments = payments.replace(/[\[\]]/g, '').split(',');
        let paymentInputs = this.order.querySelectorAll(this.paymentInput);
        if (paymentInputs) {
            paymentInputs = Array.from(paymentInputs);
            paymentInputs.forEach(el => {
                el.disabled = true;
                MsOrder.hide(el.closest(this.inputParent));
            });

            if (payments.length) {
                for (const i in payments) {
                    const selector = this.paymentInputUniquePrefix + payments[i];
                    const input = paymentInputs.find(item => '#' + item.id === selector);

                    if (input) {
                        input.disabled = false;
                        MsOrder.show(input.closest(this.inputParent));
                    }
                }
            }

            const checked = paymentInputs.filter(el => el.checked && (el.offsetWidth > 0 || el.offsetHeight > 0));
            const visible = paymentInputs.filter(el => (el.offsetWidth > 0 || el.offsetHeight > 0));
            if (!checked.length) {
                visible[0].checked = true;
            }
        }
    }

    add(key, value) {
        const oldValue = value;

        this.callbacks.add.response.success = response => {
            let field = this.order.querySelector(`[name="${key}"]`);

            if (response.data.delivery) {
                this.getrequired(value);
                this.getcost();
                return;
            }

            if (response.data.payment) {
                // field = document.querySelector(this.paymentInputUniquePrefix + response.data[key]);
                // if (response.data[key] !== oldValue) {
                //     field.dispatchEvent(this.clickEvent);
                // } else {
                //     this.getcost();
                // }
                return;
            }
            if (response.data) {
                for (let key in response.data) {
                    let value = response.data[key];
                    document.querySelectorAll(`input[name=${key}]:not([type=radio],:focus)`).forEach(input => {
                        input.value = value;
                        input.classList.remove('has-error');
                        input.closest(this.inputParent).classList.remove('has-error');
                    })
                }
            }
        }

        this.callbacks.add.response.error = () => {
            const field = this.order.querySelector(`[name="${key}"]`);
            if (['checkbox', 'radio'].includes(field.type)) {
                field.closest(this.inputParent).classList.add('has-error');
            } else {
                field.classList.add('has-error');
            }
        };

        const formData = new FormData();
        formData.append('key', key);
        formData.append('value', value);
        formData.append(this.minishop.actionName, 'order/add');
        this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Order.add);
    }

    getcost() {
        this.callbacks.getcost.response.success = response => {
            for (let key in response.data) {
                document.querySelectorAll(this.orderInfoSelector + key).forEach(item => {
                    item.parentElement.style.display = "";
                    if (Boolean(response.data[key])) {
                        item.innerText = parseInt(response.data[key]) ? this.minishop.formatPrice(response.data[key]) : response.data[key];
                    } else if (response.data[key] === 0) {
                        item.innerText = "бесплатно"
                    } else {
                        item.parentElement.style.display = "none";
                    }
                })
            }
        };

        const formData = new FormData();
        formData.append(this.minishop.actionName, 'order/getcost');
        this.minishop.send(formData, this.callbacks.getcost, this.minishop.Callbacks.Order.getcost);
    }

    clean() {
        this.callbacks.clean.response.success = () => location.reload();

        const formData = new FormData();
        formData.append(this.minishop.actionName, 'order/clean');
        this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Order.clean);
    }

    submit(formData) {
        this.minishop.Message.close();

        this.callbacks.submit.before = () => {
            const elements = this.order.querySelectorAll('button, a');
            elements.forEach(el => {
                el.disabled = false
            });
        };

        this.callbacks.submit.response.success = response => {
            setEvent("purchase", {
                "transaction_id": response.data.msorder
            });
            switch (true) {
                case Boolean(response.data.redirect) :
                    document.location.href = response.data.redirect;
                    break;
                case Boolean(response.data.msorder):
                    document.location.href = document.location.origin + document.location.pathname
                        + (document.location.search ? document.location.search + '&' : '?')
                        + 'msorder=' + response.data.msorder;
                    break;
                default:
                    location.reload();
            }
        };

        this.callbacks.submit.response.error = response => {
            setTimeout(() => {
                const elements = this.order.querySelectorAll('button, a');
                elements.forEach(el => {
                    el.disabled = false
                });
            }, 3 * this.minishop.timeout);

            if (this.order.elements) {
                Array.from(this.order.elements).forEach(el => {
                    el.classList.remove('error');
                    el.closest(this.inputParent)?.classList.remove('error');
                });
            }

            for (const i in response.data) {
                if (response.data.hasOwnProperty(i)) {
                    const key = response.data[i];
                    const field = this.order.querySelector(`[name="${key}"]`);

                    if (['checkbox', 'radio'].includes(field.type)) {
                        field.closest(this.inputParent).classList.add('error');
                    } else {
                        field.classList.add('error');
                    }
                }
            }
        };

        return this.minishop.send(formData, this.callbacks.submit, this.minishop.Callbacks.Order.submit);
    }

    getrequired(value) {
        this.callbacks.getrequired.response.success = response => {
            const {requires,hidden_fields} = response.data;

            if (this.order.elements.length) {
                Array.from(this.order.elements).forEach(el => {
                    el.classList.remove('required');
                    let input = el.closest(this.inputParent);
                    if(input){
                        input.classList.remove('required');
                        input.style.display = "";
                    }
                });
            }

            for (const name of requires) {
                if(this.order.elements[name] instanceof NodeList)
                    this.order.elements[name].forEach(item=>{
                        item?.classList.add('required');
                        item?.closest(this.inputParent)?.classList.add('required');
                    })
                else{
                    this.order.elements[name]?.classList.add('required');
                    this.order.elements[name]?.closest(this.inputParent)?.classList.add('required');
                }

            }
            for (const name of hidden_fields) {
                let input = this.order.elements[name]?.closest(this.inputParent);
                if(input)
                    input.style.display = "none";
            }
        };

        this.callbacks.getrequired.response.error = () => {
            if (this.order.elements.length) {
                Array.from(this.order.elements).forEach(el => {
                    el.classList.remove('required');
                    let input = el.closest(this.inputParent);
                    if(input){
                        input.classList.remove('required');
                        input.style.display = "";
                    }
                });
            }
        };

        const formData = new FormData();
        formData.append('id', value);
        formData.append(this.minishop.actionName, 'order/getrequired');
        this.minishop.send(formData, this.callbacks.getrequired, this.minishop.Callbacks.Order.getrequired);
    }
    static hide(node) {
        node.classList.add('ms-hidden');
        node.checked = false;
    }

    static show(node) {
        node.classList.remove('ms-hidden');
    }
}
