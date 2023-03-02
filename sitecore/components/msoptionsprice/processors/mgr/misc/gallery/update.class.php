<?php

class msopModificationImageUpdateProcessor extends modObjectProcessor
{

    public $classKey = 'msopModificationImage';

    const MODE_REMOVE = 'remove';
    const MODE_ADD = 'add';

    /** {@inheritDoc} */
    public function process()
    {
        $mid = (int)$this->getProperty('mid');
        $image = (int)$this->getProperty('image');
        $mode = trim($this->getProperty('mode'));

        if (!$mid OR !$image) {
            return $this->failure('');
        }

        /** @var msopModificationImage $o */
        switch ($mode) {
            case self::MODE_ADD:
                $o = $this->addImage($mid, $image);
                break;
            case self::MODE_REMOVE:
                $o = $this->removeImage($mid, $image);
                break;
        }

        if ($o) {
            $image = $o->getFirstImage();

            if (
                isset($image['image'])
                AND
                $mo = $this->modx->getObject('msopModification', array('id' => $mid))
                AND
                $mo->get('image') != $image['image']
            ) {
                $mo->set('image', $image['image']);
                $mo->save();
            }

            return $this->success('', $image);
        }

        return $this->success('');
    }

    public function addImage($mid, $image)
    {
        /** @var msopModificationImage $o */
        if (!$o = $this->modx->getObject($this->classKey, array(
            'mid'   => $mid,
            'image' => $image,
        ))
        ) {
            $o = $this->modx->newObject($this->classKey);
            $o->fromArray(array(
                'mid'   => $mid,
                'image' => $image,
                'rank'  => $this->modx->getCount($this->classKey, array('mid' => $mid))
            ), '', true, true);
            $o->save();
        }

        return $o;
    }

    public function removeImage($mid, $image)
    {
        /** @var msopModificationImage $o */
        if ($o = $this->modx->getObject($this->classKey, array(
            'mid'   => $mid,
            'image' => $image,
        ))
        ) {
            $o->remove();
        }

        return $o;
    }

}

return 'msopModificationImageUpdateProcessor';