<?php
//////////////////////////////////
/// 历史上的今天 API，请注意防范 SQL 注入，我已经做了比较好的防护了，
/// 但我也不知道还有哪些方法会导致 SQL 注入，最好的办法就是自己重写，使用 PDO 或别人封装好的 MySQL 方法类库
/// GitHub：https://github.com/PrintNow/TodayInHistory
/// 作者：Chuwen<chenwenzhou@aliyun.com>
/// 请保留本注释，谢谢
///
/// 使用方法：
/// 参数说明：month：查询哪个月，默认本月  day：查询哪一天，默认今天  order：排序，默认升序
/// 访问 /api.php?month=1&day=1&order=desc
//////////////////////////////////

$servername = "192.168.3.1";
$username = "nowtime.cc";//数据库 用户名
$dbname = "nowtime.cc";//数据库 库名
$password = "password";//数据库 密码

$month = get('month', date('m'), 'intval');
$day = get('day', date('d'), 'intval');
$order = get('day', 'desc');

//判断月份是否满足
if ($month < 1 || $month > 12) {
    die(json_encode([
        'code' => -1,
        'msg' => '"month" 参数错误'
    ]));
}

//判断日期是否满足
if ($day < 1 || $day > 31) {
    die(json([
        'code' => -1,
        'msg' => '"day" 参数错误'
    ]));
}

//判断排序是否满足
if (!in_array($order, ['desc', 'asc'])) {
    die(json([
        'code' => -1,
        'msg' => '仅接受 "asc" 或 "desc" 排序'
    ]));
}

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// 检测连接
if ($conn->connect_error) {
    die(json_encode([
        'code' => 500,
        'msg' => "数据库连接失败: " . $conn->connect_error
    ]));
}

//通过以上三者判断，基本不会出现 SQL 注入情况。但也不完全保证，请自行进行修改
$sql = "SELECT year,month,day,type,data FROM `today_in_history` WHERE `month`='{$month}' AND `day`='{$day}' ORDER BY `year` {$order}";
//$sql = "SELECT year,month,day,type,data FROM `today_in_history`";
$result = $conn->query($sql);

if (!empty($conn->error)) {
    //echo $conn->error;//SQL 语句执行出现错误，请取消注释该行查看注释
    die(json_encode([
        'code' => 500,
        'msg' => '查询失败，内部服务错误，请站长定位到本源码第63行'
    ]));
}

$res = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {// 输出数据
        $res[] = $row;
    }
}

echo json_encode([
    'code' => 200,
    'msg' => 'ok',
    'count' => count($res),
    'disclaimer' => '数据源于“维基百科”，经过加工后为您呈现本数据，数据采集于 2020/04/12 12:00，在此之后一些数据可能发生了改变，请以事实为准！本站不承担任何因数据改变而造成的任何责任',
    'data' => $res,
]);


$conn->close();//关闭数据库连接


/**
 * 获取 GET 参数助手
 * @author  Chuwen <chenwenzhou@aliyun.com>
 * @link    https://github.com/PrintNow/TodayInHistory
 *
 * @param  string $field     需要获取的 GET 参数
 * @param  string $default   如果没有默认输出的值
 * @param  string $filter    过滤器，仅支持 php 自带的，如传入  intval 就会将获取到的值进行转换成 数值型
 *
 * @return mixed             输出结果
 */
function get($field, $default = '', $filter = '')
{
    $val = isset($_GET[$field]) ? $field : $default;

    if (!empty($val) && !empty($default)) {
        if (function_exists($filter)) {
            $val = $filter($val);
        }
    }

    return $val;
}