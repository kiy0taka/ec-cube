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

class ProductDetailPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I, $id)
    {
        $page = new self($I);

        return $page->goPage('products/detail/'.$id);
    }

    public function カテゴリ選択($categories)
    {
        $xpath = "//*[@class='ec-layoutRole__header']/";
        foreach ($categories as $i => $category) {
            $xpath .= "/ul/li/a[contains(text(), '$category')]/parent::node()";
            $this->tester->waitForElement(['xpath' => $xpath]);
            $this->tester->moveMouseOver(['xpath' => $xpath]);
        }
        $this->tester->click(['xpath' => $xpath]);

        return $this;
    }

    public function サムネイル切替($num)
    {
        $this->tester->click("div.item_nav div.slick-list div.slick-track div.slideThumb:nth-child(${num})");

        return $this;
    }

    public function サムネイル画像URL()
    {
        return $this->tester->grabAttributeFrom('div.item.slick-slide.slick-current.slick-active img', 'src');
    }

    public function 規格選択($array)
    {
        foreach ($array as $index => $option) {
            $this->tester->selectOption(['id' => 'classcategory_id'.($index + 1)], $option);
        }

        return $this;
    }

    /**
     * @param $num|int
     *
     * @return ProductDetailPage
     */
    public function カートに入れる($num)
    {
        $this->tester->fillField(['id' => 'quantity'], $num);
        $this->tester->click(['id' => 'add-cart']);
        $this->tester->wait(1);

        return $this;
    }

    public function お気に入りに追加()
    {
        $this->tester->click('#favorite');

        return $this;
    }
}
