<?php


namespace Eccube\Tests\Entity;


use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;

class CartTest extends \PHPUnit_Framework_TestCase
{

    public function testAddCartItem()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 10, 1000));

        self::assertEquals(1, count($cart->getCartItems()));
    }

    public function testAddCartItem_2つ追加()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 10, 1000));
        $cart->addCartItem($this->newItem('2', '商品規格2', 20, 2000));

        self::assertEquals(2, count($cart->getCartItems()));
    }

    public function testGetTotalQuantity()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 10, 1000));
        $cart->addCartItem($this->newItem('2', '商品規格2', 20, 2000));

        self::assertEquals(30, $cart->getTotalQuantity());
    }

    public function testGetTotalQuantity_たくさん追加()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 30, 1000));
        $cart->addCartItem($this->newItem('2', '商品規格2', 20, 2000));

        self::assertEquals(50, $cart->getTotalQuantity());
    }

    public function testGetTotalPrice()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 10, 1000));
        $cart->addCartItem($this->newItem('2', '商品規格2', 20, 2000));

        self::assertEquals(50000, $cart->getTotalPrice());
    }

    public function testGetTotalPrice_たくさん追加()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 20, 1000));
        $cart->addCartItem($this->newItem('2', '商品規格2', 20, 2000));

        self::assertEquals(60000, $cart->getTotalPrice());
    }

    public function testSetCartItem()
    {
        $cart = new Cart();

        $cart->addCartItem($this->newItem('1', '商品規格1', 20, 1000));

        self::assertEquals(20, $cart->getCartItems()[0]->getQuantity());
        self::assertEquals(1000, $cart->getCartItems()[0]->getPrice());

        $cart->setCartItem($this->newItem('1', '商品規格1', 30, 2000));

        self::assertEquals(30, $cart->getCartItems()[0]->getQuantity());
        self::assertEquals(2000, $cart->getCartItems()[0]->getPrice());
    }


    /**
     * @return CartItem
     */
    private function newItem($classId, $className, $quantity, $price)
    {
        $item = new CartItem();
        $item->setClassId($classId);
        $item->setClassName($className);
        $item->setQuantity($quantity);
        $item->setPrice($price);
        return $item;
    }

}
