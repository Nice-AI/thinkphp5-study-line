<?php
/**
 * Created by PhpStorm.
 * User: tinywan
 * Date: 2017/7/1
 * Time: 13:40
 */

namespace app\frontend\controller;

use app\common\model\Admin;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;

class Member extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function login()
    {
        return $this->fetch();
    }

    /**
     * 注册
     * @return mixed
     */
    public function signup(Request $request)
    {
        //1 验证数据
        if ($request->isPost()) {
            $res = (new Admin())->emailRegister(input("post."), "frontend");
            if ($res['valid']) {
                //success
                add_operation_log('登录成功');
                $this->success($res['msg'], "frontend/index/index");
                exit;
            } else {
                //fail
                add_operation_log('登录失败');
                $this->error($res['msg']);
                exit;
            }
        }
        return $this->fetch();
    }

    /**
     * 邮箱注册URL验证
     * @param Request $request
     */
    public function emailRegisterUrlValid(Request $request)
    {
        if ($request->isGet()) {
            $res = (new Admin())->emailRegisterUrlValid(input("get."), "frontend");
            if ($res["valid"]) {
                //success 把目前的邮箱地址保存在session中
                $this->success($res['msg'], "frontend/index/index");
            } else {
                $this->error($res['msg'], "frontend/index/login");
            }
        }
    }

    /**
     * 登录
     * @return mixed
     */
    public function signin()
    {
        return $this->fetch();
    }

    /**
     *  ---------------------------------------------------------------------------------手机注册
     *  http://www.tinywan_thinkphp5.com/frontend/member/mobileSignUp
     * @return mixed
     */
    public function mobileSignUp(Request $request)
    {
        //1 验证数据
        if ($request->isPost()) {
            $res = (new Admin())->mobileRegister(input("post."));
            if ($res['valid']) {
                //success
                add_operation_log('注册成功');
                $this->success($res['msg'], "frontend/index/index");
                exit;
            } else {
                //fail
                add_operation_log('登录失败');
                $this->error($res['msg']);
                exit;
            }
        }
        return $this->fetch();
    }

    /**
     * 手机验证码
     * @return mixed
     */
    public function mobileCodeCheck(Request $request)
    {
        $mobile = $request->get("mobile");
        //判断该手机是否被注册
        $userInfo = Db::table("resty_user")->where("mobile", $mobile)->find();
        if ($userInfo) {
            $this->error("该手机号已经存在");
            exit;
        }
        // 验证码
        $code = rand(100000, 999999);
        // 过期时间
        $expireTime = 60;
        //保存当前手机的验证码到Redis
        session("TINYWAN:" . $mobile, $code);
        //发送验证码操作
        $sendRes = send_dayu_sms($mobile, "register", ['code' => $code]);
        //发送成功
        if (isset($sendRes->result->success) && ($sendRes->result->success == true)) {
            $res = [
                "code" => 200,
                "msg" => "验证码发送成功"
            ];
            Log::info("--------------------验证码 : " . $mobile . " 发送成功");
        } else {
            $res = [
                "code" => 500,
                "msg" => "验证码发送失败"
            ];
            Log::error("-------------------验证码 : " . $mobile . " 发送失败 ，错误原因：" . $sendRes->sub_msg);
        }
        return json($res);
    }

    /**
     * -------------------------------------------------------------------------------个人中心
     */
    public function dd(){

    }

    public function getCode()
    {
        $data = [
            'username' => "11111111",
            'password' => md5("1111111111111111111"),
            'mobile' => 13669361192,
            'loginip' => "127.0.0.1",
        ];
        $res = Db::table("resty_user")->insertGetId($data);
        halt($res);
        //1 验证数据
        halt(session("TINYWAN:13669361192"));
    }

}