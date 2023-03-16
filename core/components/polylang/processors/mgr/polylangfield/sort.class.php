<?php

class PolylangPolylangFieldSortProcessor extends modObjectProcessor
{
    public $classKey = 'PolylangField';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang */
    public $polylang;
    private $_class_name;

    public function initialize()
    {
        // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        /** @var PolylangField $target */
        if (!$target = $this->modx->getObject($this->classKey, $this->getProperty('target'))) {
            return $this->failure();
        }
        $sources = $this->modx->fromJSON($this->getProperty('sources'));
        if (!is_array($sources)) {
            return $this->failure();
        }

        $this->_class_name = $className = $this->getProperty('class_name');;

        foreach ($sources as $id) {
            /** @var PolylangField $source */
            $source = $this->modx->getObject($this->classKey, $id);
            if ($source->get('class_name') == $this->_class_name) {
                $target = $this->modx->getObject($this->classKey, $this->getProperty('target'));
                $this->sort($source, $target);
            }
        }
        $this->updateIndex();

        return $this->modx->error->success();
    }


    /**
     * @param PolylangField $source
     * @param PolylangField $target
     */
    public function sort(PolylangField $source, PolylangField $target)
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->command('UPDATE');
        $c->where(array(
            'class_name' => $this->_class_name,
        ));

        if ($source->get('rank') < $target->get('rank')) {
            $c->query['set']['rank'] = array(
                'value' => '`rank` - 1',
                'type' => false,
            );
            $c->andCondition(array(
                'rank:<=' => $target->rank,
                'rank:>' => $source->rank,
            ));
            $c->andCondition(array(
                'rank:>' => 0,
            ));
        } else {
            $c->query['set']['rank'] = array(
                'value' => '`rank` + 1',
                'type' => false,
            );
            $c->andCondition(array(
                'rank:>=' => $target->rank,
                'rank:<' => $source->rank,
            ));
        }
        $c->prepare();
        $c->stmt->execute();

        $source->set('rank', $target->get('rank'));
        $source->save();
    }

    /**
     *
     */
    public function updateIndex()
    {
        // Check if need to update children indexes
        $c = $this->modx->newQuery($this->classKey, array('class_name' => $this->_class_name));
        $c->groupby('rank');
        $c->select('COUNT(rank) as idx');
        $c->sortby('idx', 'DESC');
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            if ($c->stmt->fetchColumn() == 1) {
                return;
            }
        }

        // Update indexes
        $c = $this->modx->newQuery($this->classKey, array('class_name' => $this->_class_name));
        $c->select('id');
        $c->sortby('rank ASC, id', 'ASC');
        if ($c->prepare() && $c->stmt->execute()) {
            $table = $this->modx->getTableName($this->classKey);
            $update = $this->modx->prepare("UPDATE {$table} SET rank = ? WHERE id = ?");
            $i = 0;
            while ($id = $c->stmt->fetch(PDO::FETCH_COLUMN)) {
                $update->execute(array($i, $id));
                $i++;
            }
        }
    }

}

return 'PolylangPolylangFieldSortProcessor';