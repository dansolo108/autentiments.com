<?php

class MultiCurrencySetMember extends xPDOSimpleObject
{

    /**
     * @param float $val
     * @return bool
     */
    public function setCourse($val = 0.0)
    {
        $this->set('course', $val);
        $this->set('val', $this->prepareVal($val, $this->get('rate')));
        $this->set('updatedon', time());
        return $this->save();
    }

    /**
     * @return bool
     */
    public function calculateVal()
    {
        return $this->setCourse($this->get('course'));
    }

    /**
     * @param float $course
     * @param string $rate
     * @return float|int
     */
    public function prepareVal($course, $rate)
    {
        $val = $course;
        $rate = str_replace(',', '.', trim($rate));
        if (preg_match('/%$/', $rate)) {
            $add = str_replace('%', '', $rate);
            $add = $course / 100 * $add;
            $val = $course + $add;
        } else if (strpos($rate, '+') !== false || strpos($rate, '-') !== false) {
            $val = $course + (float)$rate;
        } else if (!empty($rate)) {
            $val = $course * $rate;
        }
        return $val;
    }
}