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

class Index extends Controller
{
    public function index()
    {
        return $this->fetch("index",[
            "title"=>"智能后台数据管理平台"
        ]);
    }

    /**
     * view() 不需要继承 think\Controller
     * @return \think\response\View
     */
    public function indexView()
    {
        return $this->fetch();
    }

    /**
     * fetch() 必须要继承 think\Controller
     * 以下列出了4中不同的赋值方式
     * @return mixed
     */
    public function indexFetch()
    {
        #【1】
        $this->assign('keys', "values");
        #【2】
        $this->view->version = "V0.01";
        #【3】
        View::share("viewName", "use think\\view");
        #【4】
        return $this->fetch("indexdemo",
            [
                'username' => "tinywan",
                'age' => 24
            ], ["STATIC" => "这是是static 替换掉后的内容"]);
    }

    /*
     *  系统变量原生标签
     */
    public function systemTag()
    {
        //var_dump($_SERVER);
        cookie('username', "Tinywan");
        return $this->fetch();
    }

    /**
     * 变量输出调节器、模板循环标签
     */
    public function varOutput()
    {
        $this->assign('username', 'Tinywan');
        $this->assign('age', '24');
        $this->assign('email', '756684177@qq.com');
        $this->assign('time', time());
        $this->assign('a', 100);
        #   模板循环标签
        $age = ["Peter" => "35", "Ben" => "37", "Joe" => "43"];
        $lists = [
            'user1' => [
                'username' => "C语言",
                'age' => 98
            ],
            'user2' => [
                'username' => "PHP语言",
                'age' => 89
            ],
            'user3' => [
                'username' => "Lua语言",
                'age' => 100
            ]
        ];
        $this->assign('empty', "<h1>这里没有数据的</h1>");
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    /**
     *  模板的布局、包含、继承
     */
    public function tplTest()
    {
        $lists = [
            'user1' => [
                'username' => "C语言",
                'age' => 98
            ],
            'user2' => [
                'username' => "PHP语言",
                'age' => 89
            ],
            'user3' => [
                'username' => "Lua语言",
                'age' => 100
            ]
        ];
        $this->assign('lists', $lists);
        $this->assign('title',"Composer安装");
        return $this->fetch('index');
    }

    public function redisTest(){
//        ini_set("session.save_handler","redis");
//        ini_set("session.save_path","tcp://127.0.0.1:6379");
//        session_start();
//        $_SESSION["user_id"] = ['name' => 'Tinywan', 'num' =>123213];
//        $_SESSION["class2"] = ['name' => 'Tinywan22222', 'num' =>12321322222222222];
        session("Lua",['123','Openresty']);

        //检查session_id
//        echo 'session_id:' . session_id() . '<br/>';
        //redis存入的session（redis用session_id作为key,以string的形式存储）
//        echo 'redis_session:' . $redis->get('PHPREDIS_SESSION:' . session_id()) . '<br/>';

        //redis存入的session（redis用session_id作为key,以string的形式存储）
        //php获取session值
//        echo 'php_session:' . json_encode($_SESSION['class']);
    }

}