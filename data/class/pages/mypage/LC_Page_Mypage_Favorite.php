<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

/**
 * MyPage のページクラス.
 *
 * @author EC-CUBE CO.,LTD.
 *
 * @version $Id$
 */
class LC_Page_Mypage_Favorite extends LC_Page_AbstractMypage_Ex
{
    /** ページナンバー */
    public $tpl_pageno;
    /** @var array */
    public $arrFavorite;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_subtitle = 'お気に入り一覧';
        $this->tpl_mypageno = 'favorite';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        $objCustomer = new SC_Customer_Ex();

        $customer_id = $objCustomer->getValue('customer_id');

        switch ($this->getMode()) {
            case 'delete_favorite':
                // お気に入り削除
                $this->lfDeleteFavoriteProduct($customer_id, (int) $_POST['product_id']);
                break;

            case 'getList':
                // スマートフォン版のもっと見るボタン用
                // ページ送り用
                if (isset($_POST['pageno'])) {
                    $this->tpl_pageno = (int) $_POST['pageno'];
                }
                $this->arrFavorite = $this->lfGetFavoriteProduct($customer_id, $this);
                SC_Product_Ex::setPriceTaxTo($this->arrFavorite);

                // 一覧メイン画像の指定が無い商品のための処理
                foreach ($this->arrFavorite as $key => $val) {
                    $this->arrFavorite[$key]['main_list_image'] = SC_Utils_Ex::sfNoImageMainList($val['main_list_image']);
                }

                echo SC_Utils_Ex::jsonEncode($this->arrFavorite);
                SC_Response_Ex::actionExit();
                break;

            default:
                break;
        }

        // ページ送り用
        if (isset($_POST['pageno'])) {
            $this->tpl_pageno = (int) $_POST['pageno'];
        }
        $this->arrFavorite = $this->lfGetFavoriteProduct($customer_id, $this);
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
    }

    /**
     * お気に入りを取得する
     *
     * @param mixed $customer_id
     * @param LC_Page_Mypage_Favorite $objPage
     *
     * @return array お気に入り商品一覧
     */
    public function lfGetFavoriteProduct($customer_id, &$objPage)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objProduct = new SC_Product_Ex();

        $objQuery->setOrder('f.create_date DESC');
        $where = 'f.customer_id = ? and p.status = 1';
        if (NOSTOCK_HIDDEN) {
            $where .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = f.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
        }
        $arrProductId = $objQuery->getCol('f.product_id', 'dtb_customer_favorite_products f inner join dtb_products p using (product_id)', $where, [$customer_id]);

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($this->lfMakeWhere('alldtl.', $arrProductId));
        $linemax = $objProduct->findProductCount($objQuery);

        $objPage->tpl_linemax = $linemax;   // 何件が該当しました。表示用

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($objPage->tpl_pageno, $linemax, SEARCH_PMAX, 'eccube.movePage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi; // 表示文字列
        $startno = $objNavi->start_row;

        $objQuery = SC_Query_Ex::getSingletonInstance();
        // $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 取得範囲の指定(開始行番号、行数のセット)
        $arrProductId = array_slice($arrProductId, $startno, SEARCH_PMAX);

        $where = $this->lfMakeWhere('', $arrProductId);
        $where .= ' AND del_flg = 0';
        $objQuery->setWhere($where, $arrProductId);
        $arrProducts = $objProduct->lists($objQuery);

        // 取得している並び順で並び替え
        $arrProducts2 = [];
        foreach ($arrProducts as $item) {
            $arrProducts2[$item['product_id']] = $item;
        }
        $arrProductsList = [];
        foreach ($arrProductId as $product_id) {
            $arrProductsList[] = $arrProducts2[$product_id];
        }

        // 税込金額を設定する
        SC_Product_Ex::setIncTaxToProducts($arrProductsList);

        return $arrProductsList;
    }

    /* 仕方がない処理。。 */

    /**
     * @param string $tablename
     */
    public function lfMakeWhere($tablename, $arrProductId)
    {
        // 取得した表示すべきIDだけを指定して情報を取得。
        $where = '';
        if (is_array($arrProductId) && !empty($arrProductId)) {
            $where = $tablename.'product_id IN ('.implode(',', $arrProductId).')';
        } else {
            // 一致させない
            $where = '0<>0';
        }

        return $where;
    }

    // お気に入り商品削除

    /**
     * @param int $product_id
     */
    public function lfDeleteFavoriteProduct($customer_id, $product_id)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $exists = $objQuery->exists('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', [$customer_id, $product_id]);

        if ($exists) {
            $objQuery->delete('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', [$customer_id, $product_id]);
        }
    }
}
