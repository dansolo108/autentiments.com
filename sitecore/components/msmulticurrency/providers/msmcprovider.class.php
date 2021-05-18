<?php

abstract class MsMCProvider
{
    /** @var modX $modx */
    public $modx;
    /** @var MsMC $msmc */
    public $msmc;
    /** @var array $config */
    public $config;

    /**
     * MsMCProvider constructor.
     * @param MsMC $msmc
     * @param array $config
     */
    function __construct(MsMC &$msmc, array $config = array())
    {
        $this->msmc = &$msmc;
        $this->modx = &$msmc->modx;
        $this->config = array_merge(array(), $config);
        $this->modx->lexicon->load('msmulticurrency:default');
    }

    /**
     * @param int $setId
     * @return array
     */
    public function getCurrencies($setId = 0)
    {
        $result = array();
        $q = $this->modx->newQuery('MultiCurrencySetMember');
        $q->leftJoin('MultiCurrency', 'MultiCurrency', '`MultiCurrency`.`id` = `MultiCurrencySetMember`.`cid`');
        $q->select($this->modx->getSelectColumns('MultiCurrencySetMember', 'MultiCurrencySetMember'));
        $q->select($this->modx->getSelectColumns('MultiCurrency', 'MultiCurrency', '', array('id'), true));
        $q->where(array(
            'enable' => 1,
            'auto' => 1,
            'base' => 0,
        ));
        if ($setId) $q->where(array('sid' => $setId));

        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;

    }

    /**
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        $code = '';
        if ($currency = $this->msmc->getBaseCurrency()) {
            $code = $currency['code'];
        }
        return $code;
    }

    /**
     * @param float $course
     * @param array $currency
     * @return bool|int
     */
    public function updateCurrencyCourse($course = 0.0, $currency = array())
    {
        $table = $this->modx->getTableName('MultiCurrencySetMember');
        $val = $this->prepareVal($course, $currency['rate']);
        $val = str_replace(',', '.', $val);
        $updatedon = time();
        $sql = "UPDATE {$table} SET `course`= {$course}, `val`= {$val}, `updatedon` = {$updatedon}  WHERE id= {$currency['id']}";
        //$this->modx->log(modX::LOG_LEVEL_ERROR, '$sql=' . $sql);
        return $this->modx->exec($sql);
    }


    /**
     * @param float $course
     * @param string $rate
     * @return float|int
     */
    public function prepareVal($course, $rate)
    {
        $val = $course;
        $rate = str_replace(',', '.', trim($rate));
        if (preg_match('/%$/', $rate)) {
            $add = str_replace('%', '', $rate);
            $add = $course / 100 * $add;
            $val = $course + $add;
        } else if (strpos($rate, '+') !== false || strpos($rate, '-') !== false) {
            $val = $course + (float)$rate;
        } else if (!empty($rate)) {
            $val = $course * $rate;
        }
        return $val;
    }

    /**
     * @param string $url
     * @return string
     */
    public function load($url)
    {
        $headers = array(
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6',
            'Keep-Alive: 300',
            'Connection: keep-alive',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!$output = curl_exec($ch)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error load data. Message:\n" . curl_error($ch) . "\nError:\n" . curl_errno($ch));
            $output = '';
        }
        return $output;
    }

    /**
     * @return array
     */
    abstract public function getCodes();

    /**
     * @return array
     */
    abstract public function getCourse();

    /**
     * @return boolean
     */
    abstract public function run();


}