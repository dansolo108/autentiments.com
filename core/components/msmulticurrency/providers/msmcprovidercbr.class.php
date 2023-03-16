<?php
require_once(__DIR__ . '/msmcprovider.class.php');

/**
 * Class MsMCProviderCbR
 * Центральный банк Российской Федерации
 */
class MsMCProviderCbR extends MsMCProvider
{

    /**
     * @return array
     */
    public function getCodes()
    {
        $cacheOptions = array(
            'cache_key' => $this->msmc->namespace . 'provider_cbr_codes',
            'cacheTime' => 0,
        );
        if (!$data = $this->msmc->getCache($cacheOptions)) {
            $data = array();
            if ($xml = $this->getXml()) {
                foreach ($xml->Valute as $xmlValute) {
                    $data[] = $xmlValute->CharCode->__toString();
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
            foreach ($xml->Valute as $xmlValute) {
                $val = floatval(str_replace(',', '.', $xmlValute->Value->__toString()));
                $val = $val / intval($xmlValute->Nominal->__toString());
                $data[$xmlValute->CharCode->__toString()] = str_replace(',', '.', $val);
            }
        }
        $code = $this->getBaseCurrencyCode();
        if (!isset($data['RUB']) && isset($data[$code])) {
            $val = 1 / $data[$code];
            $data['RUB'] = number_format($val, 6, '.', '');
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
        $url = $url ? $url : 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date('d/m/Y');
        if ($content = $this->load($url)) {
            $output = simplexml_load_string($content);
            $output = $output;
        }
        return $output;
    }


}