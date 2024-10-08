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
 * カート のページクラス.
 *
 * @author EC-CUBE CO.,LTD.
 *
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Cart extends LC_Page_FrontParts_Bloc_Ex
{
    /** @var bool */
    public $hasDownload;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrProductType = $masterData->getMasterData('mtb_product_type'); // 商品種類を取得
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $objCart = new SC_CartSession_Ex();
        $this->isMultiple = $objCart->isMultiple();
        $this->hasDownload = $objCart->hasProductType(PRODUCT_TYPE_DOWNLOAD);
        // 旧仕様との互換のため、不自然なセットとなっている
        $this->arrCartList = [0 => $this->lfGetCartData($objCart)];
    }

    /**
     * カートの情報を取得する
     *
     * @param  SC_CartSession $objCart カートセッション管理クラス
     *
     * @return array          カートデータ配列
     */
    public function lfGetCartData(&$objCart)
    {
        $products_total = 0;
        $total_quantity = 0;
        $is_deliv_free = false;
        $arrCartKeys = $objCart->getKeys();
        foreach ($arrCartKeys as $cart_key) {
            // 購入金額合計
            $products_total += $objCart->getAllProductsTotal($cart_key);
            // 合計数量
            $total_quantity += $objCart->getTotalQuantity($cart_key);

            // 送料無料チェック
            if (!$this->isMultiple && !$this->hasDownload) {
                $is_deliv_free = $objCart->isDelivFree($cart_key);
            }
        }

        $arrCartList = [];

        $arrCartList['ProductsTotal'] = $products_total;
        $arrCartList['TotalQuantity'] = $total_quantity;

        // 店舗情報の取得
        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $arrCartList['free_rule'] = $arrInfo['free_rule'];

        // 送料無料までの金額
        if ($is_deliv_free) {
            $arrCartList['deliv_free'] = 0;
        } else {
            $deliv_free = $arrInfo['free_rule'] - $products_total;
            $arrCartList['deliv_free'] = $deliv_free;
        }

        return $arrCartList;
    }
}
