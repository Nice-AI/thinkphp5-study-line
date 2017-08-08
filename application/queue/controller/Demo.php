<?php

/**
 * Created for thinkphp5-study-line.
 * File: Demo.php
 * User: cools
 * Date: 2017/8/6 0006
 * Time: 14:02
 * Description :
 */

namespace app\queue\controller;

use think\Console;
use think\Log;
use think\Queue;
use app\queue\consumer\HelloJob;

class Demo
{
    /**
     * push a new job into queue
     */
    public function test()
    {
        return "test";
    }

    public function index()
    {
        $consumer = HelloJob::class;    // app\queue\consumer\HelloJob
        $link = "http://www.baidu.com/backend/login/emailRegisterUrlValid?checkstr=11111111111&auth_key=909090909090";
        $payLoad = [
            'time' => time(),
            'mail' => "756684177@qq.com",
            'url' => $link,
        ];
        $queue = 'mail';

        $pushed = Queue::push($consumer, $payLoad, $queue);
        if ($pushed) {
            Log::error("[2000000000000000000001]邮件队列发布结果：" . $pushed);
            //发布成功后立即执行 php think queue:work --queue mail
            $command = 'queue:work';
            $params = ['queue' => $queue, 'sleep' => 3];
            $result = Console::call($command, $params);


            var_dump($result);
            Log::error("[2--------------1]发布成功后立即执行：");
            echo 'job Pushed.' . '<br/>' . 'job payload :' . '<br/>' . var_export($payLoad, TRUE);
        } else {
            echo 'Oops, something wrong with your queue';
        }
    }

    /**
     * 命令行执行
     */
    public function runQueue()
    {
        Queue::push();
        $queue = "mail";
        $command = 'queue:work' ;
        $params = ['queue' => $queue , 'sleep' => 3];
        $result = Console::call($command ,$params);
    }
}