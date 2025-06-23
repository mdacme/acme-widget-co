<?php

use Mario\AcmeWidget\Basket;
use PHPUnit\Framework\TestCase;
class BasketTest extends TestCase
{
    private $catalogue;
    private $deliveryChargeRules;
    private $offers;
    private $basket;
    protected function setUp(): void
    {
        error_reporting(E_ALL);
        $this->catalogue = [
            'R01' => ['price' => 32.95, 'product' => 'Red Widget'],
            'G01' => ['price' => 24.95, 'product' => 'Green Widget'],
            'B01' => ['price' => 7.95, 'product' => 'Blue Widget']
        ];

        $this->deliveryChargeRules = [
            50 => 4.95,
            90 => 2.95
        ];

        $this->offers = [
            'R01' => 'bogohalfoff'
        ];

        $this->basket = new Basket($this->catalogue, $this->deliveryChargeRules, $this->offers);
    }

    public function testCartWhereItemDoesNotExist()
    {
        $this->expectException(Exception::class);
        $this->basket->addItem("Test123");
    }

    public function testCartSimpleExample()
    {
        $this->basket->addItem('B01');
        $this->assertEquals(12.90, $this->basket->calculateTotal());
    }

    public function testCartExampleWithTwoProducts()
    {
        $this->basket->addItem('B01');
        $this->basket->addItem('G01');
        $this->assertEquals(37.85, $this->basket->calculateTotal());
    }

    public function testCartExampleWithBogoHalfOff()
    {
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->assertEquals(54.38, $this->basket->calculateTotal());
    }

    public function testCartExampleWithThreeBogoHalfOffItems()
    {
        //this tests if the 3rd product will be charged at the regular price
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->assertEquals(85.33, $this->basket->calculateTotal());
    }

    public function testCartExampleWithFourBogoHalfOffItems()
    {
        //this tests if the 3rd product will be charged at the regular price and 4th at half off
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->assertEquals(98.85, $this->basket->calculateTotal());
    }

    public function testCartExampleWithAMixOfProducts()
    {
        //this tests BOGO + a mixed bag of products
        $this->basket->addItem('R01');
        $this->basket->addItem('R01');
        $this->basket->addItem('G01');
        $this->basket->addItem('R01');
        $this->basket->addItem('G01');
        $this->basket->addItem('B01');

        $this->assertEquals(140.23, $this->basket->calculateTotal());
    }
}
