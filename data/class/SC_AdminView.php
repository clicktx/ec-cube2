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

class SC_AdminView extends SC_View_Ex
{
    public function init()
    {
        parent::init();

        $this->_smarty->setTemplateDir(realpath(TEMPLATE_ADMIN_REALDIR));
        $this->_smarty->setCompileDir(realpath(COMPILE_ADMIN_REALDIR));
        $this->assign('TPL_URLPATH_PC', ROOT_URLPATH.USER_DIR.USER_PACKAGE_DIR.TEMPLATE_NAME.'/');
        $this->assign('TPL_URLPATH_DEFAULT', ROOT_URLPATH.USER_DIR.USER_PACKAGE_DIR.DEFAULT_TEMPLATE_NAME.'/');
        $this->assign('TPL_URLPATH', ROOT_URLPATH.USER_DIR.USER_PACKAGE_DIR.'admin/');
    }
}
