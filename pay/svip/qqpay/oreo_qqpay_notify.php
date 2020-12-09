<?php 
require_once('../../../oreo/Oreo.Cron.php');
require_once(SYSTEM_ROOT."oreo_function/pay/svip/oreo_qqpay.php");
require_once(SYSTEM_ROOT."oreo_function/pay/svip/svip_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	//商户订单号
	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];

    if ($_GET['trade_status'] == 'TRADE_SUCCESS' || $_GET['trade_status'] == 'TRADE_FINISHED') {
		//付款完成后，支付宝系统发送该交易状态通知
	  $srow=$DB->query("SELECT * FROM oreo_order WHERE trade_no='{$out_trade_no}' limit 1")->fetch();
	  $user=$DB->query("select * from oreo_user where id='{$srow['pid']}' limit 1")->fetch();
	  if($srow['status']==0 && $conf['chaojivip']==1&&$conf['ssvip_zt']==1&&$conf['ssvip_zdy']==1&&$user['ssvip']==1&&$conf['ssvip_qq']!=777){
			$DB->query("update `oreo_order` set `status` ='1',`endtime` ='$date' where `trade_no`='$out_trade_no'");
			$addmoney=round($srow['money']*$conf['ssvip_qq']/100,2);
			$DB->query("update oreo_user set money=money+{$addmoney} where id='{$srow['pid']}'");
			$url=creat_callback($srow);
			curl_get($url['notify']);
			proxy_get($url['notify']);
			echo "success";
		}
      if($srow['status']==0 && $conf['sw_money_rate']==0 && $user['zdyfl']==0 && $conf['ssvip_qq']==777 || $conf['ssvip_zdy']==0){
			$DB->query("update `oreo_order` set `status` ='1',`endtime` ='$date' where `trade_no`='$out_trade_no'");
			$addmoney=round($srow['money']*$conf['money_rate']/100,2);
			$DB->query("update oreo_user set money=money+{$addmoney} where id='{$srow['pid']}'");
			$url=creat_callback($srow);
			curl_get($url['notify']);
			proxy_get($url['notify']);
			echo "success";
		}
	  if($srow['status']==0 && $conf['sw_money_rate']==0 && $user['zdyfl']==1 && $conf['ssvip_qq']==777 || $conf['ssvip_zdy']==0){
			$DB->query("update `oreo_order` set `status` ='1',`endtime` ='$date' where `trade_no`='$out_trade_no'");
			$addmoney=round($srow['money']*$user['rate']/100,2);
			$DB->query("update oreo_user set money=money+{$addmoney} where id='{$srow['pid']}'");
			$url=creat_callback($srow);
			curl_get($url['notify']);
			proxy_get($url['notify']);
			echo "success";
		}
	  if($srow['status']==0 && $conf['sw_money_rate']==1 && $user['zdyfl']==0 && $conf['ssvip_qq']==777 || $conf['ssvip_zdy']==0){
			$DB->query("update `oreo_order` set `status` ='1',`endtime` ='$date' where `trade_no`='$out_trade_no'");
			$addmoney=round($srow['money']*$conf['qq_rate']/100,2);
			$DB->query("update oreo_user set money=money+{$addmoney} where id='{$srow['pid']}'");
			$url=creat_callback($srow);
			curl_get($url['notify']);
			proxy_get($url['notify']);
			echo "success";
		}	
	   if($srow['status']==0 && $conf['sw_money_rate']==1 && $user['zdyfl']==1 && $conf['ssvip_qq']==777 || $conf['ssvip_zdy']==0){
			$DB->query("update `oreo_order` set `status` ='1',`endtime` ='$date' where `trade_no`='$out_trade_no'");
			$addmoney=round($srow['money']*$user['sqq_rate']/100,2);
			$DB->query("update oreo_user set money=money+{$addmoney} where id='{$srow['pid']}'");
			$url=creat_callback($srow);
			curl_get($url['notify']);
			proxy_get($url['notify']);
			echo "success";
		}
 }
	echo "success";
}
else {
    //验证失败
    echo "fail";
}

?>