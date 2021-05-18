<?php

class PolylangLanguage extends xPDOSimpleObject
{

    /**
     * @param int|modResource $resource
     * @param int|string $scheme -  -1, 'abs', 'full'
     */
    public function redirect($resource, $scheme = 'full')
    {
        $url = $this->makeUrl($resource, $scheme);
        $this->xpdo->sendRedirect($url);
    }

    /**
     * @param int|modResource $resource
     * @param int|string $scheme -  -1, 'abs', 'full'
     * @return string
     */
    public function makeUrl($resource, $scheme = -1)
    {
        $url = '';
        if (is_numeric($resource)) {
            $resource = $this->xpdo->getObject('modResource', $resource);
        }
        if ($resource && $resource instanceof modResource) {
            $siteUrl = $this->getSiteUrl();
            $pageId = $this->xpdo->getOption('site_start');
            $containerSuffix = $this->xpdo->getOption('container_suffix');
            if ($pageId == $resource->get('id')) {
                $url = '';
            } else {
                $url = $resource->get('uri');
            }
            switch ($scheme) {
                case 'abs':
                    $url = $this->getBaseUrl() . $url;
                    break;
                case'full':
                default:
                    $url = $siteUrl . $url;

            }

            if (empty($containerSuffix) && $url != '/') {
                $url = preg_replace("#/$#", "", $url);
            }
        }
        return $url;
    }


    /**
     * @param bool $withSlash
     * @return string
     */
    public function getSiteUrl($withSlash = true)
    {
        $pattern = trim($this->get('site_url'));
        $baseHost = $this->xpdo->getOption('polylang_base_host', null, MODX_HTTP_HOST, true);
        $baseHost .= '/';
        if (strpos($pattern, '/') === 0) {
            $pattern = '[[+schema]][[+base_domain]]' . $pattern;
        } elseif (!(strpos($pattern, '[[+schema]]') === 0 || strpos($pattern, 'http://') === 0 || strpos($pattern, 'https://') === 0)) {
            $pattern = '[[+schema]]' . $pattern;
        }
        $url = str_replace(array('[[+schema]]', '[[+base_domain]]'), array(MODX_URL_SCHEME, trim($baseHost)), $pattern);
        $url = preg_replace("#/$#", '', $url);
        if ($withSlash) {
            $url .= '/';
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        $url = '/';
        $defaultLanguage = $this->xpdo->getOption('polylang_default_language');
        if ($this->get('culture_key') != $defaultLanguage) {
            $url = "/{$this->get('culture_key')}/";
        }
        return $url;
    }


    /**
     * @return bool
     */
    public function isCurrent()
    {
        $currentLanguage = $this->xpdo->getOption('cultureKey');
        return $currentLanguage == $this->get('culture_key');
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        $defaultLanguage = $this->xpdo->getOption('polylang_default_language');
        return $this->get('culture_key') == $defaultLanguage;
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->get('currency_id');
    }
}