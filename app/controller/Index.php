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

use Error;
use Exception;
use app\model\Counters;
use app\model\AccessToken;
use think\response\Html;
use think\response\Json;
use think\facade\Log;
//use CommonData;

class Index
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
        return response(file_get_contents(dirname(dirname(__FILE__)).'/view/index.html'));
    }


   


    /**
     * 获取todo list
     * @return Json
     */
    public function getCount(): Json
    {
        try {
            $data = (new Counters)->find(1);
            if ($data == null) {
                $count = 0;
            }else {
                $count = $data["count"];
            }
            $res = [
                "code" => 0,
                "data" =>  $count
            ];
            Log::write('getCount rsp: '.json_encode($res));
            return json($res);
        } catch (Error $e) {
            $res = [
                "code" => -1,
                "data" => [],
                "errorMsg" => ("查询计数异常" . $e->getMessage())
            ];
            Log::write('getCount rsp: '.json_encode($res));
            return json($res);
        }
    }


    /**
     * 根据id查询todo数据
     * @param $action `string` 类型，枚举值，等于 `"inc"` 时，表示计数加一；等于 `"reset"` 时，表示计数重置（清零）
     * @return Json
     */
    public function updateCount($action): Json
    {
        try {
            if ($action == "inc") {
                $data = (new Counters)->find(1);
                if ($data == null) {
                    $count = 1;
                }else {
                    $count = $data["count"] + 1;
                }
    
                $counters = new Counters;
                $counters->create(
                    ["count" => $count, 'id' => 1],
                    ["count", 'id'],
                    true
                );
            }else if ($action == "clear") {
                Counters::destroy(1);
                $count = 0;
            }

            $res = [
                "code" => 0,
                "data" =>  $count
            ];
            Log::write('updateCount rsp: '.json_encode($res));
            
            return json($res);
        } catch (Exception $e) {
            $res = [
                "code" => -1,
                "data" => [],
                "errorMsg" => ("更新计数异常" . $e->getMessage())
            ];
            Log::write('updateCount rsp: '.json_encode($res));
            return json($res);
        }
    }

    public function getAccessToken(){
        //$GLOBALS['accessToken'] = "success";
        //return $GLOBALS['accessToken'];
        try{
            $post_data = array(
                'scope' => 'fbox',
                'client_id' => '06bd78ba4983401da95c76656b101458',
                'client_secret' => '57dd6b9f95614a118bad9594b24a9ddb',
                'grant_type' => 'client_credentials'
            );
            $tokenData = send_postX('https://account.flexem.com/core/connect/token', $post_data);
            //$tokenData = '{"access_token":"eyJhbGciOiJSUzI1NiIsImtpZCI6Ijg2QzQ2RTIxQTc0MTUxNTFCOTQ0MTY4MzhEMERGODU1OTZENkM2RTgiLCJ0eXAiOiJhdCtqd3QiLCJ4NXQiOiJoc1J1SWFkQlVWRzVSQmFEalEzNFZaYld4dWcifQ.eyJuYmYiOjE2NTMzODI5NzAsImV4cCI6MTY1MzM5MDE3MCwiaXNzIjoiaHR0cHM6Ly9hY2NvdW50LmZsZXhlbS5jb20vY29yZSIsImF1ZCI6Imlkc3ZyMyIsImNsaWVudF9pZCI6IjA2YmQ3OGJhNDk4MzQwMWRhOTVjNzY2NTZiMTAxNDU4Iiwic3ViIjoiYWJjMjZhOTMtNGEzNi00MjNhLWE5NmQtNjM5MGEyYzNiMzVlIiwic2NvcGUiOlsiZmJveCJdfQ.SdG83h5hQ9MlfKTq-yGoKpdrv23IQOYTAebaFcsVo_kk7V7gLHELPpxXF0EKv5e6c23Df-Vdj7waczhFOojVMkufIuc5jgVBc832-7KUe-Cq8F0P8YPsTybLlTkzzjzvqXlA_ElffhWLexIVUkmCcIBdnN9jDwLGL7pmLXjv4z9AWNUhTVxoA93cBQAiU54CEXlOx_-3eXXR8coyAtJO46hnEtF83mmAw31aaoG23kumYmYbXs1wjz9lCZimdM5x67M8SMKna90MxoKxnsTmHhRGjISTrJyxQ_guQfXC7AxY-jDkd7sI0N7sqvLpRm0CHDUXD1CgGHCLA3DarSYXZg","expires_in":7200,"token_type":"Bearer","scope":"fbox"}';
            $realAccessToken = json_decode($tokenData,true)['access_token'];
            Log::write('accessToken rsp: '.$realAccessToken);
            $this->globalToken = $realAccessToken;
            $accessToken = new AccessToken;
            AccessToken::destroy(1);
            $accessToken->create(
                ["main" => $realAccessToken, 'id' => 1],
            );
            Log::write('accessToken rsp: '.$tokenData);
            $res = [
                "code" => 0,
                "data" => [],
                "errorMsg" => ("获取accessToken成功")
            ];
            return json($res);
        } catch (Exception $e) {
            $res = [
                "code" => -1,
                "data" => [],
                "errorMsg" => ("查询accessToken失败" . $e->getMessage())
            ];
            Log::write('accessToken rsp: '.json_encode($res));
            return json($res);
        }
    }

   
    public function getCurrentFloor(){
        if($this->globalToken == ''){
            $this->globalToken = (new AccessToken)->find(1)["main"];
        }
        
        //dump($this->globalToken);
        $post_data = json_encode(array(
            "names" => ["左笼当前楼层"],
            "groupnames" => ["左笼"],
            "timeOut" => null
        ));
        //$res = send_post_jsonX2('http://fbcs101.fbox360.com/api/v2/dmon/value/get?boxNo=338221114635', $post_data, (new AccessToken)->find(1));
        $res = send_post_jsonX2('http://fbcs101.fbox360.com/api/v2/dmon/value/get?boxNo=338221114635', $post_data, $this->globalToken);
        //$res = send_post_jsonX2('http://fbcs101.fbox360.com/api/v2/dmon/value/get?boxNo=338221114635', $post_data, "eyJhbGciOiJSUzI1NiIsImtpZCI6Ijg2QzQ2RTIxQTc0MTUxNTFCOTQ0MTY4MzhEMERGODU1OTZENkM2RTgiLCJ0eXAiOiJhdCtqd3QiLCJ4NXQiOiJoc1J1SWFkQlVWRzVSQmFEalEzNFZaYld4dWcifQ.eyJuYmYiOjE2NTM0NDYzMDcsImV4cCI6MTY1MzQ1MzUwNywiaXNzIjoiaHR0cHM6Ly9hY2NvdW50LmZsZXhlbS5jb20vY29yZSIsImF1ZCI6Imlkc3ZyMyIsImNsaWVudF9pZCI6IjA2YmQ3OGJhNDk4MzQwMWRhOTVjNzY2NTZiMTAxNDU4Iiwic3ViIjoiYWJjMjZhOTMtNGEzNi00MjNhLWE5NmQtNjM5MGEyYzNiMzVlIiwic2NvcGUiOlsiZmJveCJdfQ.rIicgFEcEOib6M7NKN99KowBcNikD0pFWrSRE3Qo47Vx2bFGn5mBgWNdg-D0PY4cQAmfiZkZVbrtBYa2jTCt_OQn9ZY4SW4qP5BzS6ODHxrkRAD2hTjy4coKCn-jjGXoj6aRS61h5VMjPTCgbUiHBu92TUm-4eNNas6XLYZ_wAq3r6aoab2HH3q9o6v9tT4aDbyYBiQEeYj0UbldzfHRm3kr9euLNal7rEPZgpjdLmF0Dlfz0mMcvVLc2XSziGOiLdbxq2VdrLIZiBOxB-apMBP4iEILizcA_MsWvT39cmnk0aaMONC8oIA-qipXrfqJwU6mR1c6_Q_blOyHnMenYg");
        list($httpCode , $resContent) = $res;
        if($httpCode == 401){
            if($this->globalToken != ''&&$this->globalToken != null){
                $this->getAccessToken();
            }
            Log::write('getCurrentFloor rsp: '.$httpCode.' '.$resContent.'Token='.$this->globalToken);
            $restojs = [
                "code" => -1,
                "data" =>  'ErrCode:'.$httpCode
            ];
            return json($restojs);
        }
        $num_floor = json_decode(substr($resContent, 1, -1),true)['value'];
        //dump($res);
        if($num_floor == null){
            Log::write('getCurrentFloor rsp: '.substr($resContent, 1, -1));
            $restojs = [
                "code" => -2,
                "data" =>  '电梯离线'
            ];
        }else{
            Log::write('getCurrentFloor rsp: '.$httpCode.' '.$num_floor);
            $restojs = [
                "code" => 0,
                "data" =>  $num_floor
            ];
        }
        return json($restojs);
    }

    public function elevatorCall($callFloor){
        if($this->globalToken == ''){
            $this->globalToken = (new AccessToken)->find(1)["main"];
        }
        $post_data_start = json_encode(array(
            "name" => '左呼叫层'.$callFloor,
            "groupname" => "左笼",
            "value" => 1,
            "type" => 1
            
        ));

        $post_data_finsh = json_encode(array(
            "name" => '左呼叫层'.$callFloor,
            "groupname" => "左笼",
            "value" => 1,
            "type" => 1,
        ));


        list($httpCode_start , $resContent_start) = send_post_jsonX2('http://fbcs101.fbox360.com/api/v2/dmon/value?boxNo=338221114635', $post_data_start, $this->globalToken);
        list($httpCode_finsh , $resContent_finsh) = send_post_jsonX2('http://fbcs101.fbox360.com/api/v2/dmon/value?boxNo=338221114635', $post_data_finsh, $this->globalToken);
        
        Log::write('elevatorCall Start->Finsh rsp: '.$httpCode_start.'->'.$httpCode_finsh.' '.$callFloor.$post_data_start);
        if($httpCode_start == 200 && $httpCode_finsh ==200){
            $restojs = [
                "code" => 0,
                "data" =>  'success'
            ];
            return json($restojs);
        }else{
            $restojs = [
                "code" => -1,
                "data" =>  $resContent_start.'\n'.$resContent_finsh
            ];
            Log::write('[failed] elevatorCall Start->Finsh rsp: '.$httpCode_start.'->'.$httpCode_finsh.' '.$callFloor.'\n'.$resContent_start.'\n->\n'.$resContent_finsh);

            return json($restojs);
        }
    }
      

}



