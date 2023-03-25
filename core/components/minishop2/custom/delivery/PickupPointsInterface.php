<?php
interface PickupPointsInterface{
    public function getPickupPoints(msOrderInterface $order): array;
}