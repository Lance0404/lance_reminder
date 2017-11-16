<?php
global $ROOT_PATH;
include_once('/usr/local/OAKlouds/lib/db_lib.php');
include_once("$ROOT_PATH/lib/msg_lib.php"); //mail函式庫
class LANCE_REMINDER_DBA EXTENDS CC_DBA{
    function __construct($company_uuid)    {
        parent::__construct($company_uuid);
    }

    public function deleteTableData($table,$cond){

        $tb = $this->table($table, $this->company_db);
        $fix = " 1=1";
        if(is_array($cond)){
            foreach($cond as $key => $val){
                $fix .= "  AND  ". $this->tableField($key). "  =  ". $this->escapeString($val);
            }
        }else{
            echo '$cond  不是array';
            return false;
        }
        $stmt = "delete from $tb WHERE $fix";

        // return $stmt;
        if ($this->db_link->Execute($stmt)) {
            return true;
        } else {
            return false;
        }
    }
    public function sortTableData($table,$cond){
        
        $field = '*';
        $where = '';
        $where_cond = '';

        $tb = $this->table($table,$this->company_db);

        if(is_array($cond)){
            $cond_word = array();
            foreach ($cond as $field_key => $field_val) {
                if($field_key != 'in'){
                    $cond_word []= $this->tableField($field_key) .'='.$this->escapeString($field_val);
                }else{
                    //in條件式
                    foreach ($field_val as $in_key => $in_val) {
                        $cond_word []= $this->tableField($in_key) .'in (\''.$in_val.'\') ';
                    }
                }
            }
            $where_cond = implode(' AND ',$cond_word);
        }
    }

    public function insertTableData($table,$data){

        if(!is_array($data) || empty($data)){
            return false;
        }

        $tb = $this->table($table,$this->company_db);
        $col = array();
        $val = array();
        foreach($data as $key => $value){
            $col[] = $this->tableField($key);
            $val[] = $this->escapeString($value);
        }
        $stmt = "insert into $tb (".implode(",",$col).") values (".implode(",",$val).")";
        
        if($this->db_link->Execute($stmt)){
            return $this->db_link->_insertid();
        }else{
            //echo $this->db_link->ErrorMsg();
            return false;
        }

    }

    public function getTableData($table,$cond = null,$select = null,$key_word_data = null,$orderArr = null){
        // echo "db_lib_debug!";
        $count = 0;

        $field = '*';
        $where = '';
        $where_cond = '';
        $tb = $this->table($table,$this->company_db);

        if(is_array($cond)){
            $cond_word = array();
            foreach ($cond as $field_key => $field_val) {
                if($field_key != 'in'){
                    $cond_word []= $this->tableField($field_key) .'='.$this->escapeString($field_val);
                }else{
                    //in條件式
                    foreach ($field_val as $in_key => $in_val) {
                        $cond_word []= $this->tableField($in_key) .'in (\''.$in_val.'\') ';
                    }
                }
            }
            $where_cond = implode(' AND ',$cond_word);
        }

        if(is_array($orderArr)){
            foreach ($orderArr as $colName => $sortOrder) {
                $order_word[]= $colName.' '.$sortOrder;       
            }
            $order_cond = implode(', ',$order_word);            
        }
        $order=(isset($order_cond))?' ORDER BY '.$order_cond:'';

        if(is_array($key_word_data)){
            if(isset($key_word_data['key_word']) && $key_word_data['key_word']){
                $key_word =  $this->escapeString($key_word_data['key_word']);
                if( $key_word_data['key_word'] && $key_word_data['key'][0] ){
                    if(!empty($where_cond)){
                        $where_cond .= ' AND';
                    }
                    $where_cond .= ' ( ';
                    $i = 1 ;
                    foreach($key_word_data['key'] as $key => $val){
                        if($i != 1){
                            $where_cond .= ' OR ';
                        }
                        $where_cond .=  $this->tableField($val) ." like ". $key_word;
                        $i++;
                    }
                    $where_cond .= ')';
                }
            }
        }

        if(!empty($where_cond)){
            $where = "WHERE $where_cond";
        }

        if(is_array($select)){
            $select_word = array();
            foreach ($select as $value) {
                $select_word []= $this->tableField($value);
            }
            $select_cond = implode(',',$select_word);

            $field = $select_cond;
        }

        $stmt = "SELECT $field FROM $tb $where $order";
        // return $stmt;
        $rs = $this->db_link->GetArray($stmt);

        if($rs != false ){
            return $rs;
        }else{
            return false;
        }
    }

    public function updateTableData($table,$data = null,$cond = null){

        $where = '';
        $tb = $this->table($table,$this->company_db);

        if(is_array($data)){
            $data_array = array();
            foreach ($data as $field_key => $field_val) {
                $data_array []= $this->tableField($field_key).'='.$this->escapeString($field_val);
            }
            $update_data = implode(' , ',$data_array);
        }else{
            return false;
        }

        if(is_array($cond)){
            $cond_word = array();
            foreach ($cond as $field_key => $field_val) {
                if($field_key != 'in'){
                    $cond_word []= $this->tableField($field_key) .'='.$this->escapeString($field_val);
                }else{
                    //in條件式
                    foreach ($field_val as $in_key => $in_val) {
                        $cond_word []= $this->tableField($in_key) .'in (\''.$in_val.'\') ';
                    }
                }

            }
            $where_cond = implode(' AND ',$cond_word);

            $where = "WHERE $where_cond";
        }

        if(empty($where)){
            return false;
        }

        $stmt = "UPDATE $tb SET $update_data $where";
        // return $stmt;
        $rs = $this->db_link->Execute($stmt);
        if($rs != false ){
            return true;
        }else{
            //echo $this->db_link->ErrorMsg();
            return false;
        }
    }

    function getSelect($table , $cond , $key_word_data = '' ,$order_data = '' , $page_limit = 0 , $page_level = 1 ){
        $tb = $this->table($table, $this->company_db);
        $fix = " 1=1";
        $order = '';
        if($cond){
            if(is_array($cond)){
                foreach($cond as $key => $val){
                    if($val['cond'] == '=' || $val['cond'] == '>=' || $val['cond'] == '<=' || $val['cond'] == 'like'){
                        $fix .= "  AND  ". $this->tableField($val['key']).$val['cond']. $this->escapeString($val['val']);
                    }
                    if($val['cond'] == "in")
                    {
                        if(is_array($val['val'])){
                            foreach($val['val'] as $key => $val2)
                            {
                                $temp[$key] = $this->escapeString($val2);
                            }
                            $temp_val = implode(",",$temp);
                            $fix .= " AND ". $this->tableField($val['key']).$val['cond']."(".$temp_val.")";
                        }
                        else
                        {
                            echo '$val[\'val\']  不是array';
                            return false;
                        }
                    }
                }
                // echo $fix."<br>";
            }else{
                echo '$cond  不是array';
                return false;
            }
        }
        if($key_word_data){
            if(is_array($key_word_data)){
                if(isset($key_word_data['key_word']) && $key_word_data['key_word']){
                    $key_word =  $this->escapeString($key_word_data['key_word']);
                    if( $key_word_data['key_word'] && $key_word_data['key'][0] ){
                        $fix .= ' and  ( ';
                        $i = 1 ;
                        foreach($key_word_data['key'] as $key => $val){
                            if($i != 1){
                                $fix .= ' OR ';
                            }
                            $fix .=  $this->tableField($val) . 'like' . $key_word;
                            $i++;
                        }

                            $fix .= ')';
                    }
                }else if(isset($key_word_data['set'])&&is_array($key_word_data['set'])){
                    $fix .= ' and  ( ';                    
                    $i=1;
                    foreach ($key_word_data['set'] as $key => $valArr) {
                        if($i != 1){
                            $fix .= ' AND ';
                        }                        
                        $column     =$valArr['key'];
                        $key_word   =$this->escapeString($valArr['val']);
                        $fix .=  $this->tableField($column) . ' like' . $key_word;
                        $i++;
                    }
                    $fix .= ')';                    
                    // echo $fix;
                }
            }else{
                echo '$key_word_data  不是array';
                return false;
            }
        }
        if(is_array($order_data)){
            if(isset($order_data['key'])){
                $order = "ORDER BY ".$this->tableField($order_data['key'])."  ". (($order_data['type'] =='asc')?'ASC':'DESC')    ;
            }
        }

        $stmt = "select * from $tb where $fix $order ";
        // echo $stmt;
        if($page_limit ){
            $data = $this->db_link->SelectLimit($stmt, $page_limit, ($page_level-1) * $page_limit);
            if ($data and $data->RecordCount() > 0) {
                $rs = $data->getArray();
                return $rs;
            } else {
                return array();
            }
        }else{
            $rs = $this->db_link->Execute($stmt);
            if ($rs and $rs->RecordCount() > 0) {
                $data = $rs->getArray();
                return $data;
            } else {
                return array();
            }
        }
    }

    function sendEmailNotify($db,$from_info,$to_info,$subject,$content_link='',$content='',$content_title=null,$content_list=null,$content_table=null ){

        $ecm           =  $db->getMailLogoTitle();
        $module        = 'lance提醒事項';
        $title         = $subject;
        // $content_title = array();
        // $content_list  = array();
        $content_text  = '';
        // $content_table = '';
        $content_text2 = '<a href="'.$content_link.'">'.$content.'</a>';

        $mailBody = notificationHTML($ecm,$module,$title,$content_title,$content_list,$content_text,$content_table,$content_text2);

        $mailInfo = array("sender" => $from_info['email'],
                          "mailTo" => $to_info['email'],
                          "mailCc" => '',
                          "mailBcc" =>'',
                          "Subject" => $title,
                          "mailBody" => $mailBody,
                          "attachFiles" => '',
                          "inlineFiles" => '',
                          "editor" => 2
                          );

        if(!empty($from_info['email']) && !empty($to_info['email'])){
            sendMail($mailInfo, $from_info['email']);
            return 1; // testing
        }else{
            return 0;
        }

    }


// ====================== below functions not used yet ======================


    function getUserPermissionsId($table,$user_id_val,$type ){
        // $table = "lance_reminder_acl";
        $acl_type = $type;
        $table  = $this->table($table,$this->company_db);
        $column = $this->thb_property_acl;

        $acl_type=$this->escapeString($acl_type);

        $user_data = $this->getUserInfo($user_id_val,'guid');
        $ou_id = $user_data['dir_ou_id'];

        $key_word = " and ".$column['target_type'] ." = " . $acl_type;
        $where ="";
        $where .= " WHERE(
                ( ".$column['member_id']." = ".$user_id_val." and ".$column['member_type']." = 'cn' $key_word)
                or
                 ( ".$column['member_id']." = ".$user_data['dir_ou_id']." and ".$column['member_type']." = 'ou' $key_word)
                )";
        $sql = "SELECT ".$column['target'].",".$column['action'].",".$column['level'].",".$column['member_name']." FROM $table $where";
        // echo $sql;
        $rs=$this->db_link->Execute($sql);
        if ($rs and $rs->RecordCount() > 0) {
            $data =  $rs->getArray();
            $return_data ='';
            foreach($data as $key => $val){
                // if($val['target_id']){
                    if(isset($return_data[$val['action']]) && $return_data[$val['action']]){
                        // if(! in_array($val['target_id'] , $return_data[$val['action']])){
                            $return_data[$val['action']][] = $val['target'];
                        // }
                    }else{
                        $return_data[$val['action']][] = $val['target'];
                    }
                // }
            }
            return $return_data;
        } else {
            return 0;
        }
    }    
    function insert_array($table,$data){
        $table = $this->table($table,$this->company_db);
        $key_arr = array();
        $value_arr = array();
        foreach ($data as $key => $value){
        	$key_arr[] = $this->tableField($key);
            $value_arr[] = $this->escapeString($value);
        }
        $stmt = "insert into $table (". implode(",",$key_arr) .")value (".implode(",",$value_arr).")";
        $this->db_link->Execute($stmt);
        return $this->db_link->_insertid();
    }
    function update_array($table,$key,$value,$data){
        $table = $this->table($table,$this->company_db);
        $value = $this->escapeString($value);
        $key = $this->tableField($key);
        $fix = array();
        foreach($data as $data_key => $data_value){
            $fix[] = $this->tableField($data_key) ."=". $this->escapeString($data_value);
        }
        $stmt = "UPDATE $table set ". implode(",",$fix) ." WHERE $key = $value";
        $this->db_link->Execute($stmt);
        return true;
    }
    function delete($table,$key,$value){
        $tb = $this->table($table,$this->company_db);
        $key_field = $this->tableField($key);
        $value = $this->escapeString($value);
        $stmt = "delete from $tb where $key = $value";
        $this->db_link->Execute($stmt);
    }
    function getSingleById($table,$key,$value){
        $tb = $this->table($table,$this->company_db);
        $key = $this->tableField($key);
        $value = $this->escapeString($value);
        $stmt = "select * from $tb where $key = $value";
        $rs = $this->db_link->Execute($stmt);
        if($rs && $rs->RecordCount() > 0){
            return $rs->FetchRow();
        }else{
            return false;
        }
    }
    function getArrayById($table,$key,$value,$order=""){
        $tb = $this->table($table,$this->company_db);
        $key = $this->tableField($key);
        $value = $this->escapeString($value);
        $orderby = "";
        if($order != ""){
            $order_field = $this->tableField($order);
            $orderby = "order by $order_field";
        }
        $stmt = "select * from $tb where $key = $value $orderby";
        $rs = $this->db_link->Execute($stmt);
        $data = array();
        if($rs && $rs->RecordCount() > 0){
            foreach ($rs as $rs_data) {
                $data[] = $rs_data;
            }
        }
        return $data;
    }
    function getText($file,$file_id){
        $text = "";
        if($file_id > 0){
            $text_arr = $file->getFile($file_id);
            $text = $text_arr[$file_id];
        }
        return $text;
    }
}


function is_sset($name,$val="",$not_specialchars=true)
{
    if ($val)
    {
        if (isset($name))
        {
            if (isset($name) && is_array($name))
            {
                $post_arr = array();

                foreach($name as $key => $val)
                {
                    $post_arr[$key] = specialchars($val,$not_specialchars);
                    //$post_arr[$key] = htmlspecialchars($val);
                }

                $name = $post_arr;

                return isset($name) ? $name : "";
            }
            else if ($name)
            {
                return specialchars($name,$not_specialchars);
                //return htmlspecialchars($name);
            }
            else
            {
                return $val;
            }
        }
        else
        {
            return $val;
        }
    }
    else
    {
        if (isset($name) && is_array($name))
        {
            $post_arr = array();

            foreach($name as $key => $val)
            {
                $post_arr[$key] = specialchars($val,$not_specialchars);
                //$post_arr[$key] = htmlspecialchars($val);
            }

            $name = $post_arr;

            return isset($name) ? $name : "";
        }
        else
        {
            return isset($name) ? specialchars($name,$not_specialchars) : "";
        }
    }
}
function specialchars($val,$type=true)
{
    if (!isset($val))
    {
        return "";
    }

    if (is_array($val))
    {
        return $val;
    }

    if ($val && $type)
    {
        return htmlspecialchars($val);
    }
    else
    {
        return $val;
    }
}


?>
