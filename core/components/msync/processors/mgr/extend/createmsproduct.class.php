<?php
/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the msProduct type
 *
 * @package mSync
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'components/minishop2/processors/mgr/product/create.class.php';

class extendModResourceCreateProcessor extends modResourceCreateProcessor
{

	public static function getInstance(modX &$modx, $className, $properties = array())
	{
		$classKey = !empty($properties['class_key']) ? $properties['class_key'] : 'modDocument';
		$object = $modx->newObject($classKey);

		$className = 'msProductDisableCacheCreateProcessor';
		$processor = new $className($modx, $properties);
		return $processor;
	}
}

class msProductDisableCacheCreateProcessor extends msProductCreateProcessor
{

	public function checkParentPermissions() {
	    //FIX disable checkParentPermissions to add children
        //	return parent::checkParentPermissions();
		return true;
	}

	public function afterSave() {
        $parentSave = parent::afterSave();
        if (!$parentSave) return false;

        $alias = $this->object->get('alias');
        $id = $this->object->get('id');
        if ($alias == $id) {
            return true;
        }

        if ($this->modx->getOption('msync_alias_with_id', false)) {
            $alias .= '-' . $id;
            $this->object->set('alias', $alias);
            $this->object->save();
        }

        return true;
	}

	public function cleanup() {
		return $this->success('', array('id' => $this->object->get('id')));
	}

    public function success($msg = '',$object = null) {
        $result = parent::success($msg, $object);
        unset($this->object);
        return $result;
    }
}

return 'extendModResourceCreateProcessor';