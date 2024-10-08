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

<script>
    function ajaxLogin() {
        var postData = new Object;
        postData['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = "<!--{$transactionid}-->";
        postData['mode'] = 'login';
        postData['login_email'] = $('input[type=email]').val();
        postData['login_pass'] = $('input[type=password]').val();
        postData['url'] = $('input[name=url]').val();

        $.ajax({
            type: "POST",
            url: "<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php",
            data: postData,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
            },
            success: function(result){
                if (result.success) {
                    location.href = result.success;
                } else {
                    alert(result.login_error);
                }
            }
        });
    }
</script>

<section id="slidewindow">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <form name="login_mypage" id="login_mypage" method="post" action="javascript:;" onsubmit="return ajaxLogin();">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />

        <div class="login_area">

            <div class="loginareaBox">
                <!--{assign var=key value="login_email"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="email" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="mailtextBox data-role-none" placeholder="メールアドレス" />

                <!--{assign var=key value="login_pass"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="password" name="<!--{$key|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="passtextBox data-role-none" placeholder="パスワード" />
            </div><!-- /.loginareaBox -->

            <p class="arrowRtxt"><a href="<!--{$smarty.const.HTTPS_URL}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->">パスワードを忘れた方</a></p>

            <div class="btn_area">
                <input type="submit" value="ログイン" class="btn data-role-none" name="log" id="log" />
            </div>
        </div><!-- /.login_area -->
    </form>

    <form name="member_form2" id="member_form2" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="nonmember" />
        <div class="login_area_btm">
            <nav>
                <ul class="navBox">
                    <li><a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php">新規会員登録</a></li>
                </ul>
            </nav>
            <p>会員登録をすると便利なMyページをご利用いただけます。</p>
        </div>
    </form>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

<!--▲コンテンツここまで -->
