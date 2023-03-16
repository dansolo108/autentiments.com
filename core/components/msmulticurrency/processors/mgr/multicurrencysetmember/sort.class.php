<?php

class MultiCurrencySetMemberSortProcessor extends modObjectProcessor
{
    public $classKey = 'MultiCurrencySetMember';
    public $languageTopics = array('msmulticurrency:default','msmulticurrency:multicurrencysetmember');
    /** @var MsMC $msmc  */
    public $msmc ;
    private $_sid;

    public function initialize()
{
    $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
    return parent::initialize();
}

    /**
     * @return array|string
     */
    public function process()
    {
        /** @var MultiCurrencySetMember $target */
        if (!$target = $this->modx->getObject($this->classKey, $this->getProperty('target'))) {
            return $this->failure();
        }
        $sources = $this->modx->fromJSON($this->getProperty('sources'));
        if (!is_array($sources)) {
            return $this->failure();
        }

        $this->_sid = $target->get('sid');

        foreach ($sources as $id) {
            /** @var MultiCurrencySetMember $source */
            $source = $this->modx->getObject($this->classKey, $id);
            if ($source->get('sid') == $this->_sid) {
                $target = $this->modx->getObject($this->classKey, $this->getProperty('target'));
                $this->sort($source, $target);
            }
        }
        $this->updateIndex();
        $this->msmc->clearCache();

        return $this->modx->error->success();
    }


    /**
     * @param MultiCurrencySetMember $source
     * @param MultiCurrencySetMember $target
     */
    public function sort(MultiCurrencySetMember $source, MultiCurrencySetMember $target)
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->command('UPDATE');
        $c->where(array(
            'sid' => $this->_sid,
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
        $c = $this->modx->newQuery($this->classKey, array('sid' => $this->_sid));
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
        $c = $this->modx->newQuery($this->classKey, array('sid' => $this->_sid));
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

return 'MultiCurrencySetMemberSortProcessor';