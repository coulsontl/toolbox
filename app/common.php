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
function Fcurl($url, $ifpost = 0, $datafields = '', $cookiefile = '', $v = false)
{
    $ip_long = array(
        array('607649792', '608174079'), //36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    $header = array(
        "Connection: Keep-Alive",
        "Accept: text/html, application/xhtml+xml, */*",
        "Pragma: no-cache",
        "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3",
        "User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)",
        'CLIENT-IP:' . $ip,
        'X-FORWARDED-FOR:' . $ip,
        'REMOTE_ADDR:' . $ip
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, $v);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $ifpost && curl_setopt($ch, CURLOPT_POST, $ifpost);
    $ifpost && curl_setopt($ch, CURLOPT_POSTFIELDS, $datafields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $cookiefile && curl_setopt($ch, CURLOPT_COOKIE, $cookiefile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //允许执行的最长秒数
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    $ok = curl_exec($ch);
    curl_close($ch);
    unset($ch);
    $ok = strToUTF8($ok);
    return $ok;
}

/**
 * 删除当前目录及其目录下的所有目录和文件
 * @param string $path 待删除的目录
 * @note  $path路径结尾不要有斜杠/(例如:正确[$path='./static/image'],错误[$path='./static/image/'])
 */
function deleteDir($path)
{
    // 在Vercel环境中不进行文件操作
    if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
        return true;
    }
    
    if (is_dir($path)) {
        //扫描一个目录内的所有目录和文件并返回数组
        $dirs = scandir($path);
        foreach ($dirs as $dir) {
            //排除目录中的当前目录(.)和上一级目录(..)
            if ($dir != '.' && $dir != '..') {
                //如果是目录则递归子目录，继续操作
                $sonDir = $path . '/' . $dir;
                if (is_dir($sonDir)) {
                    //递归删除
                    deleteDir($sonDir);
                    //目录内的子目录和文件删除后删除空目录
                    try {
                        @rmdir($sonDir);
                    } catch (\Exception $e) {
                        // 忽略错误
                    }
                } else {
                    //如果是文件直接删除
                    try {
                        @unlink($sonDir);
                    } catch (\Exception $e) {
                        // 忽略错误
                    }
                }
            }
        }
        try {
            @rmdir($path);
        } catch (\Exception $e) {
            // 忽略错误
        }
    }
}

function pageapi($key)
{
    $page = array(
        'index' => '首页',
        'header' => '全局<head>插件',
        'title' => '页面标题',
        'canonical' => '权威URL',
        'keywords' => '页面关键词',
        'description' => '页面描述',
    );
    return isset($page[$key]) ? $page[$key] : $key;
}

/*生成唯一标志
*标准的UUID格式为：xxxxxxxx-xxxx-xxxx-xxxxxx-xxxxxxxxxx(8-4-4-4-12)
*/

function uuid($num = 1, $dx = 2)
{
    $array = array();
    for ($i = 0; $num > $i; $i++) {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-'
            . substr($chars, 8, 4) . '-'
            . substr($chars, 12, 4) . '-'
            . substr($chars, 16, 4) . '-'
            . substr($chars, 20, 12);
        if (!in_array($uuid, $array)) {
            $array[] = ($dx == 1) ? strtoupper($uuid) : strtolower($uuid);
        } else {
            $i--;
        }
    }
    return $array;
}

/**
 * 生成GUID
 */
function create_guid()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}


function getBrowserOs()
{
    $flag = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/Windows[\d\. \w]*/', $flag, $match)) {
        $sys = $match[0];
    } else {
        $sys = 'Unknown';
    }
    // 检查操作系统
    if (preg_match('/Chrome\/[\d\.\w]*/', $flag, $match)) {
        // 检查Chrome
        $browser = $match[0];
    } elseif (preg_match('/Safari\/[\d\.\w]*/', $flag, $match)) {
        // 检查Safari
        $browser = $match[0];
    } elseif (preg_match('/MSIE [\d\.\w]*/', $flag, $match)) {
        // IE
        $browser = $match[0];
    } elseif (preg_match('/Opera\/[\d\.\w]*/', $flag, $match)) {
        // opera
        $browser = $match[0];
    } elseif (preg_match('/Firefox\/[\d\.\w]*/', $flag, $match)) {
        // Firefox
        $browser = $match[0];
    } elseif (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $flag, $match)) {
        //OmniWeb
        $browser = $match[2];
    } elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $flag, $match)) {
        //Netscape
        $browser = $match[2];
    } elseif (preg_match('/Lynx\/([^\s]+)/i', $flag, $match)) {
        //Lynx
        $browser = $match[1];
    } elseif (preg_match('/360SE/i', $flag, $match)) {
        //360SE
        $browser = '360安全浏览器';
    } elseif (preg_match('/SE 2.x/i', $flag, $match)) {
        //搜狗
        $browser = '搜狗浏览器';
    } else {
        $browser = 'unkown';
    }
    return [$sys, $browser];
}

function getip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
// IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

function strToUTF8($strText)
{
    $encode = mb_detect_encoding($strText, array('UTF-8', 'GB2312', 'GBK'));
    if ($encode != "UTF-8") {
        return @iconv($encode, 'UTF-8', $strText);
    } else {
        return $strText;
    }
}

function urlheader($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);  //输出header信息
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //不显示网页内容
    curl_setopt($curl, CURLOPT_ENCODING, ''); //允许执行gzip
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 8);
    $data = curl_exec($curl);
    $pHeader = array();
    if (!curl_errno($curl)) {
        $info = curl_getinfo($curl);
        $httpHeaderSize = $info['header_size'];  //header字符串体积
        $Header = substr($data, 0, $httpHeaderSize); //获得header字符串
        preg_match_all("/([A-Za-z_\-]*?): (.*?)\r/iU", $Header, $pat_array);
        $headers = array();
        foreach ($pat_array['1'] as $key => $vo) {
            $headers[$vo] = $pat_array['2'][$key];
        }
        $pHeader['head'] = $headers;
        $data = substr($data, $httpHeaderSize);
        $ysize = strlen($data);
        $pHeader['jc'] = array(
            'ystype' => isset($headers['Content-Encoding']) ? $headers['Content-Encoding'] : '-',
            'ysize' => $ysize,
            'yssize' => $info['size_download'],
            'ysl' => @round((100 - ($info['size_download'] / $ysize * 100)), 3),
        );
    }
    curl_close($curl);
    return $pHeader;
}

function urltitlecode($url)
{
    $url = htmlspecialchars_decode($url);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //不显示网页内容
    curl_setopt($curl, CURLOPT_ENCODING, ''); //允许执行gzip
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 8);
    $data = curl_exec($curl);
    $code = '404';
    $title = '页面不见啦！';
    if (!curl_errno($curl)) {
        $data = strToUTF8($data);
        $info = curl_getinfo($curl);
        $data = htmlspecialchars_decode($data);
        preg_match("/.*<title>(.*?)<\/title>.*/is", $data, $match);
        $title = isset($match['1']) ? $match['1'] : ' - ';
        $code = $info['http_code'];
    }
    curl_close($curl);
    return array(
        'code' => $code,
        'title' => $title
    );
}

function is_url($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
        return true;
    } else {
        return false;
    }
}

function page($array, $pagesize, $current)
{
    $_return = array();
    $count = Count($array);
    $total = ceil($count / $pagesize);//求总页数
    $current = ($current > ($total) ? ($total) : $current);//当前页如果大于总页数，当前页为最后一页
    $start = ($current - 1) * $pagesize;//分页显示时，应该从多少条信息开始读取
    $page = ($start + $pagesize);
    $page = $count < $page ? $count : $page;
    for ($i = $start; $i < $page; $i++) {
        if (isset($array[$i])) array_push($_return, $array[$i]);//将该显示的信息放入数组 $_return 中
    }
    return array(
        $_return,
        $total,
        $count
    );
}

function htmlTotext($str)
{
    $str = str_replace(array("\n", "\r", "\t", ' ', '&nbsp;'), '', $str);
    $str = preg_replace("/<style.*?>.*?<\/style.*?>/is", "", $str);
    $str = preg_replace("/<script.*?>.*?<\/script.*?>/is", "", $str);
    $str = strip_tags($str);
    return $str;
}

function webstatus($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);  //输出header信息
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //不显示网页内容
    curl_setopt($curl, CURLOPT_ENCODING, ''); //允许执行gzip
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 8);
    $data = curl_exec($curl);
    $Header = '';
    $code = '';
    $ip = '';
    if (!curl_errno($curl) && $data) {
        $info = curl_getinfo($curl);
        $httpHeaderSize = $info['header_size'];  //header字符串体积
        $Header = substr($data, 0, $httpHeaderSize); //获得header字符串
        $code = $info['http_code'];
        $ip = $info['primary_ip'];
    }
    curl_close($curl);
    return array(
        'head' => str_replace(array("\r", "\n"), array("<br/>", ""), $Header),
        'code' => $code,
        'ip' => $ip
    );
}

function get_curl($url, $post=0, $referer=0, $cookie=0, $header=0, $ua=0, $nobody=0, $addheader=0)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept: */*";
	$httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
	$httpheader[] = "Connection: close";
	if($addheader){
		$httpheader = array_merge($httpheader, $addheader);
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if($referer){
		curl_setopt($ch, CURLOPT_REFERER, $referer);
	}
	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	}
	else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36");
	}
	if ($nobody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

//域名whois查询
function whois_query($domain)
{
    $url = 'https://whois.aite.xyz/?ajax&domain='.urlencode($domain);
    $data = get_curl($url,0,'https://whois.aite.xyz/');
    if(!$data) return false;
    $data = str_replace(['<br/>','<br>'], "\n", $data);
    $data = strip_tags($data);
    if(strpos($data,'For more information on')){
        $data = substr($data, 0, strpos($data,'For more information on'));
    }
    return $data;
}

//ICP备案查询
function icp_query($domain){
    $timeStamp = time();
    $authKey = md5("testtest" . $timeStamp);
    $referer = 'https://beian.miit.gov.cn/';
    $headers = ['Origin: https://beian.miit.gov.cn'];
    $url = 'https://hlwicpfwc.miit.gov.cn/icpproject_query/api/auth';
    $post = 'authKey='.$authKey.'&timeStamp='.$timeStamp;
    $response = get_curl($url, $post, $referer, 0, 0, 0, 0, $headers);
    $arr = json_decode($response, true);
    if(isset($arr['code']) && $arr['code']==200){
        $token = $arr['params']['bussiness'];

        $url = 'https://hlwicpfwc.miit.gov.cn/icpproject_query/api/icpAbbreviateInfo/queryByCondition';
        $post = json_encode(['pageNum'=>'','pageSize'=>'','unitName'=>$domain,'serviceType'=>1]);
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'token: '.$token;
        $response = get_curl($url, $post, $referer, 0, 0, 0, 0, $headers);
        $arr = json_decode($response, true);
        if(isset($arr['code']) && $arr['code']==200){
            $list = [];
            foreach($arr['params']['list'] as $row){
                $list[] = ['domain'=>$row['domain'], 'mainLicence'=>$row['mainLicence'], 'webLicence'=>$row['serviceLicence'], 'unitName'=>$row['unitName'], 'unitType'=>$row['natureName'], 'updateTime'=>$row['updateRecordTime'], 'limitAccess'=>$row['limitAccess'], 'contentTypeName'=>$row['contentTypeName']];
            }
            return ['code'=>0, 'total'=>$arr['params']['total'], 'data'=>$list];
        }elseif(isset($arr['msg'])){
            throw new Exception($arr['msg']);
        }else{
            throw new Exception('查询接口(query)请求失败');
        }

    }elseif(isset($arr['msg'])){
        throw new Exception($arr['msg']);
    }else{
        throw new Exception('查询接口(auth)请求失败');
    }
}

function checkdomain($domain){
	if(empty($domain))return false;
	if (!preg_match('/^[a-zA-Z0-9:\_\.\-]{2,512}$/i', $domain) || strpos($domain, '.') === false || substr($domain, -1) == '.' || substr($domain, 0 ,1) == '.' || strpos($domain, '*') !== false) {
		return false;
	}
	return true;
}