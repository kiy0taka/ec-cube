<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Page\Front;

class ProductListPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public function 表示件数設定($num)
    {
        $this->tester->selectOption(['css' => "select[name = 'disp_number']"], "${num}件");

        return $this;
    }

    public function 表示順設定($sort)
    {
        $this->tester->selectOption(['css' => "select[name = 'orderby']"], $sort);

        return $this;
    }

    public function 一覧件数取得()
    {
        $products = $this->tester->grabMultiple(['xpath' => "//*[@class='ec-shelfGrid__item']/a/p[1]"]);

        return count($products);
    }
}
