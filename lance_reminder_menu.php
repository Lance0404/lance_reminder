<?php
//2016-12-23
//lance
//設定模組功能的目錄

include_once "../conf/ecm.php";
include "$ROOT_PATH/lib/sess_lib.php"; //共用函式庫
include_once "$ROOT_PATH/lib/db_lib.php"; //資料庫函式庫
include_once "lib/db_lib.php"; //資料庫操作函式庫
include_once "$ROOT_PATH/lib/common.php"; //共用函式庫
include_once "$ROOT_PATH/lib/ui_lib.php"; //樣版函式庫
// include_once "$ROOT_PATH/lib/ssl_check.php";
// include_once "$ROOT_PATH/lib/auth_lib.php";

$sess = new SESSION();
$sess->start_session();
$db = new LANCE_REMINDER_DBA($_SESSION['company_uuid']);

$tplSettings = array("mainPath"    => "html/submenu_list.tpl");
$tplObj = new CC_VIEW($tplSettings,$_SESSION['company_uuid']);

$rs_function[] = array('lang_key' => 'lance_reminder_undone','name' => '未完成事項');
$rs_function[] = array('lang_key' => 'lance_reminder_done','name' => '已完成事項');

$action = (isset($_GET['action']))? $action = $_GET['action']:'lance_reminder_undone';
$num  = 0;

foreach($rs_function as $rs_function_key => $rs_function_val){

    if($rs_function_val['lang_key'] == 'lance_reminder_undone'){
        $is_true = $db->checkFuncAuth($_SESSION['login_guid'],'lance_reminder_undone');
        if(!$is_true){
            continue;
        }
    }

    if($rs_function_val['lang_key'] == 'lance_reminder_done'){
        $is_true = $db->checkFuncAuth($_SESSION['login_guid'],'lance_reminder_done');
        if(!$is_true){
            continue;
        }
    }

    $subModules[$num] = array(
        "name"          =>   $rs_function_val['lang_key'],
        "child_num"     =>  "0",
        "link"          =>   $rs_function_val['lang_key'],
        "display_name"  =>   $rs_function_val['name'],
        "display_order" =>  "$num",
        "active"        =>  ($action == $rs_function_val['lang_key'])?"active":"",
    );
    $num ++;
}

$tplObj->setNewFuncTpl($subModules,'oaks_new_layout_menubox',3);
echo $tplObj->outputTpl();

