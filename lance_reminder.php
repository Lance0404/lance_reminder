    <?php
############################################
####  
############################################
global $ROOT_PATH;
include_once("../conf/ecm.php"); //組態設定
include("$ROOT_PATH/lib/sess_lib.php"); //Session函式庫
include_once("$ROOT_PATH/lib/db_lib.php"); //DB共用函式庫
include_once("$ROOT_PATH/lib/common.php"); //共用函式庫
include_once("$ROOT_PATH/lib/ui_lib.php"); //UI共用函式庫
include_once("$ROOT_PATH/lib/auth_lib.php"); //授權檢查共用函式庫
include_once("lib/db_lib.php"); //公告資料庫操作函式庫

$sess = new SESSION();
$sess->start_session();
$db = new LANCE_REMINDER_DBA($_SESSION['company_uuid']);

// echo "<pre>";print_r($_SESSION);
$mod_name = 'lance_reminder';
$mod_dir = 'lance_reminder';

$action = (isset($_GET['action']))? $action = $_GET['action']:'lance_reminder_undone';

$moduleInfo = $db->getModuleInfo($mod_name,"lang_key");
$text = $db->loadLang($mod_name,$_SESSION['lang_type']);

// setup action.tpl
$tpl_path = "$mod_dir/template/".$action.".tpl";
$tplSettings = array(
	"mainPath" => $tpl_path,
	"includeTpls" => array(
		"lance_reminder_title_bar" => "lance_reminder/template/lance_reminder_title_bar.tpl",
	    ),  
	// "ui_version"    => "3"
);
// echo "<pre>"; print_r($_SESSION); echo "</pre>";
// echo "<pre>"; print_r($tplSettings); echo "</pre>";
$tplObj = new CC_VIEW($tplSettings,$_SESSION['company_uuid']);

$params = array(
    'TXT_INDEX_TITLE' => $text['txt_index_title'],
    'MODULE_NAME'     => $mod_name,
    'MODULE'          => $text[$mod_name],
    'MODULEPATH'      => "$mod_dir/$mod_name.php"
);
$tplObj->assignTplArray($params);
$tplObj->assignTplOne('NOW_ACTION',$action);


// setup title bar
$titleBar = array(
        "0" => array("title" => $text['txt_index_title'],"url" => 'cc_index.php'),
        "1" => array("title" => $text[$mod_name],"url" => 'module_page.php?module='.$mod_name),
);

switch($action){
    #####################################################################
    #   未完成事項列表
    #####################################################################
    case "lance_reminder_undone":
        include_once("lance_reminder_undone.php");
    break;
    #####################################################################
    #   已完成事項列表
    #####################################################################    
    case "lance_reminder_done":
        include_once("lance_reminder_done.php");
    break;
    default:
    break;
}

$tplObj->createTitleBar($titleBar,3);
$tplObj->printTpl();


function treeSetTitleBar($settingArr = array(),$tpl,$orderSettingArr = array(),$conditionArr = array(),$classArr = array()){
    $db = new LANCE_REMINDER_DBA($_SESSION['company_uuid']);
    $barSetting = array(
        "search"=>false,//是否需要搜尋列
        "searchAdminLink"=>'',//是否需要進階搜尋頁
        "searchAdminLinkMemo"=>'',//是否需要進階搜尋頁提示字元
        "addBtn_changeform"=>'',//是否變成跳頁紐
        "addBtn"=>false,//是否需要新增按鈕
        "changeOrderBtn"=>false,//是否需要修改排序按鈕
        "orderBtn"=>false,//是否需要排序按鈕
        "conditionBtn"=>false,//是否需要狀態按鈕
        "classBtn"=>false,//是否需要類別按鈕
        "menuBtn"=>true,//是否需要選單按鈕
        "uploadBtn"=>false,//是否需要上傳按鈕
        "downloadBtn"=>false,//是否需要下載按鈕
        "printBtn"=>false,//是否需要列印按鈕
        "delBtn"=>true,//是否需要刪除按鈕
    );
    $orderList = array();
    if(count($settingArr) > 0){
        foreach($settingArr as $key => $val){
            if(isset($barSetting[$key])){
                $barSetting[$key] = $val;
            }
        }
    }
    // echo "<pre>";print_r($barSetting);echo "</pre>";
    // $tpl->newTplBlock('tree_title_bar');
    foreach($barSetting as $key => $val){
        $show = "";
        if(!$val){
            $show = "display:none";
        }
        $tpl->assignTplOne('show_'.$key,$show);
    }
    //設定進階搜尋列提示字元
    if($barSetting['searchAdminLinkMemo'] != ''){
        $tpl->assignTplOne('placeholder',$barSetting['searchAdminLinkMemo']);
    }
    //設定新增按鈕是否變成跳頁
    if($barSetting['addBtn_changeform'] != ''){
        $tpl->assignTplOne('changeform',$barSetting['addBtn_changeform']);
        $tpl->assignTplOne('data_target',"");
    }
    else
        $tpl->assignTplOne('data_target',"#myModal-new-1");
    //設定進階搜尋列
    if($barSetting['searchAdminLink'] != ''){
        $tpl->assignTplOne('show_search_admin','');
        $tpl->assignTplOne('search_admin_link',$barSetting['searchAdminLink']);
    }else{
        $tpl->assignTplOne('show_search_admin','display:none');
    }
    //設定排序按鈕
    if(count($orderSettingArr) > 0){
        foreach($orderSettingArr as $key => $val){
            $orderList[$key] = $val;
        }
        $tpl->gotoTplBlock();
        foreach($orderList as $key => $val){
            $tpl->newTplBlock('order_list');
            $tpl->assignTplOne('orderName',$val['orderName']);
            $tpl->assignTplOne('orderType',$val['orderType']);
            $tpl->assignTplOne('orderShowname',$val['orderShowname']);
            $tpl->assignTplOne('class',$val['class']);
            $tpl->assignTplOne('checked',(($val['class'] == 'active')?'::before':''));
        }
    }
    //設定條件按鈕
    if(count($conditionArr) > 0){
        $tpl->gotoTplBlock();
        foreach($conditionArr as $key => $val){
            $tpl->newTplBlock('condition_list');
            $ou_name = $db->getDeptName($val);
            $tpl->assignTplOne('ou_id',$val);
            $tpl->assignTplOne('ou_name',$ou_name);
        }
    }
    //設定類別按鈕
    if(count($classArr) > 0){
        $tpl->gotoTplBlock();
        foreach($classArr as $key => $val){
            $tpl->newTplBlock('class_list');
            $tpl->assignTplOne('id',$val['id']);
            $tpl->assignTplOne('name',$val['name']);
        }
    }
    $tpl->gotoTplBlock();
}
?>
