<?php
/** .-------------------------------------------------------------------
 * |  Github: https://github.com/Tinywan
 * |  Blog: http://www.cnblogs.com/Tinywan
 * |-------------------------------------------------------------------
 * |  Author: Tinywan(SHaoBo Wan)
 * |  Date: 2017/1/20
 * |  Time: 16:25
 * |  Mail: Overcome.wan@Gmail.com
 * |  Created by PhpStorm.
 * '-------------------------------------------------------------------*/

namespace app\backend\controller;

use redis\BaseRedis;
use think\Controller;
use think\Session;
use think\session\driver\Redis;
use think\View;

class LinuxShell extends Controller
{
    /**
     * Linux 系统管理
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 内存管理
     */
    public function internalStorage()
    {
        return $this->fetch();
    }

}