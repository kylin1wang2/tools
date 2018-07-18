<?php

/**
 * 方法调用示例
 *
 * Class Example
 */
class Example extends Tools
{

    /**
     * 微信环境测试
     */
    public function test1()
    {
        $result = $this->isWechatEnv();
        print_r($result);
    }

    /**
     * 获取微信内置浏览器版本号
     */
    public function test2()
    {
        $result = $this->getWechatVersion();
        print_r($result);
    }

    /**
     * 获取用户访问页面所用的手机设备类型
     */
    public function test3()
    {
        $result = $this->getDeviceType();
        print_r($result);
    }

    /**
     * 手机号码合法性验证
     */
    public function test4()
    {
        $phone1 = '13612345678';
        $phone2 = '19900000000';
        $result1 = $this->isMobile($phone1);
        $result2 = $this->isMobile($phone2);
        print_r($result1);
        print_r($result2);
    }

    /**
     * 身份证号合法性验证
     */
    public function test5()
    {
        $id_card_example = '542500198802109261'; //号码来自于网络
        $result = $this->validationFilterIdCard($id_card_example);
        print_r($result);
    }

}