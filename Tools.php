<?php

/**
 * 一些可能会用到的方法
 *
 * Class Tools
 */
class Tools
{

    /**
     * 判断用户是否在微信环境下
     * @return bool
     */
    public function isWechatEnv()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * 获取微信浏览器版本号
     * @return string
     */
    public function getWechatVersion()
    {
        preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $_SERVER['HTTP_USER_AGENT'], $matches);
        return 'Wechat Version :'.$matches[2];
    }

    /**
     * 根据UA判断用户所用设备
     * @return string
     */
    public function getDeviceType()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']); //全部小写
        if(strpos($ua,'iphone') || strpos($ua,'ipad')){
            return 'iOS';
        }elseif (strpos($ua,'android')){
            return 'Android';
        }else{
            return 'Others';
        }
    }


	/**
	 * 手机号码验证、运营商检测
	 * 移动号段：134,135,136,137,138,139,147,150,151,152,157,158,178,182,183,184,187,188
	 * 联通号段：130,131,132,155,156,176,185,186
	 * 电信号段：133,153,173,177,180,181,189,199
	 * 运营商号段更新时间：2018-9-18
	 *
	 * @param $mobile
	 * @return bool|string
	 */
    public function isMobile($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        //正则匹配
		$mobilePattern = '#^13[4,5,6,7,8,9]{1}\d{8}$|^14[7]{1}\d{8}$|^15[0,1,2,7,8]{1}\d{8}$|^17[8]{1}\d{8}$|^18[2,3,4,7,8]{1}\d{8}$|^19[8]{1}\d{8}#';
		$unicomPattern = '#^13[0,1,2]{1}\d{8}$|^15[5,6]{1}\d{8}$|^17[6]{1}\d{8}$|^18[5,6]{1}\d{8}#';
		$telecomPattern = '#^13[3]{1}\d{8}$|^15[3]{1}\d{8}$|^17[3,7]{1}\d{8}$|^18[0,1,9]{1}\d{8}$|^19[9]{1}\d{8}#';
        $pattern = '#^13[\d]{9}$|^14[7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[3,6,7,8]{1}\d{8}$|^18[\d]{9}$|19[8,9]{1}\d{8}#';

        if(preg_match($pattern,$mobile)){
			if(preg_match($mobilePattern,$mobile)){
				return 'China Mobile';
			}elseif (preg_match($unicomPattern,$mobile)){
				return 'China Unicom';
			}elseif (preg_match($telecomPattern,$mobile)){
				return 'China Telecom';
			}else{
				return 'Unknown T-MOBILE';
			}
		}else{
        	return false;
		}
    }


    /**
     * 身份证号验证
     * @param $id_card
     * @return bool
     */
    public function validationFilterIdCard($id_card)
    {
        if (strlen($id_card) == 18) {
            return $this->idcardChecksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = $this->idcard15to18($id_card);
            return $this->idcardChecksum18($id_card);
        } else {
            return false;
        }
    }


    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     * @param $idcard_base
     * @return bool|mixed
     */
    private function idcardVerifyNumber($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }


    /**
     * 将15位身份证升级到18位
     * @param $idcard
     * @return bool|string
     */
    private function idcard15to18($idcard)
    {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . $this->idcardVerifyNumber($idcard);
        return $idcard;
    }

    /**
     * 18位身份证校验码有效性检查
     * @param $idcard
     * @return bool
     */
    private function idcardChecksum18($idcard)
    {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if ($this->idcardVerifyNumber($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }
}