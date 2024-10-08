<!--{*
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
*}-->

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.HTTPS_URL}-->mypage/refusal.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />

        <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

        <!--★インフォメーション★-->
        <div class="refusetxt">
            <p>退会手続きを実行してもよろしいでしょうか？</p>
            <ul class="btn_refuse">
                <li><a class="btn" href="./refusal.php">いいえ、退会しません</a></li>
                <li><input class="btn data-role-none" type="submit" value="はい、退会します" name="refuse_do" id="refuse_do" /></li>
            </ul>
        </div>
    </form>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

