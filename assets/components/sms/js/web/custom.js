if (typeof $.jGrowl == 'undefined') {
    var smsjGrowl = document.createElement('script');
    smsjGrowl.src = SMSConfig['componentUrl'] + 'js/web/lib/jgrowl/jquery.jgrowl.min.js';
    document.body.appendChild(smsjGrowl);
    smsjGrowl = document.createElement('link');
    smsjGrowl.rel = 'stylesheet';
    smsjGrowl.type = 'text/css';
    smsjGrowl.href = SMSConfig['componentUrl'] + 'js/web/lib/jgrowl/jquery.jgrowl.min.css';
    document.head.appendChild(smsjGrowl);
}

var SMS = {
    config: {
        phone: '.js_sms_phone',
        code: '.js_sms_code',
        btnCodeSend: '.js_sms_code_send',
        btnCodeCheck: '.js_sms_code_check',
        action: SMSConfig['componentUrl'] + 'action.php',
        noMsg: false,
        codeLength: 4,
    },
    initialize: function () {
        var $this = this;
        this.setConfig(SMSConfig);
        $('body').on('click', this.config.btnCodeSend, function () {
            $this.codeSend(this);
            return false;
        });
        $('body').on('click', this.config.btnCodeCheck, function () {
            $this.codeCheck(this);
            return false;
        });
        $('body').on('click', '.js_sms_resend_code', function (e) {
            e.preventDefault();
            $this.codeSend(this);
            $('.custom-code-link-box').hide();
            $('.js_sms_buttons_group').hide();
        });
        $('.sms_phone_input').keydown(function(e){
            if(e.keyCode == 13){
                e.preventDefault();
                $this.codeSend(this);
                return false;
            }
        });
        $('.sms_code_input').keyup(function(e){
            if ($(this).val().length == $this.config.codeLength) {
                $this.codeCheck(this);
            }
            if(e.keyCode == 13){
                e.preventDefault();
                $this.codeCheck(this);
                return false;
            }
        });
    },
    startCounter: function (config) {
        $('.count-seconds-wrapper').show();
        $('.js_sms_buttons_group').hide();
        let seconds = $('.count-seconds span');
        let counter = 60; // повторная отправка через 1 минуту
        timer_id = setInterval(() => {
            counter--;
            seconds.text(counter);
            if (counter === 0) {
                clearInterval(timer_id);
                $('.count-seconds-wrapper').hide();
                $('.custom-code-link-box').show();
                $('.js_sms_buttons_group').show();
            }
        }, 1000);
    },
    setConfig: function (config) {
        if (typeof config == 'object') {
            for (var key in config) {
                this.config[key] = config[key];
            }
        }
    },
    codeSend: function (elem) {
        var $this = this;
        var form = $(elem).closest('form');
        var values = form.serializeArray();
        values.push({name: 'type', value: 'sendCode'});
        $.post(this.config.action, values, function (response, status, obj) {
            try {
                response = typeof response == 'object' ? response : JSON.parse(response);
            } catch (e) {
                return false;
            }
            if (response.success) {
                
                $($this.config.btnCodeSend).hide();	
                $($this.config.phone).hide();
                $($this.config.code).show();
                $this.startCounter();
            }
            $this.getMessage(response);
            $(document).trigger('smsCodeSend', {response: response, form: form, values: values});
        });
    },
    codeCheck: function (elem) {
        var $this = this;
        var form = $(elem).closest('form');
        var values = form.serializeArray();
        values.push({name: 'type', value: 'checkCode'});
        $.post(this.config.action, values, function (response, status, obj) {
            try {
                response = typeof response == 'object' ? response : JSON.parse(response);
            } catch (e) {
                return false;
            }
            $this.getMessage(response);
            $(document).trigger('smsCodeCheck', {response: response, form: form, values: values});
            if (response.success) {
                document.location.reload();
            }
        });
    },
    getMessage: function (response) {
        if (response.message && !this.config.noMsg) {
            if (response.success) {
                this.msgSuccess(response.message);
            } else {
                this.msgError(response.message);
            }
        }
    },
    msgSuccess: function (message) {
        $.jGrowl(message, {
            theme: 'sms-message-success',
            sticky: false
        });
    },
    msgError: function (message) {
        $.jGrowl(message, {
            theme: 'sms-message-error',
            sticky: false
        });
    },
    msgInfo: function (message) {
        $.jGrowl(message, {
            theme: 'sms-message-info',
            sticky: false
        });
    }
};
$(document).ready(function () {
    SMS.initialize();
});