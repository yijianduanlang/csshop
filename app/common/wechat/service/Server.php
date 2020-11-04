<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息服务端服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\common\wechat\service;

use think\facade\Cache;

class Server extends CareyShop
{
    /**
     * 接收并响应微信推送
     * @access public
     * @return void
     * @throws
     */
    public function putWechatData()
    {
        // 获取消息
        $message = $this->getApp('server')->getMessage();
        if (isset($message['MsgType'])) {
            switch ($message['MsgType']) {
                // 事件消息
                case 'event':
                    $this->handleEvent();
                    break;

                // 文字消息
                case 'text':
                    $this->handleText();
                    break;

                // 图片消息
                case 'image':
                    $this->handleImage();
                    break;

                // 语音消息
                case 'voice':
                    $this->handleVoice();
                    break;

                // 视频消息
                case 'video':
                    $this->handleVideo();
                    break;

                // 坐标消息
                case 'location':
                    $this->handleLocation();
                    break;

                // 链接消息
                case 'link':
                    $this->handleLink();
                    break;

                // 文件消息
                case 'file':
                    $this->handleFile();
                    break;

                // 其它消息
                default:
                    $this->handleOther($message['MsgType']);
            }
        }

        // 响应实际输出
        $this->getApp('server')->serve()->send();
        exit();
    }

    /**
     * 处理事件消息
     * @access private
     * @return void
     * @throws
     */
    private function handleEvent()
    {
        $this->getApp('server')->push(function ($res) {
            // 订阅
            if ($res['Event'] == 'subscribe') {
                $cacheKey = User::WECHAT_USER . $this->params['code'];
                $userList = Cache::get($cacheKey, []);

                array_unshift($userList, $res['FromUserName']);
                Cache::set($cacheKey, $userList);

                // 订阅回复
                return $this->getSubscribeReply();
            }

            // 取消订阅
            if ($res['Event'] == 'unsubscribe') {
                $cacheKey = User::WECHAT_USER . $this->params['code'];
                $userList = Cache::get($cacheKey, []);
                $key = array_search($res['FromUserName'], $userList);

                if (false !== $key) {
                    unset($userList[$key]);
                    Cache::set($cacheKey, $userList);
                }
            }

            return null;
        });
    }

    /**
     * 处理文字消息
     * @access private
     * @return void
     */
    private function handleText()
    {
    }

    /**
     * 处理图片消息
     * @access private
     * @return void
     */
    private function handleImage()
    {
    }

    /**
     * 处理语音消息
     * @access private
     * @return void
     */
    private function handleVoice()
    {
    }

    /**
     * 处理视频消息
     * @access private
     * @return void
     */
    private function handleVideo()
    {
    }

    /**
     * 处理坐标消息
     * @access private
     * @return void
     */
    private function handleLocation()
    {
    }

    /**
     * 处理链接消息
     * @access private
     * @return void
     */
    private function handleLink()
    {
    }

    /**
     * 处理文件消息
     * @access private
     * @return void
     */
    private function handleFile()
    {
    }

    /**
     * 处理其它消息
     * @access private
     * @param string $type 消息类型
     * @return void
     */
    private function handleOther(string $type)
    {
    }

    /**
     * 长链接转短链接
     * @access public
     * @return array|false
     * @throws
     */
    public function setWechatShort()
    {
        $result = $this->getApp('url')->shorten($this->params['url']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['short_url'];
    }

    /**
     * 获取微信服务器IP(或IP段)
     * @access public
     * @return array|false
     * @throws
     */
    public function getWechatIP()
    {
        $result = $this->getApp('base')->getValidIps();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['ip_list'];
    }

    /**
     * 清理接口调用次数(每月10次)
     * @access public
     * @return bool
     * @throws
     */
    public function clearWechatQuota()
    {
        $result = $this->getApp('base')->clearQuota();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    public function getSubscribeReply()
    {
    }
}
