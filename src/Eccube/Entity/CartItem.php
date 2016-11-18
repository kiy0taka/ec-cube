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

/**
 * Class CartItem カート商品
 * @package Eccube\Entity
 */
class CartItem extends \Eccube\Entity\AbstractEntity
{

    private $class_name;
    private $class_id;
    private $price;
    private $quantity;
    private $object;

    public function __construct()
    {
    }

    public function __sleep()
    {
        return array('class_name', 'class_id', 'price', 'quantity');
    }

    /**
     * 規格名を設定
     * @param  string   $class_name 規格名
     * @return CartItem このカート商品自身
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;

        return $this;
    }

    /**
     * 規格名を取得
     * @return string 規格名
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * 規格IDを設定
     * @param  string   $class_id 規格ID
     * @return CartItem このカート商品自身
     */
    public function setClassId($class_id)
    {
        $this->class_id = $class_id;

        return $this;
    }

    /**
     * 規格IDを取得
     * @return string 規格ID
     */
    public function getClassId()
    {
        return $this->class_id;
    }

    /**
     * 価格を設定
     * @param  integer  $price 価格
     * @return CartItem このカート商品自身
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * 価格を取得
     * @return integer 価格
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * 数量を設定
     * @param  integer  $quantity 数量
     * @return CartItem このカート商品自身
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * 数量を取得
     * @return integer 数量
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * 合計金額を取得
     * @return integer 合計金額
     */
    public function getTotalPrice()
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * @param  object   $object
     * @return CartItem
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
}
