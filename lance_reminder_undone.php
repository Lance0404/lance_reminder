<?php
## print to tpl
$titleBar += array("2" => array("title" => $text['lance_reminder_undone']));


if (count($_POST) > 0)
{
    $_GET = $_POST;
}
// print_r($_GET);
$get = is_sset($_GET);
unset($get["action"]);
unset($get["_"]);
unset($get["cur_page"]);


$search_array = array();
$page_limit = 10;
$page_level = (isset($get['page_level']) && $get['page_level'])? $get['page_level'] :1;
$page_total_mum = 1;
$order_name = (isset($get['order_name']) && $get['order_name'])? $get['order_name'] :'create_time';
$order = (isset($get['order']) && $get['order'])? $get['order'] :'asc';
$keyword_tital = (isset($get['keyword_tital']) && $get['keyword_tital'] != "")? $get['keyword_tital'] :'';
$keyword_subject = (isset($get['keyword_subject']) && $get['keyword_subject'] != "")? $get['keyword_subject'] :'';
$keyword_remark = (isset($get['keyword_remark']) && $get['keyword_remark'] != "")? $get['keyword_remark'] :'';
// $item_status =(isset($get['item_status']) && $get['item_status'] != "")? $get['item_status'] :'';
// $item_notify =(isset($get['item_notify']) && $get['item_notify'] != "")? $get['item_notify'] :'';

$data = array(
	'keyword_tital'			=> $keyword_tital,
	'keyword_subject'		=> $keyword_subject,
	'keyword_remark'		=> $keyword_remark,
    "order_name"            => $order_name,
    "order"                 => $order,
);


$search_array []= array(
    'key' => 'user_guid',
    'val' => array($_SESSION['login_guid']),
    'cond'=> 'in'
);
$search_array []= array(
    'key' => 'is_delete',
    'val' => 0,
    'cond'=> '='
);
$search_array []= array(
    'key' => 'item_status',
    'val' => 0,
    'cond'=> '='
);
// echo "<pre>";print_r($search_array);
$tplObj->assignTplArray($data);


//titlebar設定
// $titleBar[3] = array("title" => $text[$action]);
$barSetting = array(
    "search"=>true,//是否需要搜尋列
    "searchAdminLinkMemo" =>false,
    "searchAdminLink"=>false,//進階搜尋列網址，有設定網址自動啟用進階搜尋列
    "changeOrderBtn"=>false,//是否需要排序按鈕
    "addBtn"=>true,//是否需要新增按鈕
    "orderBtn"=>true,//是否需要排序按鈕
    "conditionBtn"=>false,//是否需要狀態按鈕
    "menuBtn"=>true,//是否需要選單按鈕
    "uploadBtn"=>false,//是否需要上傳按鈕
    "downloadBtn"=>false,//是否需要下載按鈕
    "printBtn"=>false,//是否需要列印按鈕
    "delBtn"=>false,//是否需要刪除按鈕
);
//排序
$text += array(
    'lance_reminder_create_time_asc'     => "依建立時間排序A-Z",
    'lance_reminder_create_time_desc'    => "依建立時間排序Z-A",
);
$order_by_arr=array('create_time');
$sort_type_arr=array('asc','desc');
foreach ($order_by_arr as $order_by)
{
    foreach ($sort_type_arr as $sort_type)
    {
        $orderSetting[] = array(
            'class'         => ($order_name==$order_by && $order==$sort_type)?'active':'',
            'orderName'     => $order_by,
            'orderType'     => $sort_type,
            'orderShowname' => $text['lance_reminder_'.$order_by.'_'.$sort_type],
        );
    }
}
treeSetTitleBar($barSetting,$tplObj,$orderSetting);

$order_data = array(
	"key" => $order_name,
	"type" => $order,
);
// print_r($order_data);
if(($keyword_subject!='')&&($keyword_remark!='')){
    $cond = $search_array;
    $key_word_data = array(
			'set'=>	array(
    				'0'=>array(
    						'key'		=>'item_subject',
    						'val'		=>"%$keyword_subject%"
		   			),
    				'1'=>array(
    						'key'		=>'item_remark',
    						'val'		=>"%$keyword_remark%"
    				)
				)
    	);
    $data = $db->getSelect('lance_remind_item',$cond,$key_word_data,$order_data,$page_limit,$page_level);
	$data_count = count($db->getSelect('lance_remind_item',$cond ,$key_word_data));
}
else if($keyword_tital != ""){
    $cond = $search_array;
    $key_word_data = array('key_word' => "%$keyword_tital%" ,'key' => array('item_subject','item_remark'));
    $data = $db->getSelect('lance_remind_item',$cond,$key_word_data,$order_data,$page_limit,$page_level);
	$data_count = count($db->getSelect('lance_remind_item',$cond ,$key_word_data));
}
else if($keyword_subject!=''){
    $cond = $search_array;
    $key_word_data = array('key_word' => "%$keyword_subject%" ,'key' => array('item_subject'));
    $data = $db->getSelect('lance_remind_item',$cond,$key_word_data,$order_data,$page_limit,$page_level);
	$data_count = count($db->getSelect('lance_remind_item',$cond ,$key_word_data));
}
else if($keyword_remark!=''){
    $cond = $search_array;
    $key_word_data = array('key_word' => "%$keyword_remark%" ,'key' => array('item_remark'));
    $data = $db->getSelect('lance_remind_item',$cond,$key_word_data,$order_data,$page_limit,$page_level);
	$data_count = count($db->getSelect('lance_remind_item',$cond ,$key_word_data));
}
else{
    $cond =  $search_array;	
    $data = $db->getSelect('lance_remind_item',$cond, '' ,$order_data, $page_limit,$page_level);
	$data_count = count($db->getSelect('lance_remind_item',$cond));
	// echo ('debug2');
}
// print_r($data);
if($data_count)
{
    $page_total_mum =floor(($data_count-1)/$page_limit) + 1;
}
$tplObj->createPageBar($page_level,$page_limit,$data_count,$text,3);
$tplObj->assignTplArray(array(
    // 'keyword' => $key_word,//關鍵字
    'index' => $action,
    'total_num' => $data_count,
));

if($data)
{
    foreach($data as $key => $val)
    {
        //$user = $db->getUserInfo($val['modify_by'],'guid');
        $tplObj->newTplBlock("LIST");
        $tplObj->assignTplArray(array(
        	'reminder_guid'		=> $val['id'],
        	'item_subject' 		=> $val['item_subject'],
        	'item_remark' 		=> $val['item_remark'],
        	'notify' 			=> $val['notify'],
            'notify_datetime'   => $val['notify_datetime'],
            'create_time'       => $val['create_time'],
        ));
		if ($val['notify']==1){
			$tplObj->assignTplOne('notify', 'yes');
		}else{
			$tplObj->assignTplOne('notify', 'no');
		}
    }
}

$hourArr=array();
for ($i=0; $i <=23 ; $i++) { 
    $hourArr[]=sprintf("%02d", $i);
}

$minArr=array();
for ($i=0; $i <12 ; $i++) { 
    $minArr[]=sprintf("%02d", $i*5);
}

foreach ($hourArr as $hour_val) {
    $tplObj->newTplBlock("HOUR");
    $tplObj->assignTplOne('hour_val', $hour_val);
}

foreach ($minArr as $min_val) {
    $tplObj->newTplBlock("MIN");
    $tplObj->assignTplOne('min_val', $min_val);
}

?>