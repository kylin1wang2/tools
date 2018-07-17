<?php

/**
 * 一些可能会用到的方法
 */
class Tools
{

	/**
	 * 判断用户是否在微信环境下
	 * Created by SublimeText3
	 * @Author   kylinwang@dashenw.com
	 * @DateTime 2018-07-17T23:11:00+0800
	 * @Version  [v1.0.0]
	 * @return   boolean                  [true-是，false-否]
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
	 * Created by SublimeText3
	 * @Author   kylinwang@dashenw.com
	 * @DateTime 2018-07-17T23:18:37+0800
	 * @Version  [v1.0.0]
	 * @return   [string]                   [版本号：如4.5.255]
	 */
	public function getWechatVersion()
	{
		preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $_SERVER['HTTP_USER_AGENT'], $matches);
    	echo '你的微信版本号为:'.$matches[2];
	}

	//todo 获取访问Android/iPhone？
	

	/**
	 * 手机号验证
	 * Created by SublimeText3
	 * @Author   kylinwang@dashenw.com
	 * @DateTime 2018-07-18T00:00:45+0800
	 * @Version  [v1.0.0]
	 * @return   boolean                  [true:格式正确，false:格式不对]
	 */
	public function isMobile($mobile)
	{
		if (!is_numeric($mobile)) {
			return false;
		}
		$pattern = '#^13[\d]{9}$|^14[5,6,7,8,9]{1}\d{8}$|^15[^4]{1}\d{8}$|^16[6]{1}\d{8}$|^17[^4,^9]{1}\d{8}$|^18[\d]{9}$|19[8,9]{1}\d{8}#';
		return preg_match($pattern, $mobile) ? true : false;
	}


	/**
	 * 身份证号验证
	 * Created by SublimeText3
	 * @Author   kylinwang@dashenw.com
	 * @DateTime 2018-07-18T00:04:03+0800
	 * @Version  [v1.0.0]
	 * @param    [string]                   $id_card [身份证号码]
	 * @return   [boolean]                            [true:身份证号码有效，false:无效]
	 */
	public function validation_filter_id_card($id_card)
	{
		if (strlen($id_card) == 18) {
			return $this->idcard_checksum18($id_card);
		} elseif ((strlen($id_card) == 15)) {
			$id_card = $this->idcard_15to18($id_card);
			return $this->idcard_checksum18($id_card);
		} else {
			return false;
		}
	}


	/**
	 * 计算身份证校验码，根据国家标准GB 11643-1999
	 * @param $idcard_base
	 * @return bool|mixed
	 */
	private function idcard_verify_number($idcard_base)
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
	private function idcard_15to18($idcard)
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
		$idcard = $idcard . $this->idcard_verify_number($idcard);
		return $idcard;
	}

	/**
	 * 18位身份证校验码有效性检查
	 * @param $idcard
	 * @return bool
	 */
	private function idcard_checksum18($idcard)
	{
		if (strlen($idcard) != 18) {
			return false;
		}
		$idcard_base = substr($idcard, 0, 17);
		if ($this->idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
			return false;
		} else {
			return true;
		}
	}
}