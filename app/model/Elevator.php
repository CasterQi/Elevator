<?php
// +----------------------------------------------------------------------
// | 文件: index.php
// +----------------------------------------------------------------------
// | 功能: mysql数据库表model
// +----------------------------------------------------------------------
// | 时间: 2021-11-15 16:20
// +----------------------------------------------------------------------
// | 作者: rangangwei<gangweiran@tencent.com>
// +----------------------------------------------------------------------

namespace app\model;

use think\Model;

// Counters 定义数据库model
class Elevator extends Model
{
    protected $table = 'Elevator';
    public $id;
    public $boxNo;
    public $displayName;
    public $validTime;
    public $createdAt;
    public $updateAt;
}