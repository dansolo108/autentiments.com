<?php

class msopModificationImage extends xPDOObject
{

    public function getFirstImage($mid = 0)
    {
        if (empty($mid)) {
            $mid = $this->get('mid');
        }

        $q = $this->xpdo->newQuery('msopModificationImage', array(
            'mid' => $mid,
        ));
        $q->limit(1);
        $q->sortby('rank', 'ASC');
        $q->select('image');
        $a = array(
            'image' => 0
        );
        if ($q->prepare() && $q->stmt->execute()) {
            if (!$a = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $a = array(
                    'image' => 0
                );
            }
        }

        return $a;
    }


}