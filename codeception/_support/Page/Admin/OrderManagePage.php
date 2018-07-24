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

namespace Page\Admin;

class OrderManagePage extends AbstractAdminPageStyleGuide
{
    public static $検索条件_受注ステータス = ['id' => 'admin_search_order_status'];
    public static $検索結果_メッセージ = '#search_form #search_total_count';
    public static $検索結果_エラーメッセージ = '//*[@id="page_admin_order"]/div[1]/div[3]/div[3]/div/div/div[1]/div/div[1]';
    public static $詳細検索ボタン = '//*[@id="search_form"]/div[1]/div/div/div[3]/a/i/span';

    /**
     * OrderListPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);

        return $page->goPage('/order', '受注一覧受注管理');
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new self($I);

        return $page->atPage('受注一覧受注管理');
    }

    public function 検索($value = '')
    {
        $this->tester->fillField(['id' => 'admin_search_order_multi'], $value);
        $this->tester->click('#search_form #search_submit');

        return $this;
    }

    public function 詳細検索_電話番号($value = '')
    {
        $this->tester->click(self::$詳細検索ボタン);
        $this->tester->wait(1);
        $this->tester->fillField(['id' => 'admin_search_order_phone_number'], $value);
        $this->tester->click('#search_form #search_submit');

        return $this;
    }

    public function 受注CSVダウンロード実行()
    {
        $this->tester->click(['id' => 'csvDownloadDropDown']);
        $this->tester->waitForElementVisible(['id' => 'orderCsvDownload']);
        $this->tester->click(['id' => 'orderCsvDownload']);

        return $this;
    }

    public function 受注CSV出力項目設定()
    {
        $this->tester->click(['id' => 'csvSettingDropDown']);
        $this->tester->waitForElementVisible(['id' => 'orderCsvSetting']);
        $this->tester->click(['id' => 'orderCsvSetting']);

        return $this;
    }

    public function 配送CSVダウンロード実行()
    {
        $this->tester->click(['id' => 'csvDownloadDropDown']);
        $this->tester->waitForElementVisible(['id' => 'shippingCsvDownload']);
        $this->tester->click(['id' => 'shippingCsvDownload']);

        return $this;
    }

    public function 配送CSV出力項目設定()
    {
        $this->tester->click(['id' => 'csvSettingDropDown']);
        $this->tester->waitForElementVisible(['id' => 'shippingCsvSetting']);
        $this->tester->click(['id' => 'shippingCsvSetting']);

        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("#search_result > tbody > tr:nth-child(${rowNum}) a.action-edit");
    }

    public function 一覧_削除()
    {
        $this->tester->click('#form_bulk > div.row.justify-content-between.mb-2 .btn-bulk-wrapper button.btn.btn-ec-delete');

        return $this;
    }

    public function Accept_削除()
    {
        $this->tester->waitForElementVisible(['id' => 'btn_bulk_delete']);
        $this->tester->click('#btn_bulk_delete');

        return $this;
    }

    public function Cancel_削除()
    {
        $this->tester->click('#bulkDeleteModal > div > div > div.modal-footer > button.btn.btn-ec-sub');

        return $this;
    }

    public function 一覧_メール通知($rowNum)
    {
        $this->tester->click(['css' => "#search_result > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3.text-center > div > div:nth-child(1) > a"]);
        $this->tester->waitForElementVisible(['id' => 'sentUpdateModal']);
        $this->tester->wait(2);
        $this->tester->scrollTo(['id' => 'bulkChange']);
        $this->tester->click(['id' => 'bulkChange']);
        $this->tester->wait(5);
        $this->tester->waitForElementVisible(['id' => 'bulkChangeComplete']);

        return $this;
    }

    public function 一覧_選択($rowNum)
    {
        $this->tester->checkOption(['css' => "#search_result > tbody > tr:nth-child(${rowNum}) > td > input[type=checkbox]"]);

        return $this;
    }

    public function 一覧_全選択()
    {
        $this->tester->checkOption('#toggle_check_all');

        return $this;
    }

    public function 個別メール送信($rowNum)
    {
        $this->tester->click(['css' => "#search_result > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3.text-center > div > div:nth-child(1) > a"]);
        $this->tester->waitForElementVisible(['id' => 'sentUpdateModal']);
        $this->tester->wait(2);
        $this->tester->scrollTo(['id' => 'bulkChange']);
        $this->tester->click(['id' => 'bulkChange']);
        $this->tester->wait(5);
        $this->tester->waitForElementVisible(['id' => 'bulkChangeComplete']);

        return $this;
    }

    public function 一括メール送信()
    {
        $this->tester->click(['id' => 'bulkSendMail']);
        $this->tester->waitForElementVisible(['id' => 'sentUpdateModal']);
        $this->tester->wait(1);
        $this->tester->click(['id' => 'bulkChange']);
        $this->tester->wait(5);
        $this->tester->waitForElementVisible(['id' => 'bulkChangeComplete']);

        return $this;
    }

    public function 一覧_注文番号($rowNum)
    {
        return $this->tester->grabTextFrom("#search_result > tbody > tr:nth-child($rowNum) a.action-edit");
    }

    public function 受注ステータス検索($value = '')
    {
        $this->tester->checkOption(['id' => 'admin_search_order_status_'.$value]);
        $this->tester->click('#search_form #search_submit');

        return $this;
    }

    public function 受注ステータス変更($option = [])
    {
        $this->tester->selectOption('#option_bulk_status', $option);
        $this->tester->click('#form_bulk #btn_bulk_status');
        $this->tester->waitForElementVisible('#confirmBulkModal', 5);
        $this->tester->click('#confirmBulkModal button[data-action="execute"]');

        return $this;
    }
}
