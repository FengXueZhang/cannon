<?php

namespace Org\PHPPowerPoint;


class PHPPowerPoint
{

    const SAVE_PATH            = '../powerpoint/';                          //生成的PowerPoint保存地址(根目录上级文件夹)
    const IMAGES_PATH          = '../powerpoint/images/';                   //图片文件夹
    const IMAGE_NAME_JPG       = 'realdolmen_bg.jpg';                       //图片一
    const IMAGE_NAME_GIF       = 'phppowerpoint_logo.gif';                  //图片二
    const FONTCOLOR            = 'FFFFFFFF';                                //字体颜色 16进制模式 前面2位为透明度 后面为RGB值

    const BACKGROUND_WIDTH     = 950;                                       //背景图片宽度 单位像素
    const BACKGROUND_HEIGHT    = 720;                                       //背景图片高度 单位像素
    const BACKGROUND_OFFSETX   = 0;                                         //背景图片相对于左上角X位置 单位像素
    CONST BACKGROUND_OFFSETY   = 0;                                         //背景图片相对于左上角Y位置 单位像素
    const BACKGROUND_NAME      = 'Background';                              //背景图片描述

    //const LOGO_WIDTH         = 400;                                       //logo图片宽度
    const LOGO_HEIGHT          = 40;                                        //logo图片高度
    const LOGO_OFFSETX         = 10;                                        //logo图片相对于左上角X位置 单位像素
    CONST LOGO_OFFSETY         = 720 - 10 - 40;                             //logo图片相对于左上角Y位置 单位像素
    const LOGO_NAME            = 'Logo';                                    //logo图片描述


    /**
     * PHP代码生成PHPPowerPoint
     *
     * @author WangXueChen
     *
     * @date   2016-05-19
     *
     * @param  name                      string    文件名称    
     *
     * @param  data                      array     文件详细内容    
     *
     * @return                           string    
     *
     */
    public static function createPowerPoint($name, $data)
    {
    
        vendor('PHPPowerPoint.Classes.PHPPowerpoint');
        vendor('PHPPowerPoint.Classes.PHPPowerPoint.IOFactory');

        set_time_limit(0);

        /** Error reporting */
        error_reporting(E_ALL);

        $objPHPPowerPoint = new \PHPPowerPoint();

        // Set properties
        $objPHPPowerPoint->getProperties()->setCreator("Maarten Balliauw");
        $objPHPPowerPoint->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPPowerPoint->getProperties()->setTitle("Office 2007 PPTX Test Document");
        $objPHPPowerPoint->getProperties()->setSubject("Office 2007 PPTX Test Document");
        $objPHPPowerPoint->getProperties()->setDescription("Test document for Office 2007 PPTX, generated using PHP classes.");
        $objPHPPowerPoint->getProperties()->setKeywords("office 2007 openxml php");
        $objPHPPowerPoint->getProperties()->setCategory("Test result file");

        // Remove first slide
        $objPHPPowerPoint->removeSlideByIndex(0);

        foreach ($data as $key => $value) {
            // Create templated slide
            $currentSlide = self::createTemplatedSlide($objPHPPowerPoint); // local function
            // Create a shape (text)
            $shape = $currentSlide->createRichTextShape();
            //设置文本框高度, 单位像素
            $shape->setHeight($value['height']);
            //设置文本框宽度, 单位像素
            $shape->setWidth($value['width']);
            //设置文本框相对于左上角X位置, 单位像素
            $shape->setOffsetX($value['offsetx']);
            //设置文本框相对于左上角Y位置, 单位像素
            $shape->setOffsetY($value['offsety']);
            //设置文本布局位置为水平居中.
            $shape->getAlignment()->setHorizontal(\PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT);
            //设置文本布局位置为垂直居中.
            //$shape->getAlignment()->setVertical( \PHPPowerPoint_Style_Alignment::VERTICAL_CENTER );
            foreach ($value['content'] as $k => $v) {
                $textRun = $shape->createTextRun($v['title']);
                $textRun->getFont()->setBold((string)$v['bold']);
                $textRun->getFont()->setSize($v['size']);
                $textRun->getFont()->setColor(new \PHPPowerPoint_Style_Color(self::FONTCOLOR));
                $shape->createBreak();
            }
        }
        $objWriter = \PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
        $objWriter->save(self::SAVE_PATH.'/'.$name.'.pptx');
        return " Done writing file.";
    }

    private function createTemplatedSlide(PHPPowerPoint $objPHPPowerPoint)
    {
            // Create slide
            $slide = $objPHPPowerPoint->createSlide();
            // Add background image
            $shape = $slide->createDrawingShape();
            //设置图片名称
            $shape->setName(self::BACKGROUND_NAME);
            //设置图片的描述信息
            $shape->setDescription(self::BACKGROUND_NAME);
            //图片实际路径
            $shape->setPath(   self::IMAGES_PATH.self::IMAGE_NAME_JPG);
            $shape->setWidth(  self::BACKGROUND_WIDTH);
            $shape->setHeight( self::BACKGROUND_HEIGHT);
            $shape->setOffsetX(self::BACKGROUND_OFFSETX);
            $shape->setOffsetY(self::BACKGROUND_OFFSETY);
            // Add logo
            $shape = $slide->createDrawingShape();
            //设置图片名称
            $shape->setName(self::LOGO_NAME);
            //设置图片的描述信息
            $shape->setDescription(self::LOGO_NAME);
            //图片实际路径
            $shape->setPath(   self::IMAGES_PATH.self::IMAGE_NAME_GIF);
            //$shape->setWidth(self::LOGO_WIDTH);
            $shape->setHeight( self::LOGO_HEIGHT);
            $shape->setOffsetX(self::LOGO_OFFSETX);
            $shape->setOffsetY(self::LOGO_OFFSETY);
            // Return slide
            return $slide;
    }
}
