<form method="post">
    <div class="form-group js_sms_phone">
        <label class="control-label" for="sms_phone">[[%sms_web_phone?&namespace=`sms`&topic=`default`]]</label>
        <input type="text" id="sms_phone" class="form-control" name="phone">
    </div>
    <div class="form-group sms_code js_sms_code">
        <label class="control-label" for="sms_code">[[%sms_web_code?&namespace=`sms`&topic=`default`]]</label>
        <input type="text" id="sms_code" class="form-control" name="code">
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary js_sms_code_send">[[%sms_web_btn_code_send?&namespace=`sms`&topic=`default`]]</button>
        <button type="button" class="btn btn-primary sms_code_btn js_sms_code_check">[[%sms_web_btn_code_check?&namespace=`sms`&topic=`default`]]</button>
    </div>
</form>