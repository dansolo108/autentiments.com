<?php

/**
 * Sort a msopModificationImage
 */
// It is adapted code from https://github.com/splittingred/Gallery/blob/a51442648fde1066cf04d46550a04265b1ad67da/core/components/gallery/processors/mgr/item/sort.php
class msopModificationImageSortProcessor extends modObjectProcessor
{
    public $classKey = 'msopModificationImage';
    public $permission = '';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function process()
    {
        /* @var msopModificationImage $source */
        $source = $this->modx->getObject($this->classKey,
            array('mid' => $this->getProperty('mid'), 'image' => $this->getProperty('source')));
        /* @var msopModificationImage $target */
        $target = $this->modx->getObject($this->classKey,
            array('mid' => $this->getProperty('mid'), 'image' => $this->getProperty('target')));
        if (empty($source) OR empty($target)) {
            return $this->modx->error->success();
        }

        $table = $this->modx->getTableName($this->classKey);

        if ($source->get('rank') < $target->get('rank')) {
            $this->modx->exec("UPDATE {$table}
				SET rank = rank - 1 WHERE
				    mid = {$this->getProperty('mid')} 
				    AND rank <= {$target->get('rank')}
					AND rank > {$source->get('rank')}
					AND rank > 0
			");
        } else {
            $this->modx->exec("UPDATE {$table}
				SET rank = rank + 1 WHERE
				    mid = {$this->getProperty('mid')} 
				    AND rank >= {$target->get('rank')}
					AND rank < {$source->get('rank')}
			");
        }
        $newRank = $target->get('rank');
        $source->set('rank', $newRank);
        $source->save();
        if (!$this->modx->getCount($this->classKey, array('mid' => $this->getProperty('mid'), 'rank' => 0))) {
            $this->setRanks();
        }

        $image = $source->getFirstImage();

        if (
            isset($image['image'])
            AND
            empty($source->get('rank'))
            AND
            $mo = $source->getOne('Modification')
        ) {
            $mo->set('image', $image['image']);
            $mo->save();
        }

        return $this->success('', $image);
    }

    /** {@inheritDoc} */
    public function setRanks()
    {
        $q = $this->modx->newQuery($this->classKey);
        $q->where(array(
            'mid' => $this->getProperty('mid')
        ));
        $q->select('image');
        $q->sortby('rank ASC, image', 'ASC');
        if ($q->prepare() && $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $sql = '';
            $table = $this->modx->getTableName($this->classKey);
            foreach ($ids as $k => $image) {
                $sql .= "UPDATE {$table} SET `rank` = '{$k}' WHERE `image` = '{$image}';";
            }
            $this->modx->exec($sql);
        }
    }
}

return 'msopModificationImageSortProcessor';