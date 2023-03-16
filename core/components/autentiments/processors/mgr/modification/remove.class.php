<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class ModificationRemoveProcessor extends modObjectRemoveProcessor
{
    public $checkRemovePermission = true;
    public $objectType = 'Modification';
    public $classKey = 'Modification';
}

return 'ModificationRemoveProcessor';
