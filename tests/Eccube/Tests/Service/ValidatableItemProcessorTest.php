<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Tests\Service;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\ShipmentItem;
use Eccube\Service\ItemValidateException;
use Eccube\Service\ValidatableItemProcessor;
use Eccube\Tests\EccubeTestCase;

class ValidatableItemProcessorTest extends EccubeTestCase
{

    /*
     * カートの場合
     *      エラーなら明細丸め処理 ＆ カート画面にエラー表示
     *      正常時は丸め処理しない
     * 購入の場合
     *      エラーなら購入エラーで終了
     *      正常時は丸め処理しない
     */
    public function testValidateCartSuccess()
    {
        $validator = new ValidatableItemProcessorTest_NormalValidator();
        $item = new CartItem();

        $validator->process($item);
        $this->assertFalse($validator->handleCalled);
    }

    public function testValidateCartFail()
    {
        $validator = new ValidatableItemProcessorTest_FailValidator();
        $item = new CartItem();

        $validator->process($item);
        $this->assertTrue($validator->handleCalled);
    }

    public function testValidateOrderSuccess()
    {
        $validator = new ValidatableItemProcessorTest_NormalValidator();
        $item = new ShipmentItem();

        $validator->process($item);
        $this->assertFalse($validator->handleCalled);
    }

    public function testValidateOrderFail()
    {
        $validator = new ValidatableItemProcessorTest_FailValidator();
        $item = new ShipmentItem();

        $validator->process($item);
        $this->assertFalse($validator->handleCalled);
    }
}

class ValidatableItemProcessorTest_NormalValidator extends ValidatableItemProcessor
{

    public $handleCalled = false;

    protected function validate(ItemInterface $item) {}

    protected function handle(ItemInterface $item)
    {
        $this->handleCalled = true;
    }
}

class ValidatableItemProcessorTest_FailValidator extends ValidatableItemProcessor
{

    public $handleCalled = false;

    protected function validate(ItemInterface $item)
    {
        throw new ItemValidateException();
    }

    protected function handle(ItemInterface $item)
    {
        $this->handleCalled = true;
    }

}