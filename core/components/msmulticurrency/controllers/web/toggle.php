<?php
/**
 * @package msmulticurrency
 */

/**
 * Built-In Validators
 *
 * name                 function                                    parameter                                        example
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * blank                Is field blank?                                                                                nospam|blank
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * required             Is field is not empty?                                                                        username|required
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * password_confirm        Does field match value of other field?        The name of the password field                    password2|password_confirm=password
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * email                Is a valid email address?                                                                   emailaddr|email
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * minLength            Is field at least X characters long?        The min length.                                 password|minLength=6
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * maxLength            Is field no more than X characters long?    The max length.                                 password|maxLength=12
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * minValue             Is field at least X?                        The minimum value.                              donation|minValue=1
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * maxValue             Is field no higher than X?                  The maximum value.                              cost|maxValue=1200
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * contains             Does field contain string X?                The string X.                                   title|contains=Hello
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * strip                Strip a certain string from the field.      The string to strip.                            message|strip=badword
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * stripTags            Strip all tags from the field.              An optional list of allowed tags.               message|stripTags
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 *  allowTags           Note that this is on by default.
 *                      Allow tags in the field.                                                                    content|allowTags
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * isNumber             Is the field a numeric value?                                                               cost|isNumber
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * isDate               Is the field a date?                        An optional format to format the date.          startDate|isDate=%Y-%m-%d
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 * regexp               Does field match an expected format?        A valid regular expression to match against.    secretPin|regexp=/[0-9]{4}/
 * --------------------+-------------------------------------------+-----------------------------------------------+--------------------------------------
 */
class msMultiCurrencyToggleController extends MsMCController
{
    public $placeholders = array();
    /** @var modX $modx */
    public $modx;

    public function initialize()
    {
        $this->modx->lexicon->load('msmulticurrency:validate');
    }

    public function process()
    {
        $out = array();
        $this->loadDictionary();
        $this->loadValidator();
        $mode = $this->dictionary->get('mode');
        $action = $this->dictionary->get('action');
        $format = $this->getProperty('format', 'json', 'isset');
        switch ($action) {
            case 'toggle':
                $cid = $this->dictionary->get('id', 1);
                $ctx = $this->dictionary->get('ctx', '');
                $cultureKey = $this->dictionary->get('cultureKey', $this->modx->getOption('cultureKey'));
                $this->modx->setOption('cultureKey', $cultureKey);
                if ($ctx && $ctx != 'mgr') {
                    $this->modx->switchContext($ctx);
                }
                $currency = $this->msmc->setUserCurrency($cid);
                if ($mode == 'ajax') {
                    $ids = $this->dictionary->get('ids');
                    $out['data']['currency'] = $currency;
                    $out['data']['products'] = $this->msmc->getProductsPriceInCurrency($ids, $cid);
                }
                break;
        }
        if ($this->validator->hasErrors()) {
            $out['error'] = $this->validator->getRawErrors();
        }
        $out['result'] = !empty($out['error']) ? false : true;
        return $format == 'json' ? $out : $out['data'];
    }
}

return 'msMultiCurrencyToggleController';