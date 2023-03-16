<?php

class PolylangLanguageToggleProcessor extends modObjectProcessor
{

    public function process()
    {
        /** @var  Polylang $polylang */
        $polylang = $this->modx->getService('polylang', 'Polylang');
        $_SESSION['togglePolylangLanguage'] = true;
        return $polylang->success();
    }

}

return 'PolylangLanguageToggleProcessor';