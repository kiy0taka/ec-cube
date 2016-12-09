<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Exception\NotImplementedException;

/**
 * Class Cart ショッピングカート
 * @package Eccube\Entity
 */
class Cart extends AbstractEntity
{
    /**
     * @var bool
     */
    private $lock = false;

    /**
     * @var CartItem[]
     */
    private $CartItems;

    /**
     * @var string
     */
    private $pre_order_id = null;

    /**
     * @var array
     */
    private $Payments = array();

    public function __construct()
    {
        $this->CartItems = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function getLock()
    {
        return $this->lock;
    }

    /**
     * @param  bool                $lock
     * @return \Eccube\Entity\Cart
     */
    public function setLock($lock)
    {
        $this->lock = $lock;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPreOrderId()
    {
        return $this->pre_order_id;
    }

    /**
     * @param  integer             $pre_order_id
     * @return \Eccube\Entity\Cart
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->pre_order_id = $pre_order_id;

        return $this;
    }

    /**
     * @param  string                  $class_name
     * @param  string                  $class_id
     * @return CartItem
     */
    public function getCartItemByIdentifier($class_name, $class_id)
    {
        foreach ($this->CartItems as $CartItem) {
            if ($CartItem->getClassName() === $class_name && $CartItem->getClassId() === $class_id) {
                return $CartItem;
            }
        }

        return null;
    }

    public function removeCartItemByIdentifier($class_name, $class_id)
    {
        foreach ($this->CartItems as $CartItem) {
            if ($CartItem->getClassName() === $class_name && $CartItem->getClassId() === $class_id) {
                $this->CartItems->removeElement($CartItem);
            }
        }

        return $this;
    }

    /**
     * @return \Eccube\Entity\Cart
     */
    public function clearCartItems()
    {
        $this->CartItems->clear();

        return $this;
    }

    /**
     * @return CartItem[]
     */
    public function getCartItems()
    {
        return $this->CartItems;
    }

    /**
     * @param  CartItem[]          $CartItems
     * @return \Eccube\Entity\Cart
     */
    public function setCartItems($CartItems)
    {
        $this->CartItems = $CartItems;

        return $this;
    }

    /**
     * Get Payments
     *
     * @return array
     */
    public function getPayments()
    {
        return $this->Payments;
    }

    /**
     * Set Payments
     *
     * @param $payments
     * @return Cart
     */
    public function setPayments($payments)
    {
        $this->Payments = $payments;

        return $this;
    }

    /**
     * このカートに入っている商品の金額と数量を変更します。
     * @param  CartItem $CartItem 変更するカート商品
     * @return Cart このカート自身
     */
    public function setCartItem(CartItem $CartItem)
    {
        $find = false;
        foreach ($this->CartItems as $CartItem) {
            if ($CartItem->getClassName() === $AddCartItem->getClassName() && $CartItem->getClassId() === $AddCartItem->getClassId()) {
                $find = true;
                $CartItem
                    ->setPrice($AddCartItem->getPrice())
                    ->setQuantity($AddCartItem->getQuantity());
            }
        }

        if (!$find) {
            $this->addCartItem($AddCartItem);
        }

        return $this;
    }

    /**
     * カートに商品を追加。
     * @param  CartItem $CartItem 追加するカート商品
     * @return Cart このカート自身
     */
    public function addCartItem(CartItem $CartItem)
    {
        // TODO 実装する
        return $this;
    }

    /**
     * このカートに入っている商品の合計金額を取得。
     * @return integer 合計金額
     */
    public function getTotalPrice()
    {
        // TODO 実装する
        return 0;
    }

    /**
     * このカートに入っている商品の合計数を取得。
     * @return integer 商品の合計数
     */
    public function getTotalQuantity()
    {
        // TODO 実装する
        return 0;
    }
}
