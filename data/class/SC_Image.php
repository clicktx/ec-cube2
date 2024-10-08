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

// ---- アップロードファイル加工クラス(thumb.phpとセットで使用する)
class SC_Image
{
    public $tmp_dir;

    public function __construct($tmp_dir)
    {
        // ヘッダファイル読込
        $this->tmp_dir = rtrim($tmp_dir, '/').'/';
    }

    // --- 一時ファイル生成(サムネイル画像生成用)
    public function makeTempImage($keyname, $max_width, $max_height)
    {
        // 一意なIDを取得する。
        $mainname = uniqid('').'.';
        // 拡張子以外を置き換える。
        $newFileName = preg_replace("/^.*\./", $mainname, $_FILES[$keyname]['name']);
        $result = $this->MakeThumb($_FILES[$keyname]['tmp_name'], $this->tmp_dir, $max_width, $max_height, $newFileName);
        GC_Utils_Ex::gfDebugLog($result);

        return $newFileName;
    }

    // --- ファイルを指定保存DIRへ移動
    public function moveTempImage($filename, $save_dir)
    {
        // コピー元ファイル、コピー先ディレクトリが存在する場合にのみ実行する
        $from_path = $this->tmp_dir.$filename;
        $to_path = $save_dir.'/'.$filename;
        if (file_exists($from_path) && file_exists($save_dir)) {
            if (copy($from_path, $to_path)) {
                unlink($from_path);
            }
        } else {
            GC_Utils_Ex::gfDebugLog($from_path.'->'.$to_path.'のcopyに失敗しました。');
        }
    }

    // ---- 指定ファイルを削除
    public function deleteImage($filename, $dir)
    {
        if (file_exists($dir.'/'.$filename)) {
            unlink($dir.'/'.$filename);
        }
    }

    /**
     * 指定サイズで画像を出力する.
     *
     * @param string  $FromImgPath ファイル名までのパス
     * @param string  $ToImgPath   出力先パス
     * @param int $tmpMW       最大横幅
     * @param int $tmpMH       最大縦幅
     * @param int $newFileName 新ファイル名
     * @param array 新ファイル名を格納した配列
     */
    public function MakeThumb($FromImgPath, $ToImgPath, $tmpMW, $tmpMH, $newFileName = '')
    {
        // 画像の最大横幅（単位：ピクセル）
        $ThmMaxWidth = LARGE_IMAGE_WIDTH;

        // 画像の最大縦幅（単位：ピクセル）
        $ThmMaxHeight = LARGE_IMAGE_HEIGHT;

        // 拡張子取得
        $array_ext = explode('.', $FromImgPath);
        $ext = $array_ext[count($array_ext) - 1];

        $MW = $ThmMaxWidth;
        if ($tmpMW) {
            $MW = $tmpMW;
        } // $MWに最大横幅セット

        $MH = $ThmMaxHeight;
        if ($tmpMH) {
            $MH = $tmpMH;
        } // $MHに最大縦幅セット

        if (empty($FromImgPath) || empty($ToImgPath)) {
            return [0, '出力元画像パス、または出力先フォルダが指定されていません。'];
        }

        if (!file_exists($FromImgPath)) {
            return [0, '出力元画像が見つかりません。'];
        }

        $size = @getimagesize($FromImgPath);
        $re_size = $size;

        // 画像の種類が不明 or swf
        if (!$size[2] || $size[2] > 3) {
            return [0, '画像形式がサポートされていません。'];
        }

        // アスペクト比固定処理
        $tmp_w = $size[0] / $MW;
        $tmp_h = 0;
        if ($MH != 0) {
            $tmp_h = $size[1] / $MH;
        }

        if ($tmp_w > 1 || $tmp_h > 1) {
            if ($MH == 0) {
                if ($tmp_w > 1) {
                    $re_size[0] = $MW;
                    $re_size[1] = $size[1] * $MW / $size[0];
                }
            } else {
                if ($tmp_w > $tmp_h) {
                    $re_size[0] = $MW;
                    $re_size[1] = $size[1] * $MW / $size[0];
                } else {
                    $re_size[1] = $MH;
                    $re_size[0] = $size[0] * $MH / $size[1];
                }
            }
        }

        // サムネイル画像ファイル名作成処理
        $tmp = array_pop(explode('/', $FromImgPath)); // /の一番最後を切り出し
        $FromFileName = array_shift(explode('.', $tmp)); // .で区切られた部分を切り出し
        $ToFile = $FromFileName; // 拡張子以外の部分までを作成

        $ImgNew = imagecreatetruecolor($re_size[0], $re_size[1]);
        $ImgDefault = null;
        $RetVal = '';
        switch ($size[2]) {
            case '1': // gif形式
                if ($tmp_w <= 1 && $tmp_h <= 1) {
                    if ($newFileName) {
                        $ToFile = $newFileName;
                    } elseif ($ext) {
                        $ToFile .= '.'.$ext;
                    } else {
                        $ToFile .= '.gif';
                    }
                    if (!@copy($FromImgPath, $ToImgPath.$ToFile)) { // エラー処理
                        return [0, 'ファイルのコピーに失敗しました。'];
                    }
                    imagedestroy($ImgNew);

                    return [1, $ToFile];
                }

                imagecolorallocate($ImgNew, 255, 235, 214); // 背景色
                $black = imagecolorallocate($ImgNew, 0, 0, 0);
                $red = imagecolorallocate($ImgNew, 255, 0, 0);
                imagestring($ImgNew, 4, 5, 5, "GIF $size[0]x$size[1]", $red);
                imagerectangle($ImgNew, 0, 0, $re_size[0] - 1, $re_size[1] - 1, $black);

                if ($newFileName) {
                    $ToFile = $newFileName;
                } elseif ($ext) {
                    $ToFile .= '.'.$ext;
                } else {
                    $ToFile .= '.png';
                }
                $TmpPath = $ToImgPath.$ToFile;
                @imagepng($ImgNew, $TmpPath);
                // 画像が作成されていない場合
                if (!@file_exists($TmpPath)) {
                    return [0, '画像の出力に失敗しました。'];
                }
                imagedestroy($ImgNew);

                return [1, $ToFile];

            case '2': // jpg形式
                $ImgDefault = imagecreatefromjpeg($FromImgPath);
                // ImageCopyResized($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

                if ($re_size[0] != $size[0] || $re_size[0] != $size[0]) {
                    imagecopyresampled($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);
                }

                GC_Utils_Ex::gfDebugLog($size);
                GC_Utils_Ex::gfDebugLog($re_size);

                if ($newFileName) {
                    $ToFile = $newFileName;
                } elseif ($ext) {
                    $ToFile .= '.'.$ext;
                } else {
                    $ToFile .= '.jpg';
                }
                $TmpPath = $ToImgPath.$ToFile;
                @imagejpeg($ImgNew, $TmpPath);
                // 画像が作成されていない場合
                if (!@file_exists($TmpPath)) {
                    return [0, "画像の出力に失敗しました。<br>{$ImgNew}<br>{$TmpPath}"];
                }
                $RetVal = $ToFile;
                break;

            case '3': // png形式
                $ImgDefault = imagecreatefrompng($FromImgPath);
                // ImageCopyResized($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);
                imagecopyresampled($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

                if ($newFileName) {
                    $ToFile = $newFileName;
                } elseif ($ext) {
                    $ToFile .= '.'.$ext;
                } else {
                    $ToFile .= '.png';
                }
                $TmpPath = $ToImgPath.$ToFile;
                @imagepng($ImgNew, $TmpPath);
                // 画像が作成されていない場合
                if (!@file_exists($TmpPath)) {
                    return [0, '画像の出力に失敗しました。'];
                }
                $RetVal = $ToFile;
                break;
        }

        if (is_resource($ImgDefault)) {
            imagedestroy($ImgDefault);
        }
        imagedestroy($ImgNew);

        return [1, $RetVal];
    }
}
