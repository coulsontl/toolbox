<?php

namespace app\controller;

use Exception;
use think\facade\View;

class Index
{
    public function index()
    {
        $data = array();
        $act = input('act', 'index');


        // 检测是否在 Vercel 环境
        $is_vercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']);

        switch ($act) {
            case 'uuid':
                $data['uuid_number'] = input('uuid_number', 1);
                $data['uuid_letter'] = input('uuid_letter', 2);
                $data['uuid'] = uuid($data['uuid_number'], $data['uuid_letter']);
                break;
            case 'guid':
                $data['guid_number'] = input('guid_number', 1);
                $data['guid_letter'] = input('guid_letter', 1);
                $array = array();
                for ($i = 0; $data['guid_number'] > $i; $i++) {
                    $guid = create_guid();
                    if (!in_array($guid, $array)) {
                        $array[] = ($data['guid_letter'] == 1) ? strtoupper($guid) : strtolower($guid);
                    } else {
                        $i--;
                    }
                }
                $data['guid'] = $array;
                break;
            case 'md5':
                $data['txt_md5'] = input('txt_md5', '');
                $md532 = md5($data['txt_md5']);
                $md516 = substr($md532, 8, 16);
                $data['md532_d'] = strtoupper($md532);
                $data['md532_x'] = strtolower($md532);
                $data['md516_d'] = strtoupper($md516);
                $data['md516_x'] = strtolower($md516);
                break;
            case 'caiji':
                $data['url'] = input('url', '');
                $data['content'] = $data['url'] ? Fcurl($data['url']) : '';
                break;
            case 'ip':
                if (request()->isPost()) {
                    $ip = input('post.ip');
                    return redirect('/ip/' . $ip . '.html', 302);
                }
                try {
                    $ips = new \Net\Ips(app()->getRootPath().'QQWry.dat');
                    $ip = input('ip');
                    if ($ip) {
                        $data['ym']['ip'] = $ip;
                        $data['ym']['domain'] = gethostbyname($ip);
                        $domain = preg_replace('/(\d+)..*/', '\\1', $data['ym']['domain']);
                        if ('1' <= $domain && $domain <= '126') {
                            $data['ym']['fw'] = '1.0.0.1 - 126.155.255.254';
                        } elseif ('128' <= $domain && $domain <= '191') {
                            $data['ym']['fw'] = '128.0.0.1 - 191.255.255.254';
                        } elseif ('192' <= $domain && $domain <= '223') {
                            $data['ym']['fw'] = '192.0.0.1 - 223.255.255.254';
                        }
                        $fwq = $ips->Getlocation($data['ym']['domain']);
                        $data['ym']['city'] = strToUTF8($fwq['country'] . ' ' . $fwq['area']);
                    }
                    $data['getip'] = getip();
                    $data['getBrowserOs'] = getBrowserOs();
                    $city = $ips->Getlocation($data['getip']);
                    $data['city'] = strToUTF8($city['country'] . ' ' . $city['area']);
                } catch (\Exception $e) {
                    // IP 查询功能暂时不可用
                    $data['getip'] = getip();
                    $data['getBrowserOs'] = getBrowserOs();
                    $data['city'] = 'IP查询功能暂时不可用';
                }
                break;
            default:
                // 默认情况，显示首页
                break;
        }

        // 在 Vercel 环境中，如果模板加载失败，直接返回简化的 HTML
        if ($is_vercel) {
            try {
                return View::fetch($act, $data);
            } catch (\Exception $e) {
                // Vercel 环境下的降级处理
                if ($act === 'index') {
                    return $this->getSimpleHomePage();
                }
                return json([
                    'framework' => 'ThinkPHP 8.0 Toolbox',
                    'action' => $act,
                    'data' => $data,
                    'message' => 'Vercel 环境 - 模板系统降级',
                    'template_error' => $e->getMessage()
                ]);
            }
        }

        try {
            return View::fetch($act, $data);
        } catch (\Exception $e) {
            // 如果模板不存在，返回简单的响应
            return json([
                'framework' => 'ThinkPHP 8.0 Toolbox',
                'action' => $act,
                'data' => $data,
                'message' => '模板文件不存在，显示数据内容',
                'template_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 获取简化的首页 HTML（用于 Vercel 环境降级）
     */
    private function getSimpleHomePage()
    {
        return '页面被降级了！！！';
    }

    public function api()
    {
        // API 接口方法
        $action = input('action', '');
        $data = [];

        switch ($action) {
            case 'uuid':
                $number = input('number', 1);
                $letter = input('letter', 2);
                $data['uuid'] = uuid($number, $letter);
                break;
            case 'md5':
                $text = input('text', '');
                $data['md5'] = md5($text);
                break;
            default:
                $data['error'] = 'Invalid action';
                break;
        }

        return json($data);
    }
}
