<?php

/**
 * 公共函数库.
 *
 * @Date 2014/07/31
 */
//         4&(
//       ` ~&&\yM#1
//        ,_'Q!!NMW&
//        WCb 7N@4D Q%,,
//        PM'*MDk#M0p,
//            ]@J0&e~~4r' ,+bQEQ
//             F8I&#'   _&B$$bW#&$
//              &0A1   L#DE&E~!Q&Q,
// _=,        ,#0RN1  _T@0$'   ZN$Q.   grNq5
// ^ 'd     ,0K0pK^  g*Q0g'    #Q4p&,/g9X*&#,_/ (q
//  TA1   ,sDQWh4^  x&NM0` _   #FQ#K#fA#   `*K#XWP~-
//   ^&p,wNMM0qD: /HE#EN' ..#g)~ '@NG0Qx,    `=X*
//  '  '43$'hEk##m0D04f_g  ~^ ~   `-00**0
//           =0#ONq2W0BF^#, _            p,,
//             `  ^''~    ~b''        **R3`
//                      ow,F         +#F~'
//                      /-9!          ` \
//                       R

/**
 * 过滤函数.
 *
 * @param string $str 要过滤的内容
 *                    在测试服务器上 htmlentities 转实体符函数会将中文转成乱码 需要预定义一下;
 */
function htmlentities_linux($str)
{
    return htmlentities($str, ENT_NOQUOTES, 'utf-8');
}

/**
 * 根据时间戳返回日期格式.
 *
 * @param $time 时间戳;
 */
function get_date($time, $info = '无', $type = 'Y-m-d H:i:s')
{
    if ($time <= 0) {
        return $info;
    }

    return date($type, $time);
}

/**
 * 显示头像.
 *
 * @param $head 头像;
 */
function get_head($avatar = '')
{
}

/**
 * 根据字节自动单位转换;.
 *
 * @param $size 单位字节;
 *
 * @return kb||mb||gb;
 */
function size_format($size)
{
    $sizeStr = '';

    if ($size < 1024) {
        return $size.' Bytes';
    } elseif ($size < (1024 * 1024)) {
        $size = round($size / 1024, 1);

        return $size.' KB';
    } elseif ($size < (1024 * 1024 * 1024)) {
        $size = round($size / (1024 * 1024), 1);

        return $size.' MB';
    } else {
        $size = round($size / (1024 * 1024 * 1024), 1);

        return $size.' GB';
    }
}

/**
 * 向$URL POST数据;.
 */
function post_url($URL, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $res = trim(curl_exec($ch));
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = array();
    $result['result'] = $res;
    $result['httpcode'] = $httpcode;

    return $result;
}

/**
 * 加密签名;.
 */
function data_auth_sign($data)
{
    # 数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    //    usort($data, function ($v1, $v2) {
    //        return $v1 < $v2;
    //    });

    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名

    return $sign;
}

/**
 * 密码加密;.
 */
function md5_password($password)
{
    $PSW_PRE = C('PSW_PRE');
    $PSW_PRE = str_split($PSW_PRE, strlen($PSW_PRE) / 2);
    $password = $PSW_PRE[0].$password.$PSW_PRE[1];

    return md5(md5($password));
}

/**
 * 纯中文学校名称.
 */
function is_chinese($schoolname)
{
    $rex = "/^[\x7f-\xff]+$/";

    return preg_match($rex, $schoolname) == true;
}

/**
 * 密码规则 任意数字和字母组合 最少6位,最长20位;.
 */
function is_password($password)
{
    $rex = "/(?!\d+$)(?![A-Za-z]+$)[a-zA-Z0-9]{6,20}$/";

    return preg_match($rex, $password) == true;
}

/**
 * 学籍号规则 任意数字和字母组合 最少8位,最长20位;.
 */
function is_student_number($studentNumber)
{
    $rex = "/(?!\d+$)(?![A-Za-z]+$)[a-zA-Z0-9]{8,20}$/";

    return preg_match($rex, $studentNumber) == true;
}

/**
 * 学校名称长度.
 */
function is_school($companyName)
{
    $rex = "/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]{4,20}$/u";

    return preg_match($rex, $companyName) == true;
}

/**
 * 检查是否是数字.
 */
function is_number($number)
{
    return is_numeric($number);
}

/**
 * 手机号正则.
 */
function is_phonenumber($phonenumber)
{
    $rex = "/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8]))\d{8}$/";

    return preg_match($rex, $phonenumber) == true;
}

/**
 * 中文姓名正则.
 */
function is_realname($realname)
{
    $rex = "/^[\x{4e00}-\x{9fa5}]{2,6}(\·[\x{4e00}-\x{9fa5}]{2,6})?$/u";

    return preg_match($rex, $realname) == true;
}

/**
 * 用户名正则.
 */
function is_username($username)
{
    $rex = "/^[\w@\.]{6,20}$/";

    return preg_match($rex, $username) == true;
}

/**
 *   中文、英文、数字 @ . _ 混合字符串 2-20位.
 */
function is_mixname($username)
{
    $rex = '/^([\x{4e00}-\x{9fa5}A-Za-z0-9_\.@]+){2,20}$/u';

    return preg_match($rex, $username) == true;
}

/**
 *   中文、英文、数字 混合字符串 2-10位.
 */
function is_xqbname($username)
{
    $rex = '/^([\x{4e00}-\x{9fa5}A-Za-z0-9]+){2,10}$/u';

    return preg_match($rex, $username) == true;
}

/**
 * Email正则.
 */
function is_email($Email)
{
    $rex = "/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/u";

    return preg_match($rex, $Email) == true;
}

/**
 * 简单判断是否在微信游览器.
 */
function is_weixin()
{
    $UA = $_SERVER['HTTP_USER_AGENT'];

    return strpos($UA, 'MicroMessenger') !== false || $UA == 'Mozilla/4.0';
}

/**
 * 判断是否符合班级编码规则.
 *
 * @author Cui;
 *
 * @param string identifier 班级编码
 *
 * @return boolean;
 */
function is_identifier($identifier)
{
    $rex = '/^[0-9]{13}$/u';

    return preg_match($rex, $identifier) == true;
}

/**
 * 模板加减乘除.
 */
function temp_math($val1, $val2, $operator)
{
    switch ($operator) {
        case '+':$res = $val1 + $val2;
            break;
        case '-':$res = $val1 - $val2;
            break;
        case '*':$res = $val1 * $val2;
            break;
        case '/':$res = $val1 / $val2;
            break;
        default:$res = 0;
            break;
    }

    return $res;
}

/**
 * 检测验证码.
 *
 * @param int $id 验证码ID
 *
 * @return bool 检测结果
 */
function check_verify($code, $id = 2)
{
    $verify = new \COM\Verify();

    return $verify->check($code, $id);
}

/**
 * 系统加密方法.
 *
 * @param string $data   要加密的字符串
 * @param string $key    加密密钥
 * @param int    $expire 过期时间 单位 秒
 *
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; ++$i) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        ++$x;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; ++$i) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }

    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法.
 *
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 *
 * @return string
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $x = 0;
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }

    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; ++$i) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        ++$x;
    }

    for ($i = 0; $i < $len; ++$i) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }

    return base64_decode($str);
}

/**
 * 生成随机密码;.
 *
 * @param int $length 密码长度;
 *
 * @return String;
 */
function generate_password($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $password = '';
    for ($i = 0; $i < $length; ++$i) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }

    $length = strlen($password);
    //当前系统, 密码必须为字母+数字的组合;
    for ($i = 0; $i < $length; ++$i) {
        if (is_numeric($password[$i])) {
            break;
        }

        if ($i == $length - 1) {
            $password[mt_rand(0, $length - 1)] = rand(0, 9);
        }
    }

    return $password;
}

/**
 * 不在微信游览器内 输出提示页面;.
 */
function echo_not_weixin($project)
{
    send_http_status(404);
    echo file_get_contents(T($project.'@Public/notInWeixin'));
    exit;
}

/**
 * 中文字符串截取.
 *
 * @param string $str;0
 * @param int start 开始位置;
 * @param int    $length  长度;
 * @param string $charset 编码;
 * @param string $suffix  后缀;
 */
function msubstr($str, $start = 0, $length, $charset = 'utf-8', $suffix = true)
{
    $res = mb_substr($str, $start, $length, $charset);

    if ($start == 0 && mb_strlen($str, $charset) == mb_strlen($res, $charset)) {
        return $res;
    }

    if ($suffix) {
        $res .= '...';
    }

    return $res;
}

/**
 * 获取API实例.
 *
 * @author Cui.
 */
function API($className, $data = '')
{
    static $apiMap;

    if (!$className || !is_string($className)) {
        E('类名参数不正确');
    }

    if (is_array($apiMap) && array_key_exists($className, $apiMap)) {
        return $apiMap[$className];
    }

    $apiClass = '\\Project\\Api\\'.ucfirst($className).'Api';

    if (false === class_exists($apiClass)) {
        E($apiClass.'不存在');
    }

    $apiClass = new $apiClass($data);
    $apiMap[$className] = $apiClass;

    return $apiClass;
}

/**
 * 构建班级二维码链接.
 *
 * @author Cui
 *
 * @param string $identifier 班级编码
 *
 * @return string 链接
 */
function create_class_qrcode($identifier)
{
    if (!is_identifier($identifier)) {
        return '';
    }

    $url = C('MAIN_HOST').'/Dist/QRcode/'.think_encrypt($identifier);

    return $url;
}

/**
 * 获取应用信息.
 *
 * @param string $key 应用表里字段key的值
 *
 * @return array 此应用信息;
 */
function get_agent_info($key = '')
{
    $list = D('Agent')->getList();
    if ($key == '') {
        return $list;
    }

    if (array_key_exists($key, $list)) {
        return $list[$key];
    }

    return false;
}

/**
 * 获取应用信息.
 *
 * @param string $key 应用表里字段key的值
 *
 * @return int 此应用在企业号内的ID;
 */
function get_agent_id($key = '')
{
    $res = get_agent_info($key);

    if (!$res) {
        return false;
    }

    return intval($res['id_agent']);
}

/**
 * 下载用户头像;.
 *
 * @param string $url      用户微信头像地址;
 * @param string $filename 文件名 如果为空自动创建;
 *
 * @return string 图片本地地址;
 */
function download_avatar($url, $filename = '')
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0); //只取body头
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $package = curl_exec($ch);
    $httpinfo = curl_getinfo($ch);
    curl_close($ch);

    if ($httpinfo['http_code'] == 200 && $package != '') {
        $dir = C('UPLOAD_DIR').C('AVATAR_PATH');

        if (!is_dir($dir)) {
            @mkdir($dir);
        }

        if (!$filename) {
            $filename = date('Ym', time()).'/';

            if (!is_dir($dir.$filename)) {
                @mkdir($dir.$filename);
            }

            $filename = $filename.uniqid().'.jpg';
            $file = $dir.$filename;

            if (file_exists($file)) {
                return false;
            }
        } else {
            $file = $dir.$filename;
        }

        $res = file_put_contents($file, $package, LOCK_EX);
        if (false === $res) {
            return false;
        }

        return $filename;
    }

    return false;
}

/**
 * 获取用户头像;
 * 目前发现 微信头像的防盗链机制好像取消了, 所以直接可用, 但为了防止以后取消, 所以统一用函数来中转下头像地址.
 *
 * @param string $avatar 用户微信头像地址(用户表的avatar字段);
 *
 * @return string 图片地址;
 */
function get_avatar($avatar)
{
    if (!$avatar) {
        $avatar = '/Public/common/image/default_avatar.png';
    }

    return $avatar;
}

/**
 * 根据性别ID获取性别文本信息.
 *
 * @param $id 性别ID
 *
 * @return string 性别文本
 */
function get_gender($id)
{
    $db = D('User/Gender');
    $data = $db->getAllGender();
    if (!$data) {
        return '未知';
    }

    $text = array_filter($data, function ($v) use ($id) {
        if ($v['id'] == $id) {
            return true;
        }

        return false;
    });

    $text = reset($text);
    $text = $text['name'];

    return $text;
}

/**
 * 根据家长与孩子的关系ID获取关系文本信息.
 *
 * @param $id 关系ID
 *
 * @return string 关系文本
 */
function get_kinship($id)
{
    $db = D('User/Kinship');
    $data = $db->getAllKinship();
    if (!$data) {
        return '未知';
    }

    $text = array_filter($data, function ($v) use ($id) {
        if ($v['id'] == $id) {
            return true;
        }

        return false;
    });

    $text = reset($text);
    $text = $text['name'];

    return $text;
}

/**
 * 计算给定时间戳与当前时间相差的时间.
 *
 * @param [int] $timestamp    [给定的时间戳]
 * @param [int] $current_time [要与之相减的时间戳，默认为当前时间]
 *
 * @return [string] [相差天数]
 */
function tmspan($timestamp, $current_time = 0)
{
    if (!$current_time) {
        $current_time = time();
    }

    $time = $current_time - $timestamp;
    if ($time < 60) {
        return '刚刚';
    } elseif ($time < 3600) {
        return intval($time / 60).'分钟前';
    } elseif ($time < 24 * 3600) {
        return intval($time / 3600).'小时前';
    } elseif ($time < (7 * 24 * 3600)) {
        return intval($time / (24 * 3600)).'天前';
    } else {
        return date('Y-m-d', $timestamp);
    }
}

/**
 * 计算年级.
 *
 * @param int $createTime 入学年
 * @param int $verifyM    几月份后升级
 * @param int $verifyD    几号份后升级
 * @param int $endYr      结束年级
 *
 * @return string $result 中文当前年级
 */
function calculate_grade($createTime, $verifyM = '', $endYr = '', $verifyD = '')
{
    if ($verifyM == '') {
        $verifyM = C('UP_MONTH');
    }

    if ($verifyD == '') {
        $verifyD = C('UP_DAY');
    }

    if ($endYr == '') {
        $endYr = C('MAX_GRADE');
    }

    $time = time(); // 当前时间
    $nowYr = date('Y', $time); // 当前年
    $gapYr = $nowYr - $createTime; // 差年
    $nowM = intval(date('m', $time)); // 当前月份
    $nowDay = date('d', $time); // 当前日

    if ($gapYr > 1) {
        if ($nowM >= $verifyM && $nowDay >= $verifyD) {
            $gapYr += 1;
        }
    } elseif ($gapYr == 1) {
        if ($nowM >= $verifyM && $nowDay >= $verifyD) {
            $gapYr += 1;
        }
    } elseif ($gapYr <= 0) {
        if ($nowM >= $verifyM && $nowDay >= $verifyD) {
            $gapYr = 1;
        } else {
            $gapYr = 0;
        }
    } else {
        $gapYr = 0;
    }

    if ($gapYr >= $endYr) {
        $gapYr = $endYr;
    }
    $arr = array(
        '零',
        '一',
        '二',
        '三',
        '四',
        '五',
        '六',
        '七',
        '八',
        '九',
    );
    $result = $arr[$gapYr];

    return $result;
}

/**
 * 生成部门名称 (20xx级-x班).
 *
 * @param int $createTime 入学年
 * @param int $className  班级名
 * @param int $aptitude   资质类别
 *
 * @return string $result 部门名
 */
function create_sector_name($createTime, $className, $aptitude)
{
    $className = change_capital($className);
    $result = $createTime.'级-'.$aptitude.'-'.$className.'班';

    return $result;
}

/**
 * 两位数 数字转中文数字.
 *
 * @param int $num 入学年
 *
 * @return string $result 中文数字
 */
function change_capital($num)
{
    $number = ''.intval($num);
    $number = substr($number, 0, 2);
    $arr = array(
        '零',
        '一',
        '二',
        '三',
        '四',
        '五',
        '六',
        '七',
        '八',
        '九',
    );
    if (strlen($number) == 1) {
        $result = $arr[$number];
    } else {
        if ($number == 10) {
            $result = '十';
        } else {
            if ($number < 20) {
                $result = '十';
            } else {
                $result = $arr[substr($number, 0, 1)].'十';
            }

            if (substr($number, 1, 1) != '0') {
                $result .= $arr[substr($number, 1, 1)];
            }
        }
    }

    return $result;
}

/**
 * 中文转数字.
 *
 * @param int $num 一班二班
 *
 * @return string int 数字
 */
function change_chinese_name($name)
{
    $length = mb_strlen($name, 'utf-8');

    if ($length == 2) {
        $chineseName = msubstr($name, 0, 1, 'utf-8', false);
    } elseif ($length == 3) {
        $chineseName = msubstr($name, 0, 2, 'utf-8', false);
    }

    $arr = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二', '十三', '十四', '十五', '十六', '十七', '十八', '十九', '二十');
    $num = array_search($chineseName, $arr);

    return $num;
}

/**
 * 获取年级名.
 *
 * @param int $entranceYears 入学年
 * @param int $aptitude      资质
 */
function get_years($entranceYears, $aptitude)
{
    $arrayName = array();
    $arrayName['1']['零'] = '预入小学';
    $arrayName['1']['other'] = '年级';

    $arrayName['2']['零'] = '预入初中';
    $arrayName['2']['other'] = '初';

    $arrayName['3']['零'] = '预入高中';
    $arrayName['3']['other'] = '高';

    $inYr = calculate_grade($entranceYears);

    if ($inYr == '零') {
        $index = $arrayName[$aptitude];
    } else {
        $index = '';
    }

    if (array_key_exists($inYr, (array) $index)) {
        $res = $arrayName[$aptitude]['零'];
    } else {
        $res = $arrayName[$aptitude]['other'];
        $aptitude == 1 && $res = $inYr.$res;
        $aptitude > 1 && $res = $res.$inYr;
    }

    return $res;
}

/**
 * 加载数据库中的配置文件;.
 */
function load_sys_config()
{
    $config = S('DB_CONFIG_DATA');
    if (!$config) {
        $config = D('SystemConfig');
        $config = $config->lists();
        S('DB_CONFIG_DATA', $config);
    }

    $nowConfig = C('CONFIG_GROUP');

    if (false != $nowConfig && isset($config[$nowConfig])) {
        $config = array_merge($config[$nowConfig], $config[0]);
    } else {
        $config = $config[0];
    }

    C($config); //添加配置
}

/**
 * 获取配置的分组.
 *
 * @param string $group 配置分组
 *
 * @return string
 */
function get_config_group($group = 0)
{
    $list = C('CONFIG_GROUP_LIST');

    return $list[$group];
}

/**
 * 获取配置的类型.
 *
 * @param string $type 配置类型
 *
 * @return string
 */
function get_config_type($type = 0)
{
    $list = C('CONFIG_TYPE_LIST');

    return $list[$type];
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string)
{
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }

    return $value;
}

/**
 * 拼装用于展示的上传文件路径;.
 *
 * @author Cui
 *
 * @param $file string 不带Upload的上传文件路径 如 xx/xx/test.jpg;
 *
 * @return string 可以在游览器访问到的文件路径 如 http://clound.diglinx.cn/Upload/xx/xx/test.jpg;
 */
function get_upload_url($file = '', $flag = '/')
{
    $uploadUrl = C('TMPL_PARSE_STRING');
    $uploadUrl = $uploadUrl['__UPLOAD__'];

    return $uploadUrl.$flag.$file;
}

/**
 * 拼装Public文件路径;.
 *
 * @author Cui
 *
 * @param $file string 不带Public的样式文件路径 如 xx/xx/test.jpg;
 *
 * @return string 可以在游览器访问到的文件路径 如 http://clound.diglinx.cn/Public/xx/xx/test.jpg;
 */
function get_public_url($file = '', $flag = '/')
{
    $publicUrl = C('TMPL_PARSE_STRING');
    $publicUrl = $publicUrl['__PUBLIC__'];

    return $publicUrl.$flag.$file;
}

/**
 * /加密 解密 算法.
 *
 * @param string $str 要加密的字符串
 * @param boole  $de  加密/解密
 *
 * @return string
 */
function md6($str = '', $de = false)
{
    $key = (C('MD6_KEY'));
    $char = (C('MD6_CHAR'));
    if ($str != '') {
        if ($de) {
            $str = $char.$str;
            $str = think_decrypt($str, $key);
        } else {
            $str = think_encrypt($str, $key);
            $str = str_replace($char, '', $str);
        }
    }

    return $str;
}

/**
 * 获取后台用户类型.
 *
 * @author Wang
 *
 * @param string $module 模块名
 *
 * @return int;
 */
function get_admin_type($module = MODULE_NAME)
{
    switch ($module) {
        case 'Home':
            $type = \Common\Event\BaseCodeEvent::ADMIN_USER_TYPE;
            break;
        case 'Office':
            $type = \Common\Event\BaseCodeEvent::OFFICE_USER_TYPE;
            break;
        case 'Root':
            $type = \Common\Event\BaseCodeEvent::ROOT_USER_TYPE;
            break;
        default:
            return false;
    }

    return $type;
}

/**
 * 用于绑定域名后的跨分组组装URL.
 *
 * @author Cui
 *
 * @param string $url  路径
 * @param array  $args 参数
 *
 * @return int;
 */
function U_MAIN($url, $args = array())
{
    return C('MAIN_HOST').U($url, $args);
}

/**
 * 清除script并转译.
 *
 * @author LiuTong
 *
 * @param string 要验证的数据
 *
 * return string;
 */
function lt_replace($date)
{
    $rex = '/<script.*?>/';

    return htmlspecialchars(preg_replace($rex, '', $date));
}
/*
 *自动加版本号
 */
function AutoVersion($file)
{
    if (file_exists($_SERVER['DOCUMENT_ROOT'].$file)) {
        $ver = filemtime($_SERVER['DOCUMENT_ROOT'].$file);
    } else {
        $ver = 1;
    }

    return $file.'?v='.$ver;
}

/**
 * 拼装用于展示的下载文件路径;.
 *
 * @param $file string 不带Download的上传文件路径 如 xx/xx/test.jpg;
 *
 * @return string 可以在游览器访问到的文件路径 如 http://clound.diglinx.cn/Download/xx/xx/test.jpg;
 */
function get_download_url($file = '', $flag = '/')
{
    $uploadUrl = C('TMPL_PARSE_STRING');
    $uploadUrl = $uploadUrl['__DOWNLOAD__'];

    return $uploadUrl.$flag.$file;
}

/**
 * 获取请求头部信息.
 *
 * @author Cui
 */
function get_requset_headers()
{
    if (function_exists('getallheaders')) {
        return getallheaders();
    }

    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }

    return $headers;
}

/**
 * 获取入学年份.
 *
 * @param $grade int
 */
function get_entrance_year($grade)
{
    $nowDate = getdate();
    $nowYear = $nowDate['year'];
    $nowMon = $nowDate['mon'];
    $nowMday = $nowDate['mday'];

    if (($nowMon == 7 && $nowMday > 15) || $nowMon > 7) {
        $start = $nowYear + 1;
    } else {
        $start = $nowYear;
    }

    $gradeText = array($start => 0);
    $gradeText[$start - 1] = 1;
    $gradeText[$start - 2] = 2;
    $gradeText[$start - 3] = 3;
    $gradeText[$start - 4] = 4;
    $gradeText[$start - 5] = 5;
    $gradeText[$start - 6] = 6;

    return array_search($grade, $gradeText);
}

/**
 * 简化调用Logic层.
 *
 * @author Cui
 *
 * @param $logic string 逻辑层类名.
 *
 * @return Object;
 */
function P($logic)
{
    return D($logic, 'Logic');
}

/**
 * 获取泛域名参数.
 *
 * @author Yi
 *
 * @param $logic string 逻辑层类名.
 *
 * @return int;
 */
function get_domain_param()
{
    $httpHost = explode('.', $_SERVER['HTTP_HOST']);

    return $httpHost[0];
}

/**
*
* 压缩用户头像
*
* @author XueFeng
*
* @return base_encode $im 返回加密后的图片二进制    
*
**/
function setUserImage($url)
{
    $resource = getBaseodeByUrl($url);
    $resource = base64_decode($resource);
    $bg_w       = 139; // 背景图片宽度  
    $bg_h       = 139; // 背景图片高度  

    $background = imagecreatetruecolor($bg_w,$bg_h);// 背景图片  
    $color      = imagecolorallocate($background, 202, 201, 201);
    // 为真彩色画布创建白色背景，再设置为透明  
    imagefill($background, 0, 0, $color);  
    imageColorTransparent($background, $color);

    $resource   = imagecreatefromstring($resource);
    imagecopyresized($background, $resource, 0, 0, 0, 0, 139, 139,imagesx($resource),imagesy($resource));

    $image = "/tmp/image_".rand(1,10000000).".jpg";
    imagejpeg($background, $image,100);

    $file = file_get_contents($image);
    unlink($image);
    return base64_encode($file);
}
/**
*
* 获取图片，返回图片信息
*
* @author XueFeng
*
* @param string $src 图片路径   
*
* @return resource $im 返回图片信息    
*
**/
function getImageInfo($src)     
{     
    return getimagesize($src);
}

/**
* 创建图片，返回资源类型
*
* @author XueFeng
*
* @param string $src 图片路径
*
* @return resource $im 返回资源类型
* 
**/
function create($src)
{
    $info = getImageInfo($src);

    switch ($info[2])
    {
        case 1:
            $im = imagecreatefromgif($src);
            break;
        case 2:
            $im = imagecreatefromjpeg($src);
            break;
        case 3:
            $im = imagecreatefrompng($src);
            break;
    }
    return $im;     
}     

/**
* 缩略图主函数
*
* @author XueFeng
*
* @param string $src 图片路径
* @param int    $w   缩略图宽度
* @param int    $h   缩略图高度
*
* @return mixed 返回缩略图路径
**/
function resize($src,$w,$h)
{
    $temp     = pathinfo($src);
    $name     = $temp["basename"];//文件名
    $savepath = "/tmp/" . $name . ".jpg";//缩略图保存路径,新的文件名为*.jpg
    
    //获取图片的基本信息
    $info   = getImageInfo($src);

    $width  = $info[0];//获取图片宽度
    $height = $info[1];//获取图片高度
    $per1   = round($width/$height,2);//计算原图长宽比
    $per2   = round($w/$h,2);//计算缩略图长宽比
    
    //计算缩放比例     
    if($per1 > $per2 || $per1 == $per2) {
        //原图长宽比大于或者等于缩略图长宽比，则按照宽度优先
        $per  = $w/$width;
    }
    if($per1 < $per2) {
        //原图长宽比小于缩略图长宽比，则按照高度优先
        $per  = $h/$height;
    }
    $temp_w   = intval($width*$per);//计算原图缩放后的宽度
    $temp_h   = intval($height*$per);//计算原图缩放后的高度
    $temp_img = imagecreatetruecolor($temp_w,$temp_h);//创建画布
    $im       = create($src);
    imagecopyresampled($temp_img,$im,0,0,0,0,$temp_w,$temp_h,$width,$height);
    
    if($per1>$per2) {
        imagejpeg($temp_img,$savepath, 100);
        imagedestroy($im);
        return addBg($savepath,$w,$h,"w");
        //宽度优先，在缩放之后高度不足的情况下补上背景
    }
    if($per1 == $per2) {
        imagejpeg($temp_img,$savepath, 100);
        imagedestroy($im);
        return $savepath;
        //等比缩放
    }
    if($per1<$per2) {
        imagejpeg($temp_img,$savepath, 100);
        imagedestroy($im);
        return addBg($savepath,$w,$h,"h");
        //高度优先，在缩放之后宽度不足的情况下补上背景
    }
}

/**
* 添加背景
* @author XueFeng
* @param string $src 图片路径
* @param int $w 背景图像宽度
* @param int $h 背景图像高度
* @param String $first 决定图像最终位置的，w 宽度优先 h 高度优先 wh:等比
* @return 返回加上背景的图片
* **/
function addBg($src,$w,$h,$fisrt = "w")
{
    $bg     = imagecreatetruecolor($w,$h);
    $white  = imagecolorallocate($bg,255,255,255);
    imagefill($bg,0,0,$white);//填充背景

    //获取目标图片信息
    $info   = getImageInfo($src);
    $width  = $info[0];//目标图片宽度
    $height = $info[1];//目标图片高度
    $img    = create($src);
    if($fisrt == "wh") {
        //等比缩放
        return $src;
    } else {     
        if($fisrt == "w") {     
            $x = 0;     
            $y = ($h-$height)/2;//垂直居中
        }
        if($fisrt == "h")
        {
            $x = ($w-$width)/2;//水平居中
            $y = 0;
        }
        imagecopymerge($bg,$img,$x,$y,0,0,$width,$height,100);     
        imagejpeg($bg,$src,100);     
        imagedestroy($bg);     
        imagedestroy($img);     
        return $src;     
    }
}

/**
* 获取微信头像二进制
* @author XueFeng
* @param string $url 图片路径
* @return 图片加密后的二进制
* **/
function getBaseodeByUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $res = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    list($header, $body) = explode("\r\n\r\n", $res, 2);

    return base64_encode($body);
}

/**
* 生成群头像图片 尺寸45x45
* @author XueFeng
* @param array $pic_list 图片二进制数组
* @return 图片加密后的二进制
* **/
function setnine($pic_list){

    $pic_list = array_slice($pic_list, 0, 9); // 只操作前9个图片
    $bg_w       = 150; // 背景图片宽度  
    $bg_h       = 150; // 背景图片高度  
  
    $background = imagecreatetruecolor($bg_w,$bg_h); // 背景图片  
    $color      = imagecolorallocate($background, 202, 201, 201); // 为真彩色画布创建白色背景，再设置为透明  
    imagefill($background, 0, 0, $color);  
    imageColorTransparent($background, $color);

    $pic_count  = count($pic_list);  
    
    $lineArr    = array();  // 需要换行的位置  
    $space_x    = 3;  
    $space_y    = 3;  
    $line_x     = 0;  
    switch($pic_count) {  
    case 1: // 正中间  
        $start_x = intval($bg_w/4);  // 开始位置X  
        $start_y = intval($bg_h/4);  // 开始位置Y  
        $pic_w   = intval($bg_w/2); // 宽度  
        $pic_h   = intval($bg_h/2); // 高度  
        break;  
    case 2: // 中间位置并排  
        $start_x = 2;  
        $start_y = intval($bg_h/4) + 3;  
        $pic_w   = intval($bg_w/2) - 5;  
        $pic_h   = intval($bg_h/2) - 5;  
        $space_x = 5;  
        break;  
    case 3:  
        $start_x = 40;   // 开始位置X  
        $start_y = 5;    // 开始位置Y  
        $pic_w   = intval($bg_w/2) - 5; // 宽度  
        $pic_h   = intval($bg_h/2) - 5; // 高度  
        $lineArr = array(2);  
        $line_x  = 4;  
        break;  
    case 4:  
        $start_x = 4;    // 开始位置X  
        $start_y = 5;    // 开始位置Y  
        $pic_w   = intval($bg_w/2) - 5; // 宽度  
        $pic_h   = intval($bg_h/2) - 5; // 高度  
        $lineArr = array(3);  
        $line_x  = 4;  
        break;  
    case 5:  
        $start_x = 30;   // 开始位置X  
        $start_y = 30;   // 开始位置Y  
        $pic_w   = intval($bg_w/3) - 5; // 宽度  
        $pic_h   = intval($bg_h/3) - 5; // 高度  
        $lineArr = array(3);  
        $line_x  = 5;  
        break;  
    case 6:  
        $start_x = 5;    // 开始位置X  
        $start_y = 30;   // 开始位置Y  
        $pic_w   = intval($bg_w/3) - 5; // 宽度  
        $pic_h   = intval($bg_h/3) - 5; // 高度  
        $lineArr = array(4);  
        $line_x  = 5;  
        break;  
    case 7:  
        $start_x = 53;   // 开始位置X  
        $start_y = 5;    // 开始位置Y  
        $pic_w   = intval($bg_w/3) - 5; // 宽度  
        $pic_h   = intval($bg_h/3) - 5; // 高度  
        $lineArr = array(2,5);  
        $line_x  = 5;  
        break;  
    case 8:  
        $start_x = 30;   // 开始位置X  
        $start_y = 5;    // 开始位置Y  
        $pic_w   = intval($bg_w/3) - 5; // 宽度  
        $pic_h   = intval($bg_h/3) - 5; // 高度  
        $lineArr = array(3,6);  
        $line_x  = 5;  
        break;  
    case 9:  
        $start_x = 5;    // 开始位置X  
        $start_y = 5;    // 开始位置Y  
        $pic_w   = intval($bg_w/3) - 5; // 宽度  
        $pic_h   = intval($bg_h/3) - 5; // 高度  
        $lineArr = array(4,7);  
        $line_x  = 5;  
        break;  
    }
    foreach( $pic_list as $k => $pic_base ) {
        
        $kk = $k + 1;
        if ( in_array($kk, $lineArr) ) {  
            $start_x = $line_x;  
            $start_y = $start_y + $pic_h + $space_y;  
        }

        $pic_base = base64_decode($pic_base);
        $resource = imagecreatefromstring($pic_base);
        // $start_x,$start_y copy图片在背景中的位置
        // 0,0 被copy图片的位置
        // $pic_w,$pic_h copy后的高度和宽度
        imagecopyresized($background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource));
        //最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度
        $start_x  = $start_x + $pic_w + $space_x;
    }

    imagejpeg($background, "/tmp/nine.jpeg",100);
    file_get_contents("/tmp/nine.jpeg");
    
    $url  = resize("/tmp/nine.jpeg", "139", "139");
    $file = file_get_contents($url);
    unlink("/tmp/nine.jpeg");
    unlink($url);
    return base64_encode($file);
}

function pdf2png($PDF,$Path,$xp = 120,$yp = 120,$quality = 100)
{

   if( !extension_loaded('imagick') ) {
       return false;
   }

   if( !file_exists($PDF) ) {
       return false;
   }

   $IM = new imagick($PDF);
   $IM->setImageBackgroundColor('#ffffff');
   $IM->setResolution($xp,$yp);
   $IM->setCompressionQuality($quality);
   //$IM->readImage($PDF);
   $IM = $IM->flattenImages();

   $Return = [];

   foreach($IM as $Key => $Var) {
       $Var->setImageFormat('png');
       $Filename = $Path.'/'.uniqid().rand(1000,9999).'.png';
       if( $Var->writeImage($Filename) == true ){
           $Return[]= $Filename;
       }else{
            $ERR[] = $Key;
       }
   }



   return $Return;
}
function objectToArray($obj){

    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if(is_array($arr)){
        return array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}
