<?php
require_once(__DIR__ . '/msmcprovider.class.php');

/**
 * Class MsMCProviderNbRb
 * Национальный банк Республики Беларусь
 */
class MsMCProviderNbRb extends MsMCProvider
{

    /**
     * @return array
     */
    public function getCodes()
    {
        $cacheOptions = array(
            'cache_key' => $this->msmc->namespace . 'provider_nbrb_codes',
            'cacheTime' => 0,
        );
        if (!$data = $this->msmc->getCache($cacheOptions)) {
            $data = array();
            if ($items = $this->getData()) {
                foreach ($items as $item) {
                    $data[] = $item['Cur_Abbreviation'];
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
        if ($items = $this->getData()) {
            foreach ($items as $item) {
                $val = floatval(str_replace(',', '.', $item['Cur_OfficialRate']));
                $val = $val / intval($item['Cur_Scale']);
                $data[$item['Cur_Abbreviation']] = str_replace(',', '.', $val);
            }
        }
        $code = $this->getBaseCurrencyCode();
        if (!isset($data['BYR']) && isset($data[$code])) {
            $val = 1 / $data[$code];
            $data['BYR'] = number_format($val, 6, '.', '');
        }
        return $data;
    }

    /**
     * @param string $url
     * @return array|mixed
     */
    protected function getData($url = '')
    {
        $url = $url ? $url : 'http://www.nbrb.by/API/ExRates/Rates?Periodicity=0';
        if (!$data = $this->load($url)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error load data from ' . $url);
            return array();
        }
        return $this->modx->fromJSON($data);
    }


}