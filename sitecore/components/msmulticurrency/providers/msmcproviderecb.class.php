<?php
require_once(__DIR__ . '/msmcprovider.class.php');

/**
 * Class MsMCProviderECB
 * European Central Bank
 */
class MsMCProviderECB extends MsMCProvider
{
    /**
     * @return array
     */

    public function getCodes()
    {
        $cacheOptions = array(
            'cache_key' => $this->msmc->namespace . 'provider_ecb_codes',
            'cacheTime' => 0,
        );
        if (!$data = $this->msmc->getCache($cacheOptions)) {
            $data = array();
            if ($xml = $this->getXml()) {
                foreach ($xml->Cube as $xmlItem) {
                    $data[] = $xmlItem["currency"]->__toString();
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
            foreach ($xml->Cube as $xmlItem) {
                $val = floatval(str_replace(',', '.', $xmlItem["rate"]->__toString()));
                $val = 1 / $val;
                $data[$xmlItem["currency"]->__toString()] = str_replace(',', '.', $val);
            }
        }
        return $data;
    }

    /**
     * @param string $url
     * @return SimpleXMLElement
     */
    protected function getXml($url = '')
    {
        $url = $url ? $url : 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
        if ($content = $this->load($url)) {
            $output = simplexml_load_string($content);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error load ' . $url);
        }
        return $output->Cube->Cube;
    }
}