<?php

namespace Org\Code;

import('ORG.Code.phpqrcode');

/**
 * 微信-通讯录 用户相关接口.
 *
 * @author Cui.
 */
class CodeGeneration
{

    const CODE_GENERATION_PATH = '../images/';                //二维码图片生成地址

    /**
     * 二维码生成
     *
     * @author WangXueChen
     *
     * @date   2016-05-18
     *
     * @param  value                     string    二维码内容    
     *
     * @param  errorCorrectionLevel      string    容错等级    
     *
     * @param  matrixPointSize           string    大小 
     *
     * @return string                              图片地址
     *
     */
    public static function GenerationCode($value, $errorCorrectionLevel = 'L', $matrixPointSize = 10)
    {
        if(isset($value) && !empty($value)){

            $filename = md5($value.$errorCorrectionLevel.$matrixPointSize).'.png';

            \QRcode::png($value, self::CODE_GENERATION_PATH.$filename, $errorCorrectionLevel, $matrixPointSize, 2);

            if(file_exists(self::CODE_GENERATION_PATH.$filename)) {

                return self::CODE_GENERATION_PATH.$filename;

            } else {

                return "二维码生成失败";

            }  

        }else{

            return "二维码内容不能为空";

        }  

    }


     

}
