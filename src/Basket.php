<?php

namespace Mario\AcmeWidget;
use Exception;

class Basket
{
    private $productCatalogue = [];
    private $deliveryChargeRules = [];
    private $offers = [];
    private $items = [];

    /**
     * @param array $productCatalogue e.g.
     * [
     * 'R01' => ['price' => 32.95, 'product' => 'Red Widget'],
     * 'G01' => ['price' => 24.95, 'product' => 'Green Widget'],
     * 'B01' => ['price' => 7.95, 'product' => 'Blue Widget']
     * ]
     * @param array $deliveryChargeRules e.g.
     * [
     * 50 => 4.95,
     * 90 => 2.95
     * ]
     * @param array $offers e.g.
     * [
     * 'R01' => 'bogohalfoff'
     * ]
     */
    public function __construct(array $productCatalogue, array $deliveryChargeRules, array $offers)
    {
        $this->productCatalogue = $productCatalogue;
        $this->deliveryChargeRules = $deliveryChargeRules;
        ksort($this->deliveryChargeRules);
        $this->offers = $offers;
    }

    public function getProductCatalogue(): array
    {
        return $this->productCatalogue;
    }

    public function setProductCatalogue(array $productCatalogue): void
    {
        $this->productCatalogue = $productCatalogue;
    }

    public function getDeliveryChargeRules(): array
    {
        return $this->deliveryChargeRules;
    }

    public function setDeliveryChargeRules(array $deliveryChargeRules): void
    {
        $this->deliveryChargeRules = $deliveryChargeRules;
    }

    public function getOffers(): array
    {
        return $this->offers;
    }

    public function setOffers(array $offers): void
    {
        $this->offers = $offers;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @param string $productCode
     * @return void
     * @throws Exception
     */
    public function addItem(string $productCode): void
    {
        if (!array_key_exists($productCode, $this->productCatalogue)) {
            throw new Exception("Product ({$productCode}) does not exist.");
        }
        $this->items[] = $productCode;
    }

    /**
     * @return float
     */
    public function calculateTotal(): float
    {
        if (empty($this->items)) {
            return 0.00;
        }

        $itemCounts = array_count_values($this->items);
        $total = 0.00;
        foreach ($itemCounts as $item => $count) {
            $total += $this->getTotalProductPrice($item, $count);
        }

        $total = $this->calculateDeliveryCharges($total);

        // rounds up with 2 decimals
        return round($total, 2, PHP_ROUND_HALF_UP);
    }

    /**
     * @param $total
     * @return float
     */
    private function calculateDeliveryCharges($total): float
    {
        foreach ($this->deliveryChargeRules as $amount => $deliveryFee) {
            if ($total < $amount) {
                return $total + $deliveryFee;
            }
        }
        return $total;
    }

    /**
     * @param string $item
     * @param int $count
     * @return float
     */
    private function getTotalProductPrice(string $item, int $count): float
    {
        if (!array_key_exists($item, $this->offers)) {
            //no offers for this product
            return $this->productCatalogue[$item]['price'] * $count;
        }

        // will need the $offer once there are more offers to be implemented
        //$offer = $this->offers[$item];

        $price = $this->productCatalogue[$item]['price'];

        // here implement future offers - this one in particular is Buy One Get One Half Off
        return $this->calculatePriceForProductBogoHalfOff($count, $price);
    }

    /**
     * @param int $count
     * @param mixed $price
     * @return float
     */
    private function calculatePriceForProductBogoHalfOff(int $count, mixed $price): float
    {
        $sum = 0.00;
        for ($x = 1; $x <= $count; $x++) {
            if ($x % 2 == 0) {
                $sum += $price / 2;
                continue;
            }
            $sum += $price;
        }
        return $sum;
    }

}
