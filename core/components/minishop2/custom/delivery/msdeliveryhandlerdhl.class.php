<?php
if(!class_exists('msDeliveryInterface')) {
    require_once MODX_CORE_PATH. 'components/minishop2/handlers/msdeliveryhandler.class.php';
}


class msDeliveryHandlerDhl extends msDeliveryHandler implements msDeliveryInterface {
    public $modx;
    public $ms2;
    
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0) {
        
        $modx = $this->modx;
        $indexFrom = $modx->getOption('stik_cdek_from_index');
        $orderData = $order->get('order');
        $delivery_id = $delivery->get('id');
        $total = $this->ms2->cart->status();
        $country = strtoupper($orderData['country']);
        $receiverCity = $orderData['city'];
        $receiverPostCode = $orderData['index'];
        $cultureKey = $this->modx->cultureKey;
        
        $siteID = 'v62_hIGJLpUpb1';
        $password = '79iLqNQsGi';
        $paymentAccountNumber = '380933116';
        
        $query = $modx->newQuery('stikCountry');
        $query->where( array('ru_name:=' => $country, 'OR:name:=' => $country) );
        $codeCountry = $modx->getObject('stikCountry', $query);
        
        if (!empty($receiverCity) && !empty($receiverPostCode) && !empty($codeCountry)) {
            $date = strtotime("+1 day");
        	$message_time = date("Y-m-d", $date) . "T" . date("H:i:sP");
            $message_ref = '';
            for ($i=0; $i< 30; $i++){
                $message_ref .= rand(0, 9);
            }
            
    	    $country_code = $codeCountry->get('code');
            if( preg_match("/[А-Яа-я]/", $receiverCity) ) { // если город указан на русском, то ищем его английское название
                $q = $this->modx->newQuery('stikCity');
                $q->where(['alternatenames:LIKE' => "%$receiverCity%", 'country_code' => $country_code]);
                $q->limit(1);
                $items = $this->modx->getCollection('stikCity', $q);
                foreach ($items as $item) {
                    $receiverCity = $item->get('asciiname');
                }
            }
            if ($country_code == 'RU') {
            	$url= 'https://xmlpi-ea.dhl.com/XMLShippingServlet?isUTF8Support=true'; //ru
            	
            	$query = '<?xml version="1.0" encoding="utf-8" standalone="no"?>
            	<p:DCTRequest xmlns:p="http://www.dhl.com"
            	xmlns:p1="http://www.dhl.com/datatypes"
            	xmlns:p2="http://www.dhl.com/DCTRequestdatatypes"
            	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            	schemaVersion="2.0" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd">
                   <GetQuote>
                      <Request>
                         <ServiceHeader>
                            <MessageTime>'.$message_time.'</MessageTime>
                            <MessageReference>'.$message_ref.'</MessageReference>
                            <SiteID>'.$siteID.'</SiteID>
                            <Password>'.$password.'</Password>
                         </ServiceHeader>
                         <MetaData>
                            <SoftwareName>3PV</SoftwareName>
                            <SoftwareVersion>2.0</SoftwareVersion>
                        </MetaData>
                      </Request>
                      <From>
                         <CountryCode>RU</CountryCode>
                         <Postalcode>'.$indexFrom.'</Postalcode>
                         <City>MOSCOW</City>
                      </From>
                      <BkgDetails>
                         <PaymentCountryCode>RU</PaymentCountryCode>
                         <Date>'.date('Y-m-d', strtotime("+1 day")).'</Date>
                         <ReadyTime>PT5M</ReadyTime>
                         <DimensionUnit>CM</DimensionUnit>
                         <WeightUnit>KG</WeightUnit>
                         <Pieces xmlns="">
                            <Piece xmlns="">
                                <PieceID>1</PieceID>
                                <Height>32</Height>
                                <Depth>21</Depth>
                                <Width>21</Width>
                                <Weight>'.($total['total_weight'] ?: "1.0").'</Weight>
                            </Piece>
                         </Pieces>
                         <PaymentAccountNumber>'.$paymentAccountNumber.'</PaymentAccountNumber>
                         <IsDutiable>N</IsDutiable>
                         <QtdShp>
                             <GlobalProductCode>N</GlobalProductCode>
                            <LocalProductCode>N</LocalProductCode>
                         </QtdShp>
                      </BkgDetails>
                      <To>
                         <CountryCode>'.strtoupper($country_code).'</CountryCode>
                         <Postalcode>'.$receiverPostCode.'</Postalcode>
                         <City>'.$receiverCity.'</City>
                      </To>
                   </GetQuote>
                </p:DCTRequest>';
            } else {
                	$url= 'https://xmlpi-ea.dhl.com/XMLShippingServlet';
            	
            	$query = '<?xml version="1.0" encoding="UTF-8"?>
                <p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
                  <GetQuote>
                    <Request>
                      <ServiceHeader>
                        <MessageTime>'.$message_time.'</MessageTime>
                        <MessageReference>'.$message_ref.'</MessageReference>
                            <SiteID>'.$siteID.'</SiteID>
                            <Password>'.$password.'</Password>
                      </ServiceHeader>
                    </Request>
                    <From>
                      <CountryCode>RU</CountryCode>
                      <Postalcode>'.$indexFrom.'</Postalcode>
                    </From>
                    <BkgDetails>
                      <PaymentCountryCode>RU</PaymentCountryCode>
                      <Date>'.date('Y-m-d', strtotime("+1 day")).'</Date>
                      <ReadyTime>PT10H21M</ReadyTime>
                      <ReadyTimeGMTOffset>+01:00</ReadyTimeGMTOffset>
                      <DimensionUnit>CM</DimensionUnit>
                      <WeightUnit>KG</WeightUnit>
                      <Pieces>
                        <Piece>
                          <PieceID>1</PieceID>
                          <Height>32</Height>
                          <Depth>21</Depth>
                          <Width>21</Width>
                          <Weight>'. ($total["total_weight"] ? number_format($total["total_weight"], 1, ".", "") : "1.0") .'</Weight>
                        </Piece>
                      </Pieces>
                      <PaymentAccountNumber>'.$paymentAccountNumber.'</PaymentAccountNumber>
                      <IsDutiable>N</IsDutiable>
                      <NetworkTypeCode>AL</NetworkTypeCode>
                      <QtdShp>
                         <GlobalProductCode>D</GlobalProductCode>
                         <LocalProductCode>D</LocalProductCode>
                      </QtdShp>
                    </BkgDetails>
                    <To>
                      <CountryCode>'.strtoupper($country_code).'</CountryCode>
                      <Postalcode>'.$receiverPostCode.'</Postalcode>
                      <City>'.$receiverCity.'</City>
                    </To>
                  </GetQuote>
                </p:DCTRequest>';
            }
        	
        	$ch = curl_init($url);
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
    
            $xml = simplexml_load_string($output);
            // $modx->log(1, print_r($query,1));
            // $modx->log(1, print_r($xml,1));
            if($xml && isset($xml->GetQuoteResponse->BkgDetails->QtdShp->ShippingCharge)){
                $date = '';
                if (isset($xml->GetQuoteResponse->BkgDetails->QtdShp->DeliveryDate->DlvyDateTime)) {
                    $date = $xml->GetQuoteResponse->BkgDetails->QtdShp->DeliveryDate->DlvyDateTime;
                }
                // бесплатная доставка по РФ в зависимости от настройки
                if ($delivery->get('free_delivery_rf') == 1 && in_array(mb_strtolower($country), ['россия','russian federation'])) {
                    //
                } else {
                    $cost = $cost + round($xml->GetQuoteResponse->BkgDetails->QtdShp->ShippingCharge);
                }
                return [$cost, $date];
            } else {
                if($xml && isset($xml->GetQuoteResponse->Note->Condition->ConditionData)){
                    $modx->log(1, 'DHL Error: ' . $xml->GetQuoteResponse->Note->Condition->ConditionData . " Country: $country, City: $receiverCity, Index: $receiverPostCode");
                }
                if($xml && isset($xml->Response->Status->Condition->ConditionData)){
                    $modx->log(1, 'DHL Error: ' . $xml->Response->Status->Condition->ConditionData . " Country: $country, City: $receiverCity, Index: $receiverPostCode");
                }
                return $cost;
            }
        } else {
            return $cost;
        }
    }
}