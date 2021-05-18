<?php
require_once(__DIR__ . '/msmcprovider.class.php');

/**
 * Class MsMCProviderNbU
 * Центральный банк Украины
 */
class MsMCProviderNbU extends MsMCProvider
{

    /**
     * @return array
     */
    public function getCodes()
    {
        $cacheOptions = array(
            'cache_key' => $this->msmc->namespace . 'provider_nbu_codes',
            'cacheTime' => 0,
        );
        if (!$data = $this->msmc->getCache($cacheOptions)) {
            $data = array();
            if ($xml = $this->getXml()) {
                foreach ($xml->currency as $xmlCurrency) {
                    $data[] = $xmlCurrency->cc->__toString();
                }
            }
            $this->msmc->setCache($data, $cacheOptions);
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function run()
    {
        if (!$data = $this->getCourse()) return false;
        if (!$currencies = $this->getCurrencies()) return false;
        foreach ($currencies as $currency) {
            $code = $currency['code'];
            if (!isset($data[$code])) continue;
            $this->updateCurrencyCourse($data[$code], $currency);
        }
        return true;

    }

    /**
     * @return array
     */
    public function getCourse()
    {
        $data = array();
        if ($xml = $this->getXml()) {
            foreach ($xml->currency as $xmlCurrency) {
                $val = str_replace(',', '.', $xmlCurrency->rate->__toString());
                $data[$xmlCurrency->cc->__toString()] = $val;
            }
        }
        $code = $this->getBaseCurrencyCode();
        if (!isset($data['UAH']) && isset($data[$code])) {
            $val = 1 / $data[$code];
            $data['UAH'] = number_format($val, 6, '.', '');
        }
        return $data;
    }


    /**
     * @param string $url
     * @return SimpleXMLElement
     */
    protected function getXml($url = '')
    {
        $output = null;
        $url = $url ? $url : 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange';
        if ($content = $this->load($url)) {
            $output = simplexml_load_string($content);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error load ' . $url);
        }
        return $output;
    }

}