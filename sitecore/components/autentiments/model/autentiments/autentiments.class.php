<?php
class Autentiments
{
    public modX $modx;
    /** @var pdoFetch|null $pdoTools */
    private $pdoTools;
    /** @var array $config */
    public array $config;
    private array $defaultConfig;
    private pdoFetch $pdoFetch;

    public function __construct(modX &$modx, $config = [])
    {
        $this->modx = &$modx;
        $core_path = $this->modx->getOption('autentiments.core_path', $config, MODX_CORE_PATH . 'components/autentiments/');
        $assets_path = $this->modx->getOption(
            'autentiments.assets_path',
            $config,
            MODX_ASSETS_PATH . 'components/autentiments/'
        );
        $assets_url = $this->modx->getOption('autentiments.assets_url', $config, MODX_ASSETS_URL . 'components/autentiments/');
        $action_url = $this->modx->getOption('autentiments.action_url', $config, $assets_url . 'action.php');
        $connector_url = $assets_url . 'connector.php';

        $this->defaultConfig = array(
            'core_path' => $core_path,
            'assets_path' => $assets_path,
            'model_path' => $core_path . 'model/',
            'processors_path' => $core_path . 'processors/',

            'assets_url' => $assets_url,
            'css_url' => $assets_url . 'css/',
            'js_url' => $assets_url . 'js/',
            'connector_url' => $connector_url,
            'action_url' => $action_url,

            'ctx' => 'web',
            'json_response' => true,
        );
        $this->config = array_merge($this->defaultConfig, $config);
        $this->modx->addPackage('autentiments', $this->config['model_path']);

        if ($this->pdoTools = $this->modx->getService('pdoFetch')) {
            $this->pdoTools->setConfig($this->config);
        }
        /** @var pdoFetch $pdoFetch */
        $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
        $path = $this->modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
        if ($pdoClass = $this->modx->loadClass($fqn, $path, false, true)) {
            $this->pdoFetch = new $pdoClass($modx);
        } else {
            return false;
        }
        $this->modx->lexicon->load('autentiments:default');
    }

    public function OnDocFormPrerender($sp)
    {
        $mode = $this->modx->getOption('mode', $sp);
        if ($mode != 'upd')
            return;
        $res = $this->modx->getObject("modResource", $sp['id']);
        if (!$res || $res->get('class_key') != 'msProduct')
            return;
        $this->modx->controller->addLexiconTopic('autentiments:manager');
        $config = array_intersect_key($this->defaultConfig, $this->config);
        $config['options'] = $this->getProductDetailNames($res->get('id'));
        $config['stores'] = $this->getProductStoreNames($res->get('id'));
        $this->modx->regClientStartupScript($this->config['js_url'] . 'mgr/product/modifications.panel.js?v=0.0.1');
        $this->modx->regClientStartupScript($this->config['js_url'] . 'mgr/product/modifications.grid.js?v=0.0.1');
        //$this->modx->regClientScript($this->config['js_url'] . 'mgr/misc/stikpr.utils.js?v=0.0.1');
        $this->modx->regClientStartupScript('<script>
autentimentsPanel.config = ' . json_encode($config) . ';
var tabs = ["minishop2-product-settings-panel", "minishop2-product-tabs"];
for (var i=0; i<tabs.length; i++) {
    Ext.ComponentMgr.onAvailable(tabs[i], function() {
        this.on("beforerender", function() {
            this.add({
                title: "Модификации"
                ,hideMode: "offsets"
                ,items: [
                    {
                        html: "Модификации товара и остатки",
                        cls: "modx-page-header container",
                        border: false
                    },{
                        xtype: "mspr-grid-modification-remains",
                        cls: (this.id == "minishop2-product-tabs" ? "main-wrapper" : ""),
                        style: (this.id == "minishop2-product-tabs" ? "padding-top: 0px;" : "")
                    }
                ]
            });
        });
    });
}
</script>', true);
    }

    public function mSyncOnProductOffers($sp){
        $resource = $sp['resource'];
        $xml = $sp['xml'];
        /** @var mSyncOfferData $offer */
        $offer = $sp['offer'];
        // ищем модификацию
        /** @var Modification $modification */
        $modification = $this->modx->getObject('Modification',['code'=> (string) $offer->get('article')]);
        $prices = $offer->getMany('Prices');
        $price = $old_price = 0;
        foreach ($prices as $priceObj){
            switch ($priceObj->get('price_name')){
                case 'Цена продажи':
                    $old_price = $priceObj->get('value');
                    break;
                case 'Цена со скидкой':
                    $price = $priceObj->get('value');
                    break;
            }
        }
        if(!($old_price > 0) || $old_price < $price){
            $price = $old_price;
            $old_price = 0;
        }
        $data = [
            'code'=>(string) $offer->get('article'),
            'product_id' => $resource->get('id'),
            'price'=>$price,
            'old_price'=>$old_price,
        ];
        if(empty($modification)){
            $modification = $this->modx->newObject('Modification');
        }
        $modification->fromArray($data);
        if(!$modification->save()){
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'Ошибка при сохранении модификации :'.var_export($modification->toArray(),true));
            return false;
        }
        // Заполняем размер у найденной модификации
        if (isset($xml->ХарактеристикиТовара->ХарактеристикаТовара)) {
            foreach ($xml->ХарактеристикиТовара->ХарактеристикаТовара as $feature) {
                $name = str_replace('ё','е',mb_strtolower((string) $feature->Наименование));
                $data = [
                    'modification_id'=>$modification->get('id'),
                    'name'=>$name,
                    'value'=>str_replace('ё','е',mb_strtolower((string) $feature->Значение)),
                ];
                /** @var ModificationDetail $detail */
                $detail = $this->modx->getObject('ModificationDetail',['modification_id'=>$modification->get('id'),'name'=>$name]);
                if(empty($detail)){
                    $detail = $this->modx->newObject('ModificationDetail');
                }
                $detail->fromArray($data);
                if(!$detail->save()){
                    $this->modx->log(MODX_LOG_LEVEL_ERROR,'Ошибка при сохранении опции :'.var_export($detail->toArray(),true));
                }
            }
        }
        // остатки для каждого склада
        /** @var SimpleXMLElement $storeXML */
        foreach ($xml->Склад as $storeXML) {
            /** @var Store $storeObj */
            $storeObj = $this->modx->getObject('Store',['1c_id'=>(string)$storeXML->attributes()['ИдСклада']]);
            if(empty($storeObj)){
                $this->modx->log(MODX_LOG_LEVEL_ERROR,'Ошибка. Нужно создать новый склад с uuid:'.$storeXML->attributes()['ИдСклада']);
                continue;
            }
            /** @var ModificationRemain $remain */
            $remain = $this->modx->getObject('ModificationRemain',['store_id'=>$storeObj->get('id'),'modification_id'=>$modification->get('id')]);
            $data = [
                'store_id'=>$storeObj->get('id'),
                'modification_id'=>$modification->get('id'),
                'remains'=> (float)$storeXML->attributes()['КоличествоНаСкладе']
            ];
            if(empty($remain)){
                $remain = $this->modx->newObject('ModificationRemain');
            }
            $remain->fromArray($data);
            if(!$remain->save()){
                $this->modx->log(MODX_LOG_LEVEL_ERROR,'Ошибка при сохранении остатков модификации '.var_export($modification->toArray(),1).' на складе:'.var_export($storeObj->toArray(),1));
            }
        }
    }
    public function getProductDetailNames($id){
        if(!is_integer($id))
            return false;
        $product = $this->modx->getObject('msProduct',$id);
        if(empty($product))
            return false;
        // получаем все опции всех модификации
        $this->pdoFetch->setConfig([
            'class' => 'Modification',
            'where' => ['Modification.product_id' => $product->get('id')],
            'innerJoin' => [
                'ModificationDetail' => [
                    'class' => 'ModificationDetail',
                    'on' => 'ModificationDetail.modification_id = Modification.id'
                ],
                'DetailType' => [
                    'class' => 'DetailType',
                    'on' => 'DetailType.id = ModificationDetail.type_id',
                ],
            ],
            'limit' => 0,
            'select' => [
                'DetailType' => 'id,name',
            ],
            'groupby' => 'DetailType.id',
            'return' => 'data'
        ]);
        $result = $this->pdoFetch->run();
        $details = [];
        foreach ($result as $option) {
            $details[] = $option;
        }
        return $details;
    }
    public function getProductStoreNames($id){
        if(!is_integer($id))
            return false;
        $product = $this->modx->getObject('msProduct',$id);
        if(empty($product))
            return false;
        // получаем все остатки всех модификации
        // получаем все опции всех модификации
        $this->pdoFetch->setConfig([
            'class' => 'Modification',
            'where' => ['Modification.product_id' => $product->get('id')],
            'innerJoin'=>[
                'ModificationRemain' => [
                    'class' => 'ModificationRemain',
                    'on' => 'ModificationRemain.modification_id = Modification.id'
                ]
            ],
            'leftJoin' => [
                'Store'=>[
                    'class'=>'Store',
                    'on' => 'Store.id = ModificationRemain.store_id'
                ]
            ],
            'limit' => 0,
            'select' => [
                'Store' => '*',
            ],
            'groupby' => 'ModificationRemain.store_id',
            'return' => 'data'
        ]);
        $result = $this->pdoFetch->run();
        $output = [];
        foreach ($result as $store) {
            $output[] = $store;
        }
        return $output;
    }
    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param string $action Path to processor
     * @param array $data Data to be transmitted to the processor
     *
     * @return mixed The result of the processor
     */
    public function runProcessor($action = '', $data = array())
    {
        if (empty($action)) {
            return false;
        }
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH . 'components/autentiments/processors/';

        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath,
        ));
    }
}