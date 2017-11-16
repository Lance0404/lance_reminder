<!-- INCLUDE BLOCK : lance_reminder_title_bar -->
<div class="row">
    <div class="col-md-12 m-t-10 " style="padding:0;">
        <div class="grid simple dataTables_wrapper" style="margin-bottom:50px;">
            <div class="grid-title gridWithPaginate">
                <div class="row">
                    <div class="col-md-6">
                        <!-- INCLUDE BLOCK : page_title_bar -->
                    </div>
                    <div class="col-md-6">
                        <!-- INCLUDE BLOCK : page_line_bar -->
                        <input type="hidden" id="order_name" value="{order_name}">
                        <input type="hidden" id="order" value="{order}">
                    </div>                    
                </div>
            </div>
            <div class="grid-body">
                <table class="table table-bordered no-more-tables landTable tabbleNarrow table-vert-center JtableWithCheck table-center" id="reminder_table">
                    <thead>
                        <tr>
                            <th>主題</th>
                            <th>備註</th>
                            <th>通知</th>
                            <th>通知時間</th>
                            <th>建立時間</th>
                            <th>動作</th>
                        </tr>
                    </thead>

                    <tbody>
                    <!-- START BLOCK : LIST -->                        
                        <tr>
                            <td class="text-left">{item_subject}</td>
                            <td class="text-left">{item_remark}</td>
                            <td >{notify}</td>
                            <td >{notify_datetime}</td>
                            <td >{create_time}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success dropdown-toggle fuctionBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <div class="fa fa-th-list top-settings-dark"></div>
                                    </button>
                                    <ul class="dropdown-menu function-dropdown" id="">
                                        <span class="arrow_r_int"></span>
                                        <span class="arrow_r_out"></span>
                                        <li>
                                            <a id="up_{reminder_guid}" class="update_reminder" data-value={reminder_guid} data-toggle="modal" data-target="#case_modal">
                                                <i class="fa fa-pencil-square-o"></i>  修改
                                            </a>                                          
                                        </li>
                                        <li>
                                            <a id="del_{reminder_guid}" class="delete_reminder" data-value={reminder_guid} data-toggle="modal" data-target="#case_modal">
                                                <i class="fa fa-trash"></i>  刪除
                                            </a>                                        
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <!-- END BLOCK : LIST -->                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal starts here -->
<div id="case_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content--> 
        <form id="case_form">
            <div class="modal-content" id="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">待辦事項</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label"><span class="important-star">*</span><span>主題</span></label>
                            <input class="form-control" id="item_subject" name="item_subject" type="text" value="">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label"><span>備註</span></label>
                            <input class="form-control" id="item_remark" name="item_remark" type="text" value="">
                        </div>
                    </div>
                    <div class="row item_status_row">
                        <div class="col-md-4">
                            <label class="form-label">狀態</label>
                            <select style="width:100%" id="item_status" name="item_status">
                                <option value="0">未完成</option>
                                <option value="1">已完成</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">通知</label>
                            <select style="width:100%" id="notify" name="notify">
                                <option id="notify_0" value="0">不通知</option>
                                <option id="notify_1" value="1">通知</option>
                            </select>
                        </div>    
                        <div class="col-md-4 notifydatetime" style="display:none">
                            <label class="form-label">日期</label>
                            <div id="notifyDateGroup" class="input-append success date no-padding JdataPicker" data-date-format="yyyy-mm-dd">
                                <input id="notify_date" type="text" class="form-control" placeholder="yyyy-mm-dd">
                                <span class="add-on"><span class="arrow"></span><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>                                         
                        <div class="col-md-4 notifydatetime" style="display:none">
                            <label class="form-label">時間</label>
                            <select id="notify_hour" class="notify_hour" name="notify_hour" style="width:30%">
                                <!-- START BLOCK : HOUR --> 
                                <option value="{hour_val}">{hour_val}</option>
                                <!-- END BLOCK : HOUR -->
                            </select>時
                            <select id="notify_min" class="notify_min" name="notify_min" style="width:30%">
                                <!-- START BLOCK : MIN -->
                                <option value="{min_val}">{min_val}</option>
                                <!-- END BLOCK : MIN -->
                            </select>分
                                                 
                            <!-- <input class="form-control " id="notify_time" name="notify_time" type="text" style="width:100%" placeholder="HH:MM:SS"> -->
                        </div>
                    </div>    
                </div>
                <div class="modal-footer">
                    <button type="button" id="send" class="btn btn-success send">確定</button>
                    <button type="button" id="cancel" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Update Modal ends here -->

<div id="height_top"></div>
<script>



// 最先做
$(document).ready(function(){
    $('#notify').val(0);
    $('#notify_hour').val('00');
    $('#notify_min').val('00');
    $(".notifydatetime").hide();
});

// 建立datepicker
$('#notifyDateGroup').datepicker({
    autoclose: true
});

// 當點擊取消回歸原始狀態
$("#cancel").click(function(){
    renewModal('all');
})


// ========= 當點擊新增鈕
$('#add_button').on('click', function(){
    renewModal('all');

    $('div.item_status_row').hide();
    $('h4.modal-title').text('新增待辦事項');    

    $("#notify").click(function(e){
        e.preventDefault();
        if ($('#notify').val()==1){
            $(".notifydatetime").show();
            $('#notifyDateGroup').datepicker('update', new Date());
        }else{
            renewModal('notify');
        }
    });

    $('#modal-content').on('click','#send', function(event){
        event.preventDefault();
        case_form('send','case_form','add_case');
        renewModal('all');       
    });     
});    



// ========= 當點擊修改鈕
$('#reminder_table').on('click','.update_reminder', function(){

    // 抓點擊的鈕的data-value值
    if ($(this).attr('data-value')){
        var reminder_guid = $(this).attr('data-value');
    }else{return false;}

    $('h4.modal-title').text('修改待辦事項');  
    // 用guid撈值進來
    $.ajax({
        url: "/lance_reminder/lance_reminder_action.php?action=update_button",
        type: "POST",
        dataType:'json', 
        data:{
            "reminder_guid":reminder_guid, 
        },
        success:function(data, status){
            $('#item_subject').val(data.item_subject);
            $('#item_remark').val(data.item_remark);
            $('#item_status').val(data.item_status);
            $('#notify').val(data.notify);

            if (data.notify ==1){
                $("div.notifydatetime").show();
            }else{
                $("div.notifydatetime").hide();
            }
            var datetime=data.notify_datetime.match(/(\S+)\s(\S+)/);
            if (datetime[1] == '0000-00-00'){
                $('#notify_date').val(''); 
            }
            if(datetime[2] =='00:00:00'){
                $('#notify_hour').val('00');
                $('#notify_min').val('00');                               
            }
            if((datetime[1]!='0000-00-00')||(datetime[2]!='00:00:00')){
                $('#notify_date').val(datetime[1]); 
                if (datetime[2]!='00:00:00'){
                    var hourmin=datetime[2].match(/(\S+?):(\S+?):(\S+?)/);
                    $('#notify_hour').val(hourmin[1]);
                    $('#notify_min').val(hourmin[2]);
                }
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
        }
    });

    $("#notify").click(function(e){
        // console.log($('#notify').val());
        e.preventDefault();
        if ($('#notify').val()==1){
            $(".notifydatetime").show();
            $('#notifyDateGroup').datepicker('update', new Date());
            // 不知何故，更動"通知"會還原到send id             
        }else{
            renewModal('notify')
        }
    });

    $('.modal-content').on('click', '#send', function(event) {
        event.preventDefault();
        case_form('send','case_form','update_case',reminder_guid);
        renewModal('all');
    });   
});

// ========= 當點擊刪除鈕
$('#reminder_table').on('click','.delete_reminder', function(){
    if ($(this).attr('data-value')){
        var reminder_guid = $(this).attr('data-value');
    }else{return false;}

    $('h4.modal-title').text('刪除待辦事項');  
    // 用guid撈值進來
    $.ajax({
        url: "/lance_reminder/lance_reminder_action.php?action=update_button",
        type: "POST",
        dataType:'json', 
        data:{
            "reminder_guid":reminder_guid, 
        },
        success:function(data, status){
            $('#item_subject').val(data.item_subject);
            $('#item_remark').val(data.item_remark);
            $('#item_status').val(data.item_status);
            $('#notify').val(data.notify);

            if (data.notify ==1){
                $("div.notifydatetime").show();
            }else{
                $("div.notifydatetime").hide();
            }
            var datetime=data.notify_datetime.match(/(\S+)\s(\S+)/);
             if (datetime[1] == '0000-00-00'){
                $('#case_modal').find('#notify_date').val(''); 
            }
            if(datetime[2] =='00:00:00'){
                $('#case_modal').find('#notify_hour').val('00');
                $('#case_modal').find('#notify_min').val('00');                               
            }
            if((datetime[1]!='0000-00-00')||(datetime[2]!='00:00:00')){
                $('#case_modal').find('#notify_date').val(datetime[1]); 
                if (datetime[2]!='00:00:00'){
                    var hourmin=datetime[2].match(/(\S+?):(\S+?):(\S+?)/);
                    $('#notify_hour').val(hourmin[1]);
                    $('#notify_min').val(hourmin[2]);
                }
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
        }        
    });

    $('.modal-content').on('click', '#send', function(event) {
        event.preventDefault();
        case_form('send','case_form','delete_case',reminder_guid);
        renewModal('all');
    }); 

});

// ======================== JS function ========================

function case_form(id,formid,action,guid){
    if (guid===undefined){ 
        guid='';
    }
    // console.log(id,formid,action)
    if ((action=='add_case') && (action=='update_case')){
        // 新增與修改鈕都需要欄位檢查
        var input_id = ['item_subject','item_remark','item_status','notify','notify_date','notify_time'];
        var input_name = ['主題','詳細內容','狀態','通知','日期','時間'];
        var must_fill = [0];

        for (var i = 0; i < must_fill.length; i++) {
            var must_index=must_fill[i];
            var val=$('#'+input_id[must_index]).val();
            if (!val){
                alert('請填寫'+input_name[must_index]);
                return false;
            }
        }
    }

    // var reminder_guid;
    var formJSON;
    switch (action){
        case "add_case":
            formJSON=$('#'+formid).serialize(); //return string, not really JSON
            formJSON+='&notify_date='+$('#notify_date').val();  
        break;

        case "update_case":
            formJSON=$('#'+formid).serialize();
            formJSON+='&notify_date='+$('#notify_date').val();  
        break;

        case "delete_case":
            formJSON={
                "reminder_guid":guid
            }   
        break;
    }
    console.log(guid);
    console.log(formJSON);    
    var reminder_guidGET=(guid=='')?'':'&guid=';
    var ajaxUrl='/lance_reminder/lance_reminder_action.php?'
            +'action='+action
            +reminder_guidGET+guid;

    $.ajax({
        url: ajaxUrl,
        type: "POST",
        dataType:'json', 
        data:formJSON,
        success:function(data, status){
            // alert(data);
            location.reload();                
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
        }
    });
}

$(document).ready(function(){

var urlNow      = window.location.href;
var url_match = urlNow.match(/^http:\/\/.*?action=(.*?)(&\S+)*$/);
var func=url_match[1];
// console.log(urlNow);
// console.log(func);

if (func == "lance_reminder_undone"){
    $('#add_button').show();
}else if (func == "lance_reminder_done") {
    $('#add_button').hide();
}

});
</script>