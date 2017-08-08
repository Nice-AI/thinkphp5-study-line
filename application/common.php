<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function getJson($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}

function get_uri($url)
{
    $ch = curl_init() or die (curl_error());
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 360);
    $response = curl_exec($ch);
    curl_close($ch);
    //显示获得的数据
    return $response;
}

/**
 * 参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
 * @param $url
 * @param string $post
 * @param string $cookie
 * @param int $returnCookie
 * @return mixed|string
 */
function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
{
    // 设置头部信息返回为json 格式
    $headers = ["Accept: application/json"];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if ($returnCookie) {
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie'] = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    } else {
        return $data;
    }
}

/**
 * 生成 auth_key
 * @param string $check_str
 * @return string
 */
function get_auth_key($check_str)
{
    if (empty($check_str)) return ['valid' => 0, 'msg' => "参数不合适"];
    // 1 过期时间
    $send_email_expire_time = config('email.EMAIL_SEND_EXPIRE_TIME');
    // 2 私有密钥
    $send_email_private_key = config('email.EMAIL_SEND_PRIVATE_KEY');
    $timestatmp = strtotime(date('Y-m-d H:i:s', strtotime("+" . $send_email_expire_time . "minute")));
    $uuid = 0;
    $uid = 0;
    $hash_value = md5($check_str . '-' . $timestatmp . '-' . $uuid . '-' . $uid . '-' . $send_email_private_key);
    $auth_key = $timestatmp . '-' . $uuid . '-' . $uid . '-' . $hash_value;
    return $auth_key;
}

/**
 * 邮箱地址有效期验证
 * @param string $check_str
 * @param string $auth_key
 * @return array
 */
function check_auth_key($check_str, $auth_key)
{
    if (empty($check_str) && empty($auth_key)) return ['valid' => 0, 'msg' => "参数不合适"];
    $send_email_expire_time = substr($auth_key, 0, 10);
    if ($send_email_expire_time < time()) return ['valid' => 0, 'msg' => "该URL地址已经过期了"];
    $uuid = 0;
    $uid = 0;
    $send_email_private_key = config('email.EMAIL_SEND_PRIVATE_KEY'); // 私有密钥
    $sequest_hash_value = substr($auth_key, -32);
//    return $sequest_hash_value;
    $hash_value = md5($check_str . '-' . $send_email_expire_time . '-' . $uuid . '-' . $uid . '-' . $send_email_private_key);
    if ($hash_value != $sequest_hash_value) return ['valid' => 0, 'msg' => "URL地址签名错误"];
    return ['valid' => 1, 'msg' => "签名验证通过"];
}

/**
 * 发送邮件
 * @param  array $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return array  放回状态吗和提示信息
 */
function send_email($address, $subject, $content)
{
    $email_smtp = config('email.EMAIL_SMTP');
    $email_username = config('email.EMAIL_USERNAME');
    $email_password = config('email.EMAIL_PASSWORD');
    $email_from_name = config('email.EMAIL_FROM_NAME');
    if (empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)) {
        return ["error" => 1, "message" => '邮箱请求参数不全，请检测邮箱的合法性'];
    }
    $phpmailer = new PHPMailer();
    // 	设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 	设置为html格式
    $phpmailer->IsHTML(true);
    // 	设置邮件的字符编码'
    $phpmailer->CharSet = 'UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host = $email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth = true;
    // 设置用户名
    $phpmailer->Username = $email_username;
    // 设置密码
    $phpmailer->Password = $email_password;
    // 设置邮件头的From字段。
    $phpmailer->From = $email_username;
    // 设置发件人名字
    $phpmailer->FromName = $email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if (is_array($address)) {
        foreach ($address as $addressv) {
            //验证邮件地址,非邮箱地址返回为false
            if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return ["error" => 1, "message" => '邮箱格式错误'];
            }
            $phpmailer->AddAddress($addressv);
        }
    } else {
        //验证邮件地址,非邮箱地址返回为false
        if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return ["error" => 1, "message" => '邮箱格式错误'];
        }
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject = $subject;
    // 设置邮件正文,这里最好修改为这个，不是boby
    $phpmailer->MsgHTML($content);
    // 发送邮件。
    if (!$phpmailer->Send()) {
        return ["error" => 1, "message" => $phpmailer->ErrorInfo];
    }
    return ["error" => 0];
}

//--------------------------------------------------auth2.0------------------------------------------------------------
/**
 * 获取github信息
 */
function oauth_github()
{
    $github_url = config('github.OAUTH_URL');
    // 这个参数是必须的，这就是我们在第一步注册应用程序之后获取到的Client ID；
    $client_id = config('github.OAUTH_URL');
    // 该参数可选，当我们从Github获取到code码之后跳转到我们自己网站的URL
    $redirect_uri = config('github.OAUTH_URL');
    $url = $github_url . "?client_id=" . $client_id . "&redirect_uri=" . $redirect_uri;
    header('location:' . $url);
}

/**
 * github 回调地址
 * @param Request $request
 */
function github_redirect_uri(Request $request)
{
    //'code' => string '137b34c45d7282436d53'
    $code = $request->get('code');
    $client_id = config('github.OAUTH_URL');
    $client_secret = config('github.OAUTH_URL');
    $access_token_url = config('github.ACCESS_TOKEN_URL');
    //第一步:取全局access_token
    $postRes = $this->curl_request($access_token_url, [
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "code" => $code,
    ]);
    //第三步:根据全局access_token和openid查询用户信息
    $jsonRes = json_decode($postRes, true);
    $access_token = $jsonRes["access_token"];
    $userUrl = config('github.USER_INFO_URL') . "?access_token=" . $access_token;
    $userInfo = $this->curl_request($userUrl);
    $userJsonRes = json_decode($userInfo, true);
    //第五步，如何设置Wordpress中登录状态
    return $userJsonRes;
}


function get_global_db_config()
{
    $global_db_config = array(
        'log_table' => config('database.log_table'),
        'username' => config('database.username'),
        'password' => config('database.password'),
        'hostname' => config('database.hostname'),
        'database' => config('database.database'),
    );
    return $global_db_config;
}

/**
 * 记录操作日志
 * @param null $desc
 * @param string $unique_flag
 * @param $app
 * @param $action
 * @param $method
 * @return bool
 */
function add_operation_log($desc = null, $unique_flag = 'system')
{
    $instance = \think\Request::instance();
    $module = $instance->module();
    $controller = $instance->controller();
    $action = $instance->action();
    $config = get_global_db_config();
    $conn = new \mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    mysqli_query($conn, 'set names utf8');
    $account = get_admin_account();
    $nickname = get_admin_nickname();
    $user_id = get_admin_user_id();
    $ipaddr = request()->ip();
    $query_string = implode('--', array_merge($_GET, $_POST, input()));
    $is_desc = 0;

    if ($desc) $is_desc = 1;
    $insert_time = date('Y-m-d H:i:s');
    $query = "INSERT INTO `" . $config['log_table'] . "` (`guid`,`account`,`nickname`,`addtime`,`module`,`controller`,
    `action`,`query_string`,`is_desc`,`desc`,`ipaddr`,`unique_flag`) VALUES ('$user_id','$account','$nickname','$insert_time','$module',
    '$controller','$action','$query_string','$is_desc','$desc','$ipaddr','$unique_flag');";
    if (mysqli_query($conn, $query)) {
        $result = TRUE;
    } else {
        //$		result = "Error:" . $query . "<br/>" . mysqli_error($conn);
        //用于以后调试
        $result = FALSE;
    }
    return $result;
}

/**
 * @return mixed
 */
function get_admin_account()
{
    return session('admin.username');
}

/**
 * 获取用户昵称
 * @return mixed
 */
function get_admin_nickname()
{
    return session('admin.username');
}


/**
 * 获取用户ID信息，这条信息是用户登陆的时候保存的
 * @return mixed
 */

function get_admin_user_id()
{
    return session('admin.admin_id');
}


/*************************************经验值转换为等级 **********************************
 *
 * @param  [type] $exp [description]
 * @return [type]      [description]
 */
function points_to_level($exp)
{
    switch (true) {
        case $exp >= config('points.LV20') :
            return LV20;
        case $exp >= config('points.LV19') :
            return 19;
        case $exp >= config('points.LV18') :
            return 18;
        case $exp >= config('points.LV17') :
            return 17;
        case $exp >= config('points.LV16') :
            return 16;
        case $exp >= config('points.LV15') :
            return 15;
        case $exp >= config('points.LV14') :
            return 14;
        case $exp >= config('points.LV13') :
            return 13;
        case $exp >= config('points.LV12') :
            return 12;
        case $exp >= config('points.LV11') :
            return 11;
        case $exp >= config('points.LV10') :
            return 10;
        case $exp >= config('points.LV9') :
            return 9;
        case $exp >= config('points.LV8') :
            return 8;
        case $exp >= config('points.LV7') :
            return 7;
        case $exp >= config('points.LV6') :
            return 6;
        case $exp >= config('points.LV5') :
            return 5;
        case $exp >= config('points.LV4') :
            return 4;
        case $exp >= config('points.LV3') :
            return 3;
        case $exp >= config('points.LV2') :
            return 2;
        default :
            return 1;
    }
}

/**
 * -----------------------------------------------------------文件管理-------------------------------------
 */

/**
 * 转换字节大小 Bytes/Kb/MB/GB/TB/EB
 * @param $size
 * @return string
 */
function trans_byte($size)
{
    $size_arr = ["B", "KB", "MB", "GB", "TB", "EB"];
    $i = 0;
    while ($size >= 1024) {
        $size = $size / 1024;
        $i++;
    }
    return round($size, 2) . $size_arr[$i];
}

/**
 * ---------------------------------------------------------------短信------------------------------------
 */

/**
 * 阿里大于发送模板
 * @param $tel   电话号码，如：13669361192
 * @param $type  发送模板类型，如：live
 * @param $data  模板发送的数据，如：["number" => '89', 'code' => "888888"]
 * @return mixed
 */
function send_dayu_sms($tel, $type, $data)
{
    $dayu_template = 'template_' . $type; //template_register
    $signname = config("sms")['dayu'][$dayu_template]["sign_name"];
    $templatecode = config("sms")['dayu'][$dayu_template]["code"];
    $config = [
        'app_key' => config("sms")['dayu']['app_key'],
        'app_secret' => config("sms")['dayu']['app_secret']
    ];
    $client = new \Flc\Alidayu\Client(new \Flc\Alidayu\App($config));
    $req = new \Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend();
    $req->setRecNum("{$tel}");
    switch ($type) {
        case 'register':
            $req->setSmsParam([
                'code' => $data['code']
            ]);
            break;
        case 'live':
            $req->setSmsParam([
                'number'=>$data['number'],
                'code'=>$data['code']
            ]);
            break;
        case 'identity':
            $req->setSmsParam('{"name":"' . $data['name'] . '"}');
            break;
        default:
            $req->setSmsParam('{"code":"' . $data['code'] . '","product":"' . $data['product'] . '"}');
    }
    $req->setSmsFreeSignName("{$signname}");
    $req->setSmsTemplateCode("{$templatecode}");
    $resp = $client->execute($req);
    return $resp;
}

