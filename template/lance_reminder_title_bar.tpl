<style>
    .dmfilter > .active > a:before,
    .dmfilter > .active > a:hover:before,
    .dmfilter > .active > a:focus:before {
        content: "\f00c";
        font-family: FontAwesome;
        font-style: normal;
        font-weight: normal;
        text-decoration: inherit;
        position: absolute;
        left: 8px;
        color: #616161;
        font-size: 12px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="grid-body">
            <div class="row-fluid">
                <div class="row">
                    <div class="col-md-4 col-xs-12">
                        <div class="input-group search-dropdown-control" style="{show_search}">
                            <input type="text" id="keyword_tital" value="{keyword_tital}" class="form-control" placeholder="請輸入關鍵字">
                        <!-- 
                            透過onclick="$('#search_more').toggle()"觸發進階搜尋後
                            把視窗鎖定住不讓點擊其他地方而讓進階搜尋視窗關閉
                         -->
                            <span class="input-group-addon-white" onclick="$('#search_more').toggle();">
                                <i class="fa fa-caret-down"></i>
                            </span>
                            <span id="normalSearch" class="input-group-addon info search-button" onclick="singleSearch(id);">
                                <i class="fa fa-search" ></i>           
                            </span>
                            <div class="dropdown-menu search-dropdown" id="search_more">
                                <div class="row form-row m-t-10 owl-buttons" >
                                    <div class="col-md-12">
                                        <label class="form-label">搜尋主題</label>
                                        <input type="text" class="form-control" id="keyword_subject" value="{keyword_subject}" placeholder="請輸入關鍵字">
                                    </div>
                                </div>
                                <div class="row form-row m-t-10 owl-buttons" >
                                    <div class="col-md-12">
                                        <label class="form-label">搜尋備註</label>
                                        <input type="text" class="form-control" id="keyword_remark" value="{keyword_remark}" placeholder="請輸入關鍵字">
                                     </div>
                                </div>
                                <div class="form-actions">
                                    <div type="button" id="searchMore" class="btn btn-success btn-cons" onclick="searchMore(id);"> 搜尋</div>
                                    <div type="button" class="btn btn-white btn-cons" onclick="$('#search_more').toggle()">返回</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 m-t-sm-10 m-t-xs-10 text-left-xs">
                        <div id="add_button" type="button" class="btn btn-success btn-demo-space addNewBtnColor fancybox" data-toggle="modal" data-target="#case_modal" style="{show_addBtn}">
                            <i class="fa fa-paste"></i>&nbsp;新增
                        </div>
                        <div class="btn-group sortGroup" style="{show_orderBtn}"> <a class="btn btn-info dropdown-toggle btn-demo-space" data-toggle="dropdown" href="#"> 排序 <span class="caret"></span> </a>
                            <ul class="dropdown-menu dmfilter" id="sort">
                                <!-- START BLOCK : order_list -->
                                <li orderName="{orderName}" orderType="{orderType}" class="orderBtn {class}"><a class="orderLink" onclick="search_orderby('{orderName}','{orderType}')">{orderShowname}</a></li>
                                <!-- END BLOCK : order_list  -->     
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 m-t-sm-10 text-right-sm text-right-md text-right-lg text-left-xs">
                        <div class="btn-group" style="{show_menuBtn}">
                            <a id="landMenu" class="btn btn-white dropdown-toggle btn-demo-space" data-toggle="dropdown" href="#"><i class="fa fa-th"></i></a>
                            <!-- BLOCK : oaks_v3_submenu -->
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="margin5">
<script>
$('#landMenu').popSubMenu({ url:'lance_reminder/lance_reminder_menu.php',ui_version:'3'});
// $('#landMenu').popSubMenu({ mod_name:'lance_reminder',ui_version:'3'});
$('#leftMenu').hide();
$(document).ready(function(){

});

function singleSearch(id){
    var keyword_tital = $('#keyword_tital').val();
    $("#keyword_tital").val(keyword_tital);
    gotoPageV('{index}');    
}

function searchMore(id){
    // console.log(id);
    // $("#searchAction").val(id);
    var keyword_subject = $('#keyword_subject').val();
    var keyword_remark = $('#keyword_remark').val();    
    $("#keyword_subject").val(keyword_subject);
    $("#keyword_remark").val(keyword_remark);    
    gotoPageV('{index}');
}

function search_orderby(order_name,order) {
    $('#order_name').val(order_name);
    $('#order').val(order);
    //alert($('#order_name').val());
    gotoPageV('{index}');
}
function gotoPage(gotoType) {
        gotoPageV('{index}&page_level=' + gotoType);
}

function gotoPageV(url){
    // var key_word = $('#keyword').val();
    var keyword_tital = $('#keyword_tital').val();   
    var keyword_remark = $('#keyword_remark').val();       
    var keyword_subject = $('#keyword_subject').val();
    var order_name = $('#order_name').val();
    var order = $('#order').val();
    changeForm(url 
        + '&keyword_tital=' + keyword_tital 
        + '&keyword_remark=' + keyword_remark                         
        + '&keyword_subject=' + keyword_subject 
        + '&order_name='+ order_name 
        + '&order='+order
    );
}


</script>
