<?php
// +----------------------------------------------------------------------
// | 文件: index.php
// +----------------------------------------------------------------------
// | 功能: 提供todo api接口
// +----------------------------------------------------------------------
// | 时间: 2021-11-15 16:20
// +----------------------------------------------------------------------
// | 作者: rangangwei<gangweiran@tencent.com>
// +----------------------------------------------------------------------

namespace app\controller;

use app\model\Elevator;
use Error;
use Exception;
use app\model\Counters;
use app\model\AccessToken;
use think\App;
use think\response\Html;
use think\response\Json;
use think\facade\Log;
//use CommonData;

class admin
{
   
    public $globalToken = '';
    /**
     * 主页静态页面
     * @return Html
     */
    public function index(): Html
    {
        # html路径: ../view/index.html
        //$this->globalToken = (new AccessToken)->find(1)["main"];
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/admin.html'));
    }


   public function getWXAccessToken(){
       $Appid   = "wxc71adb8b5e6cdf60";//小程序appid
       $Secret  = "bc90b72d42dbe360158834800852865a";//小程序Secret/小程序Secret
       $url = "https://api.weixin.qq.com/cgi-bin/token?appid=".$Appid."&secret=".$Secret."&grant_type=client_credential";
       $res = get_wx($url);
       if(isset($res['errcode'])){
           return false;
       }

       $token=$res['access_token'];
       Log::write('getWXAccessToken'.$token);
       return $token;
   }

   public function getQRcode($data_boxNo,$data_displayName,$data_validDate,$data_direction,$data_defaultFloor,$passwd){
       //$boxNo,$carDirection,$currentFloor
       if($passwd!='jadge22'){
           return '密码错误';
       }
       Log::write('getQRcode'.$data_boxNo.$data_displayName.$data_validDate.$data_direction.$data_defaultFloor);
       $elevatorQuery= Elevator::where('boxNo', $data_boxNo)->find();
       if(!empty($elevatorQuery)){
           return json($elevatorQuery)->contentType('application/json');
       }
       $elevator = new Elevator();
       //$time=date("Y-m-d H:i:s",strtotime('+1year'));
       switch ($data_validDate){
           case 0:
               $time = date("Y-m-d H:i:s",strtotime('+1year'));
               break;
           case 1:
               $time = date("Y-m-d H:i:s",strtotime('+2year'));
               break;
           case 2:
               $time = date("Y-m-d H:i:s",strtotime('+3year'));
               break;
           case 3:
               $time = date("Y-m-d H:i:s",strtotime('+6month'));
               break;
           case 4:
               $time = date("Y-m-d H:i:s",strtotime('+3month'));
               break;
           case 5:
               $time = date("Y-m-d H:i:s",strtotime('+1month'));
               break;
       }

       $elevator->save([
           'boxNo'  =>  $data_boxNo,
           'displayName' =>  $data_displayName,
           'validTime' =>  $time,
       ]);

       $data = array(
           //'access_token' => $this->getWXAccessToken(),
           'scene' => $data_boxNo.'#'.$data_direction.(empty($data_defaultFloor)?"":'#'.$data_defaultFloor),
           //'scene' => '###AsudoAA',
           'width'=> 800
       );
       $QRcodeData = send_post_jsonX('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$this->getWXAccessToken(), $data);
       //return response(file_get_contents(dirname(dirname(__FILE__)).'/view/admin.html'));
       $im = ImageCreate(800,800);
       $im = imagecreatefromstring($QRcodeData);

       $black = ImageColorAllocate($im, 0, 0, 0);
       

       //dump(dirname(__FILE__));
       //发现是GD库加载字体文件时，需求提供绝对路径，给font路径用realpath()将相对路径转成绝对路径即可
       imagettftext($im,10,0,15,15,$black,"/app/app/Alibaba-PuHuiTi-Medium.ttf",'No:'.$data_boxNo.' '.$data_displayName.' VT:'.date('W',strtotime($time)).substr($time,3,1).' '.$data_direction.$data_defaultFloor);
       $processedImg = imagejpeg($im);
       imagedestroy($im);
       try {
           $resX = response($processedImg, 200)->contentType('image/jpeg');

       }catch (Error $e){

       }
//['Content-Length' => strlen($QRcodeData)]
       return $resX;
   }


//https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=ACCESS_TOKEN




}



