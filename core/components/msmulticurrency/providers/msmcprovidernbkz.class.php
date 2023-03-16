<?php
require_once(__DIR__ . '/msmcprovider.class.php');

/**
 * Class MsMCProviderNbKz
 * Национальный банк Республики Казахстан
 */
class MsMCProviderNbKz extends MsMCProvider
{

    /**
     * @return array
     */
    public function getCodes()
    {
        $cacheOptions = array(
            'cache_key' => $this->msmc->namespace . 'provider_nbkz_codes',
            'cacheTime' => 0,
        );
        if (!$data = $this->msmc->getCache($cacheOptions)) {
            $data = array();
            if ($xml = $this->getXml()) {
                foreach ($xml->item as $xmlItem) {
                    $data[] = $xmlItem->title->__toString();
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
            foreach ($xml->item as $xmlItem) {
                $val = floatval(str_replace(',', '.', $xmlItem->description->__toString()));
                $val = $val / intval($xmlItem->quant->__toString());
                $data[$xmlItem->title->__toString()] = str_replace(',', '.', $val);
            }
        }
        $code = $this->getBaseCurrencyCode();
        if (!isset($data['KZT']) && isset($data[$code])) {
            $val = 1 / $data[$code];
            $data['KZT'] = number_format($val, 6, '.', '');
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
        $url = $url ? $url : 'http://www.nationalbank.kz/rss/rates_all.xml';
        if ($content = $this->load($url)) {
            if ($output = simplexml_load_string($content)) {
                return $output->channel;
            }
        }
        return $output;
    }


}