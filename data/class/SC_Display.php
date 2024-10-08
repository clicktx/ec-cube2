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
 * Http コンテンツ出力を制御するクラス.
 *
 * @author Ryuichi Tokugami
 *
 * @version $Id$
 */
class SC_Display
{
    public $response;

    /** 端末種別を保持する */
    // XXX プロパティとして保持する必要があるのか疑問。
    public static $device;

    /** SC_View インスタンス */
    public $view;

    public $deviceSeted = false;

    /*
     * TODO php4を捨てたときに ここのコメントアウトを外してね。
     * const('MOBILE',1);
     * const('SMARTPHONE',2);
     * const('PC',10);
     * const('ADMIN',99);
     */

    public function __construct()
    {
        $this->response = new SC_Response_Ex();
    }

    /**
     * @deprecated 2.18.0 本体では利用していない。
     */
    public function setPrevURL()
    {
        $objCartSess = new SC_CartSession_Ex();
        $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
    }

    /**
     * LC_Page のパラメーターを, テンプレートに設定し, 出力の準備を行う.
     *
     * @param LC_Page $page LC_Page インスタンス
     * @param $is_admin boolean 管理画面を扱う場合 true
     */
    public function prepare($page, $is_admin = false)
    {
        if (!$this->deviceSeted || !is_null($this->view)) {
            $device = ($is_admin) ? DEVICE_TYPE_ADMIN : $this->detectDevice();
            $this->setDevice($device);
        }
        $this->assignobj($page);
        $this->view->setPage($page);
        $this->response->setResposeBody($this->view->getResponse($page->getTemplate()));
    }

    /**
     * リロードを行う.
     *
     * SC_Response::reload() のラッパーです.
     *
     * @deprecated SC_Response::reload() を使用してください
     */
    public function reload($queryString = [], $removeQueryString = false)
    {
        SC_Response_Ex::reload($queryString, $removeQueryString);
    }

    public function noAction()
    {
        return;
    }

    /**
     * ヘッダを追加する.
     */
    public function addHeader($name, $value)
    {
        $this->response->addHeader($name, $value);
    }

    /**
     * デバイス毎の出力方法を自動で変更する、ファサード
     * Enter description here ...
     */
    public function setDevice($device = DEVICE_TYPE_PC)
    {
        switch ($device) {
            case DEVICE_TYPE_MOBILE:
                if (USE_MOBILE === false) {
                    exit;
                }
                $this->response->setContentType('text/html');
                $this->setView(new SC_MobileView_Ex());
                break;
            case DEVICE_TYPE_SMARTPHONE:
                $this->setView(new SC_SmartphoneView_Ex());
                break;
            case DEVICE_TYPE_PC:
                $this->setView(new SC_SiteView_Ex());
                break;
            case DEVICE_TYPE_ADMIN:
                $this->setView(new SC_AdminView_Ex());
        }
        $this->deviceSeted = true;
    }

    /**
     * SC_View インスタンスを設定する.
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * 端末種別を判別する。
     *
     * SC_Display::MOBILE = ガラケー = 1
     * SC_Display::SMARTPHONE = スマホ = 2
     * SC_Display::PC = PC = 10
     *
     * @static
     *
     * @param          $reset boolean
     *
     * @return int 端末種別ID
     */
    public static function detectDevice($reset = false)
    {
        if (is_null(SC_Display_Ex::$device) || $reset) {
            // $nu = new Net_UserAgent_Mobile();
            $su = new SC_SmartphoneUserAgent_Ex();
            if ($su->isSmartphone()) {
                SC_Display_Ex::$device = DEVICE_TYPE_SMARTPHONE;
            } else {
                SC_Display_Ex::$device = DEVICE_TYPE_PC;
            }
        }

        return SC_Display_Ex::$device;
    }

    public function assign($val1, $val2)
    {
        $this->view->assign($val1, $val2);
    }

    /**
     * @param LC_Page $obj
     */
    public function assignobj($obj)
    {
        $this->view->assignobj($obj);
    }

    public function assignarray($array)
    {
        $this->view->assignarray($array);
    }
}
