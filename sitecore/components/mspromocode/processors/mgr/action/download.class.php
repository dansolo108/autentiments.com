<?php

class mspcDownloadCouponsProcessor extends modObjectProcessor
{
    public $languageTopics = array('mspromocode:default');

    public function process()
    {
        if ($ids = $this->getProperty('ids')) {
            $ids = $this->modx->fromJSON($ids);
        }

        if ($this->getProperty('check')) {
            return !empty($ids)
                ? $this->success()
                : $this->failure($this->modx->lexicon('mspromocode_err_ns'));
        }

        $c = $this->modx->newQuery('mspcCoupon', array('action_id:IN' => $ids));
        $c->innerJoin('mspcAction', 'mspcAction', 'mspcAction.id = mspcCoupon.action_id');
        $c->leftJoin('mspcOrder', 'mspcOrder', 'mspcOrder.coupon_id = mspcCoupon.id');
        $c->leftJoin('msOrder', 'msOrder', 'msOrder.id = mspcOrder.order_id');
        $c->select($this->modx->getSelectColumns('mspcCoupon', 'mspcCoupon'));
        $c->select($this->modx->getSelectColumns('mspcAction', 'mspcAction', 'a_', array('name', 'discount', 'begins', 'ends')));
        $c->select($this->modx->getSelectColumns('mspcOrder', 'mspcOrder', 'o_', array('order_id', 'discount_amount', 'createdon')));
        $c->select($this->modx->getSelectColumns('msOrder', 'msOrder', 'o_', array('num')));

        if ($c->prepare() && $c->stmt->execute()) {
            $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($rows,1));

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="promocodes-'.date('Y-m-d His').'.csv"');

            $output = fopen('php://output', 'w');

            foreach ($rows as $row) {
                // если у купона не указана дата начала и окончания - берём у акции
                $row['begins'] =
                    $row['begins'] == '' && isset($row['a_begins'])
                        ? $row['a_begins']
                        : $row['begins'];
                $row['ends'] =
                    $row['ends'] == '' && isset($row['a_ends'])
                        ? $row['a_ends']
                        : $row['ends'];

                $_rows[] = array(
                    'id' => $row['id'],
                    // 'action_id'		=> $row['a_action_id'],
                    'action' => $row['a_name'],
                    'code' => $row['code'],
                    'discount' => $row['discount'],
                    'begins' => $row['begins'],
                    'ends' => $row['ends'],
                    // 'order_id'		=> $row['o_order_id'],
                    'order_num' => $row['o_num'],
                    'activatedon' => $row['o_createdon'],
                );
            }
            $rows = $_rows;
            unset($_rows);

            // делаем заголовки из лексиконов
            foreach (array_keys($rows[0]) as $key) {
                $headers[] = $this->modx->lexicon('mspromocode_action_download_'.$key);
            }

            fputcsv($output, $headers, ';'); // ставим заголовки

            foreach ($rows as $row) {
                // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($row,1));
                fputcsv($output, $row, ';');
            }
        }

        return '';
    }
}

return 'mspcDownloadCouponsProcessor';
