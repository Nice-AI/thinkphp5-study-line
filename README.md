# thinkphp5-study-line
+ thinkphp5-study-line
+ ffmpeg
+ MissYang Linux 1.1
+ MissYang Linux 2.0
+ MissYang Linux 3.4
+ MissYang Linux 4.4
+ MissYang Linux 5.8

##  定义独立的配置目录 .
```bash
├─application         应用目录
├─config              配置目录
│  ├─config.php       应用配置文件
│  ├─database.php     数据库配置文件
│  ├─route.php        路由配置文件
│  ├─index            index模块配置文件目录
│  │  ├─config.php    index模块配置文件
│  │  └─database.php  index模块数据库配置文件
```
+   自己封装的函数写在helper.php 文件中
##   PHPer这样写代码也许更优雅
+   master分支应该是非常稳定的，也就是仅用来发布新版本，平时不能在上面干活
##  微信
+   网页授权
+   MisssYang

##   PHPer这样写代码也许更优雅
+   master分支应该是非常稳定的，也就是仅用来发布新版本，平时不能在上面干活

## 命名约定
+   表和字段采用小写字母和下划线命名，如`user_name`

##  模块
+   common为公共模块，不允许在URL上直接访问   
+   扩展配置会覆盖应用配置，应用覆盖默认配置   
+   隐藏index.php Apache 开启`LoadModule rewrite_module modules/mod_rewrite.so`
+   入口文件绑定
##  API 模块的单独使用
+   在public文件夹下新建api.php文件
    ```php
    define('APP_PATH', __DIR__ . '/../application/');
    define('CONF_PATH', __DIR__ . '/../config/');
    require __DIR__ . '/../thinkphp/start.php';
    ```
+   入口自动绑定模块`'auto_bind_module' => true` 默认是false,修改为true既可以
+   访问路径：`http://127.0.0.1:8080/thinkphp5-study-line/public/api.php/index/test`
##  路由
+   dfsd
    ```php
    return [
        "news/:id"=>"admin/index/info", #   http://127.0.0.1/news/234
    ];
    ```
##  请求和相应
+   $request 将会打印所有请求信息
+   命令行创建一个控制器（backend模块的common控制器）：php think make:controller backend/Common --plain
##  功能添加
+   支付宝即时到账：`http://test.thinkphp5-ffmpeg.com/frontend/AliPay/show`

##  命令行
+ 命令行新建一个控制器：php think make:controller backend/Category --plain

## URL路由
+   a 连接：`<a href="{:url('backend/entry/modpass')}">`
+   from 表单提交：`<form action="/backend/login/forgotPassword" method="post">`
##  控制器
+   命令行新建控制器：``

## 功能添加
+   邮箱注册、登录、找回密码、忘记密码功能
+   分类管理
+   支付宝即时到账
##  Auth 权限系统
+   分支：auth
##  Excel 表格数据导出功能
+   MySQL数据的数据导出
+   Excel数据的导入
+   微信邀请码的发送
##  Frontend & Backend User Register or Login 
+  携带参数
+  回调地址
+  封装成一个Model
##  GoEath007 README
+  第一次提交代码注释
+  163邮箱注册


