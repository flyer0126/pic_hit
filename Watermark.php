<?php
/**
 * Created by PhpStorm.
 * User: zhangh
 * Date: 16-3-22
 * Time: 下午5:29
 */
class Watermark{

    /**
     * 水印类型【1.图片；2.文字】
     * @var
     */
    protected $type;

    /**
     * 水印位置【1.左上角；2.居中；3.右下角】
     * @var
     */
    protected $pos;

    /**
     * 图片类型【1.大图；2.原图；3.小图】
     * @var
     */
    protected $size;

    /**
     * 尺寸数据
     * @var array
     */
    protected $sizeData = array(
        '1' => 5,
        '2' => 0.2
    );

    /**
     * 水印图片
     * @var string
     */
    protected $waterImg = './imgs/icon.png';

    /**
     * 水印文案
     * @var string
     */
    protected $waterText = '测试文字水印';

    /**
     * 水印字体
     * @var string
     */
    protected $font = './font/msyh.ttf';

    /**
     * 构造函数
     * @param $type
     * @param $pos
     */
    public function __construct($type, $pos, $size, $destination){
        $this->destination = $destination;
        $this->type = $type;
        $this->pos = $pos;
        $this->size = $size;
    }

    /**
     * 打水印
     */
    public function hit(){
        // 上传图片资源
        $sourceImg = imagecreatefromjpeg($this->destination);
        // 水印图片资源
        $waterImg = imagecreatefrompng($this->waterImg);

        // 打印水印图片位置
        list($source_w, $source_h) = $this->_getPos($sourceImg, $waterImg);
        switch($this->type){
            case 1: //图片
                imagecopy($sourceImg, $waterImg, $source_w, $source_h, 0, 0, imagesx($waterImg), imagesy($waterImg));
                break;
            case 2: //文字
                $im = imagecreatetruecolor(300, 100);
                $red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
                imagefttext($sourceImg, 16, 0, $source_w, $source_h, $red, $this->font, $this->waterText);
                break;
            default:
                imagecopy($sourceImg, $waterImg, $source_w, $source_h, 0, 0, imagesx($waterImg), imagesy($waterImg));
                break;
        }

        // 输出图像到文件
        imagejpeg($sourceImg, $this->destination);
        // 生成不同图片尺寸
        $this->_generateSize($sourceImg);
        imagedestroy($sourceImg);
        imagedestroy($waterImg);
    }

    /**
     * 获取图片原图打水印起始位置
     * @param $sourceImg // 原图资源
     * @param $waterImg // 水印资源
     * @return array
     */
    private function _getPos($sourceImg, $waterImg){
        switch($this->pos){
            case 1: //左上角
                $source_w = $source_h = 20;
                break;
            case 2: //居中
                $source_w = floor((imagesx($sourceImg) - imagesx($waterImg))/2);
                $source_h = floor((imagesy($sourceImg) - imagesy($waterImg))/2);
                break;
            case 3: //右下角
                $source_w = imagesx($sourceImg) - imagesx($waterImg) - 20;
                $source_h = imagesy($sourceImg) - imagesy($waterImg) - 20;
                break;
            default:
                $source_w = $source_h = 20;
                break;
        }

        return [$source_w, $source_h];
    }

    /**
     * 生成不同尺寸图片
     * @param $sourceImg
     */
    private function _generateSize($sourceImg){
        foreach($this->size as $sizeVal){
            $this->_createPic($sizeVal, $sourceImg);
        }
    }

    /**
     * 具体生成
     * @param $size
     * @param $sourceImg
     */
    private function _createPic($size, $sourceImg){
        // 创建一个图片
        $newImg = imagecreatetruecolor(imagesx($sourceImg) * $this->sizeData[$size], imagesy($sourceImg) * $this->sizeData[$size]);
        // 将源文件剪切全部域并缩小放到目标图片上
        imagecopyresampled($newImg, $sourceImg, 0, 0, 0, 0, imagesx($sourceImg) * $this->sizeData[$size], imagesy($sourceImg) * $this->sizeData[$size], imagesx($sourceImg), imagesy($sourceImg));
        $tmp = explode('.', $this->destination);

        $newDestination = $tmp[0].'_'.$size.$tmp[1];
        imagejpeg($newImg, $newDestination);
        imagedestroy($newImg);
    }


}