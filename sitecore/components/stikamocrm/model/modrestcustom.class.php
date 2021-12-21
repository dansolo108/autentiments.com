<?php
require_once MODX_CORE_PATH . 'model/modx/rest/modrest.class.php';

class modRestCustom extends modRest {
	
    /**
     * @param string $url
     * @param array $parameters
     * @param array $headers
     * @return RestClientResponse
     */
    public function patch($url, $parameters=array(), $headers=array()){
        if (!empty($this->config['addMethodParameter'])) {
            $parameters['_method'] = "PATCH";
        }
        return $this->execute($url,'PATCH',$parameters, $headers);
    }
}