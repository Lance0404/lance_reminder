<?php
############################################
####  
############################################
global $ROOT_PATH;
$ROOT_PATH="/usr/local/OAKlouds";
include_once("$ROOT_PATH/conf/ecm.php"); //組態設定
// include("$ROOT_PATH/lib/sess_lib.php"); //Session函式庫
include_once("$ROOT_PATH/lib/db_lib.php"); //DB共用函式庫
// include_once("$ROOT_PATH/lib/common.php"); //共用函式庫
// include_once("$ROOT_PATH/lib/ui_lib.php"); //UI共用函式庫
// include_once("$ROOT_PATH/lib/auth_lib.php"); //授權檢查共用函式庫
include_once("$ROOT_PATH/lance_reminder/lib/db_lib.php"); //公告資料庫操作函式庫

// $sess = new SESSION();
// $sess->start_session();
$company_guid ='1';
$login_name='2'; 
$db = new LANCE_REMINDER_DBA($company_guid);

$nowDateTime=date("Y-m-d H:i:s");

$cond []= array(
    'key' => 'notify',
    'val' => 1,
    'cond'=> '='
);
$cond []= array(
    'key' => 'item_status',
    'val' => 0,
    'cond'=> '='
);
$cond []= array(
    'key' => 'notify_datetime',
    'val' => "$nowDateTime + INTERVAL 1 MINUTE",
    'cond'=> '>='
);

$rs = $db->getSelect('lance_remind_item',$cond);

$tableHeader=array();
$tableHeaderText=array('id','user_guid','item_subject','item_remark','item_status','notify','notify_datetime','is_delete','create_time','modify_time');

for ($i=0; $i <count($tableHeaderText); $i++) { 
	$tableHeader[0][$i]=$tableHeaderText[$i];	
}

$content_title=array('0'=>'未完成事項列表');
$content_list=array();
$subject = '[lance提醒事項]: 您的未完成事項列表';
$content = '「lance提醒事項」';
// echo $_SERVER['SERVER_NAME'];
$content_link = "http://10.4.1.51/module_page.php?module=lance_reminder";
$from_user_info = $db->getUserInfo($login_name,'guid');

// $i=1;
// $guid_count=array();
// $guid_count=0;
// $guidCount;
foreach ($rs as $key => $infoArr) {
	$guid=$infoArr['user_guid'];

	if(!isset($guid_count[$guid])){
		$guid_count[$guid]=1;
	};
    foreach ($infoArr as $key => $eachVal) {
    	$dataByGuidArr[$guid][$guid_count[$guid]][]=$eachVal;
    }
	// echo $guid." ".$guid_count[$guid]."<br>";
	$guid_count[$guid]++;

}
foreach ($dataByGuidArr as $guid => $tableArr) {
	// echo $guid."<br>";
	// echo "<pre>"; print_r($tableArr); echo "</pre>";

	$to_user_info = $db->getUserInfo($guid,'guid');
	// echo "<pre>"; print_r($to_user_info); echo "</pre>";
	$table=array_merge($tableHeader, $tableArr);
	$sendEmailNotifyCheck=$db->sendEmailNotify($db,$from_user_info,$to_user_info,$subject,$content_link,$content,$content_title,$content_list,$table);
	if (!$sendEmailNotifyCheck) {
		// error_log("Notify email failed sending!", 1, 'lance.chang@hgiga.com', 'Failed to send email!');
		error_log("[".date("Y-m-d H:i:s")."]Failed to send notify email!\n", 3, "$ROOT_PATH/lance_reminder/cron/lance_remind_notify.log");
	}

}

?>