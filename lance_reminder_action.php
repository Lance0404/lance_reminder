<?php
############################################
####  
############################################
global $ROOT_PATH;
include_once("../conf/ecm.php");
include("$ROOT_PATH/lib/sess_lib.php"); //共用函式庫
include_once("$ROOT_PATH/lib/db_lib.php"); //資料庫函式庫
include_once("lib/db_lib.php"); //公告資料庫操作函式庫
include_once("$ROOT_PATH/lib/common.php"); //共用函式庫
include_once("$ROOT_PATH/lib/ui_lib.php"); //樣版函式庫
include_once("$ROOT_PATH/lib/ssl_check.php"); 
include_once("$ROOT_PATH/lib/auth_lib.php"); 
$sess = new SESSION();
$sess->start_session();
$db = new LANCE_REMINDER_DBA($_SESSION['company_uuid']);

$mod_name = 'lance_reminder';
$mod_dir = 'lance_reminder';
$action = '';

if(isset($_GET['action'])) $action = $_GET['action'];

$moduleInfo = $db->getModuleInfo($mod_name,"lang_key");
$text = $db->loadLang($mod_name,$_SESSION['lang_type']);

switch($action){
    case "add_case":
        $data = array();
        $item_subject		= isset($_POST['item_subject'])?$_POST['item_subject']:'';
        $item_remark		= isset($_POST['item_remark'])?$_POST['item_remark']:'';
        $item_status		= isset($_POST['item_status'])?$_POST['item_status']:'';
        $notify				= isset($_POST['notify'])?$_POST['notify']:'';
        $notify_date	    = isset($_POST['notify_date'])?$_POST['notify_date']:'';
        $notify_hour        = isset($_POST['notify_hour'])?$_POST['notify_hour']:'';
        $notify_min         = isset($_POST['notify_min'])?$_POST['notify_min']:'';                
        // echo $notify_hour;
        // echo $notify_min;
        // exit;
        if(empty($item_subject)){
            $error_msg = '請填寫必填欄位*';
            echo $error_msg;
            return false;
            break;
        }

        // 前端一定會有值
        // if($notify==1){
        //     $error_msg = '日期或時間未填';
        //     echo $error_msg;
        //     return false;
        //     break;
        // }

        $data['user_guid']			=$_SESSION['login_guid'];
        $data['item_subject']		=$item_subject;	
        $data['item_remark']		=$item_remark;
        $data['item_status']		=$item_status;
        $data['notify']				=$notify;
        $data['notify_datetime']	=$notify_date." ".$notify_hour.":".$notify_min.":00";
        $data['create_time']		=date("Y-m-d H:i:s");

        // echo $data['notify_datetime'];
        // print_r($data);exit;
        $case_guid = $db->insertTableData('lance_remind_item',$data);

        $content_title=array('0'=>'一則新增');
        $content_list=array();
        foreach ($data as $key => $val) {
            $content_list[0][]=array(
                        'item'=>$key,
                        'data'=>$val
                );
        }

        $subject = '[lance提醒事項]: 您有一則新增';
        $content = '「lance提醒事項」';
        $content_link = "";
        // $content_title ='';
        // $content_list=$data;
        $from_user_info = $db->getUserInfo($_SESSION['login_guid'],'guid');
        $to_user_info = array('email' => 'lance.chang@hgiga.com' );
        $db->sendEmailNotify($db,$from_user_info,$to_user_info,$subject,$content_link,$content,$content_title,$content_list);

        if($case_guid != false){
         return json_encode(1);
        }else{
            return false;
        }


    break;
    
    case "update_button":
        $data = array();
        $id	= isset($_POST['reminder_guid'])?$_POST['reminder_guid']:'';
        $data['id']			=$id;

		$cond = array(  'id'     => $data['id'],
		    );
		$rs = $db->getTableData('lance_remind_item', $cond);        

		echo json_encode($rs{0});


    break;
    
    case "update_case":
        $data = array();
        $cond = array();
        $id 				= isset($_GET['guid'])?$_GET['guid']:'';
        $item_subject		= isset($_POST['item_subject'])?$_POST['item_subject']:'none';
        $item_remark		= isset($_POST['item_remark'])?$_POST['item_remark']:'';
        $item_status		= isset($_POST['item_status'])?$_POST['item_status']:'';
        $notify				= isset($_POST['notify'])?$_POST['notify']:'';
        $notify_date    = isset($_POST['notify_date'])?$_POST['notify_date']:'';
        // $notify_time     = isset($_POST['notify_time'])?$_POST['notify_time']:'';
        $notify_hour        = isset($_POST['notify_hour'])?$_POST['notify_hour']:'';
        $notify_min         = isset($_POST['notify_min'])?$_POST['notify_min']:'';   

        if(empty($item_subject)){
            $error_msg = '請填寫必填欄位*';
            echo $error_msg;
            break;
        }
        // if(($notify==1) && (($notify_date=='')||($notify_time==''))){
        //     echo $notify."<br>";
        //     echo $notify_date."<br>";
        //     echo $notify_time."<br>"    ;                        
        //     $error_msg = '日期或時間未填';
        //     echo $error_msg;
        //     return false;
        //     break;
        // }

        $cond['id']					=$id;

        $data['item_subject']		=$item_subject;	
        $data['item_remark']		=$item_remark;
        $data['item_status']		=$item_status;
        $data['notify']				=$notify;
        $data['notify_datetime']    =$notify_date." ".$notify_hour.":".$notify_min.":00";
        $data['modify_time']		=date("Y-m-d H:i:s");

		$rs = $db->updateTableData('lance_remind_item',$data,$cond);

		if ($rs){
			return json_encode(1);
		}else{
			return false;
		}

    break;

    case "delete_case":

        $data = array();
        $id	= isset($_POST['reminder_guid'])?$_POST['reminder_guid']:'';

		$cond = array(  'id'     => $id,
		    );
		$rs = $db->deleteTableData('lance_remind_item', $cond);        

		if ($rs){
			return json_encode(1);
		}else{
			return false;
		}

    break;

	default:
	exit;
}


?>
