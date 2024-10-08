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
 * 利用規約について のページクラス.
 *
 * @author EC-CUBE CO.,LTD.
 *
 * @version $Id$
 */
class LC_Page_Guide_Kiyaku extends LC_Page_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
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
        $this->lfGetKiyaku((int) $_GET['page'], $this);
    }

    /**
     * 利用規約を取得し、ページオブジェクトに格納する。
     *
     * @param int $index 規約のインデックス
     * @param object &$objPage ページオブジェクト
     * @param LC_Page_Guide_Kiyaku $objPage
     *
     * @return void
     */
    public function lfGetKiyaku($index, &$objPage)
    {
        $objKiyaku = new SC_Helper_Kiyaku_Ex();
        $arrKiyaku = $objKiyaku->getList();

        $number = count($arrKiyaku);
        if ($number > 0) {
            $last = $number - 1;
        } else {
            $last = 0;
        }

        if ($index < 0) {
            $index = 0;
        } elseif ($index > $last) {
            $index = $last;
        }

        $objPage->tpl_kiyaku_title = $arrKiyaku[$index]['kiyaku_title'];
        $objPage->tpl_kiyaku_text = $arrKiyaku[$index]['kiyaku_text'];
        $objPage->tpl_kiyaku_index = $index;
        $objPage->tpl_kiyaku_last_index = $last;
        $objPage->tpl_kiyaku_is_first = $index <= 0;
        $objPage->tpl_kiyaku_is_last = $index >= $last;
    }
}
