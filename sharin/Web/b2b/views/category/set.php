<div class="container">
    <br>
    <div class="row">
        <h3>当前：<?php echo isset($url)?$url:'未设置';?></h3>
    </div>

    <div class="row">
        <div>
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?php echo SCRIPT_URL;?>/category/set?code=<?php echo $code;?>">设置</a></li>
                <li><a href="<?php echo SCRIPT_URL;?>/category/done?code=<?php echo $code;?>">完成</a></li>
            </ul>
        </div>
    </div>
    <div class="tab1">
        <br>
        <div class="row" >
            <div class="col-xs-6 col-sm-6" >
                <label>上级分类选择</label>
                <div id="dropdown-holder"></div>
            </div>
            <div class="col-xs-6 col-sm-6" >
                <label>上级分类选择</label>
                <div id="dropdown-holder2"></div>
            </div>
        </div>
        <br>
        <div class="row" >
            <div class="col-xs-6 col-sm-6" >
                <table id="b2b" class="table table-bordered" cellspacing="0" width="100%"></table>
            </div>
            <div class="col-xs-6 col-sm-6">
                <table id="bg" class="table table-bordered" cellspacing="0" width="100%"></table>
            </div>
        </div>
        <div class="row" align="center">
            <br>
            <button type="button" id="auto" class="btn btn-default">自动匹配</button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" id="add" class="btn btn-default">添加</button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" id="save" class="btn btn-default">保存添加</button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" id="clear" class="btn btn-default">清除</button>
            <br>
        </div>
        <div class="row" >
            <table id="bd" class="table table-bordered" cellspacing="0" width="100%"></table>
        </div>
    </div>
</div>
<script>
    var code = "<?php echo $code;?>";
    var baseurl = "<?php echo SCRIPT_URL;?>";
    var topcate = {};
    var topcate2 = {};
    var sel = null;
    var sel2 = null;
    var bg = datatables.create('#bg',{
        searchDelay: 350,
        pagingType : "simple",
        columns: [
            {
                title:'id',
                data:'id',
                width: "12%"
            },
            {
                title:'name',
                data:function (row) {
                    var arr = row.name.split(" > ");
                    var target = topcate2;
                    for (var i = 0; i < arr.length - 1; i ++){
                        var name = arr[i];
                        if(!(name in target)){
                            target[name] = {};
                        }
                        target = target[name];
                    }
                    return row.name;
                },
                width: "88%"
            }
        ]
    }).onDraw(function () {
        //可选多行
        bg.table().find("tr").unbind("click").click(function () {
            var thistr = $(this);
            if ( thistr.hasClass('selected') ) {
                thistr.removeClass('selected');
            } else {
                thistr.addClass('selected');
            }
        });

        if(!sel2){
            sel2 = window.select.create().load(topcate2,function (data) {
                var format = [
                    ""
                ];
                for(var x in data){
                    format.push(x);
                    if(!$.isEmptyObject(data[x])){
                        for (var y in data[x]){
                            format.push("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+y);
                            if(!$.isEmptyObject(data[x][y])){
                                for (var z in data[x][y]){
                                    format.push( "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+z);
                                }
                            }
                        }
                    }
                }
                return format;
            }).getEle().change(function () {
                var selected = $(this).find("option:selected").text().trim();
                bg.search(selected,1);
            });
            $("#dropdown-holder2").html("").append(sel2);
        }
    });
    var b2b = datatables.create('#b2b',{
        pagingType : "simple",
        searchDelay: 350,
        columns: [
            {
                title:'id',
                data:'id',
                width: "12%"
            },
            {
                title:'name',
                data:function (row) {
                    var arr = row.name.split(" > ");
                    var target = topcate;
                    for (var i = 0; i < arr.length - 1; i ++){
                        var name = arr[i];
                        if(!(name in target)){
                            target[name] = {};
                        }
                        target = target[name];
                    }
                    return row.name;
                },
                width: "88%"
            }
        ]
    }).onDraw(function () {
        //限制于单行
        b2b.table().find("tr").unbind("click").click(function () {
            var thistr = $(this);
            if ( thistr.hasClass('selected') ) {
                thistr.removeClass('selected');
            } else {
                thistr.closest("table").find("tr").removeClass("selected");
                thistr.addClass('selected');
            }
        });
        if(!sel){
            sel = window.select.create().load(topcate,function (data) {
                var format = [
                    ""
                ];
                for(var x in data){
                    format.push(x);
                    if(!$.isEmptyObject(data[x])){
                        for (var y in data[x]){
                            format.push("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+y);
                            if(!$.isEmptyObject(data[x][y])){
                                for (var z in data[x][y]){
                                    format.push( "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+z);
                                }
                            }
                        }
                    }
                }
                return format;
            }).getEle().change(function () {
                var selected = $(this).find("option:selected").text().trim();
                b2b.search(selected,1);
            });
            $("#dropdown-holder").html("").append(sel);
        }
    });
    var bd = datatables.create("#bd",{
        columns: [
            {
                title:'id',
                data:'id'
            },
            {
                title:'name',
                data:'name'
            },
            {
                title:'id2',
                data:'id2'
            },
            {
                title:'name2',
                data:'name2'
            },
            {
                title:'operation',
                data:function () {
                    return '<a href="javascript:void(0);" class="dbd">remove</a>';
                }
            }
        ]
    }).onDraw(function(){
        $(".dbd").unbind("click").click(function () {
            var tr = $(this).closest("tr");
            var data = bd.data(tr);
            bg.load(data,false);
            bd.remove(tr);
        });
    });
    var databg = [];
    var datab2b = [];
    $.get(baseurl+"/category/list",{code:0,b2bcode:code},function(data){
        bg.load(databg = data);
    });

    var requestB2bC = function(times){
        $.get(baseurl+"/category/list",{code:code},function(data,status){
            console.log(status);
            if(status !== "success"){
                if(++times < 4){/* 最大重试4次 */
                    
                }
            }else{
                b2b.load(datab2b = data);
            }
        });
    };
    requestB2bC(0);

    $("#add").click(function(){
        var selected = bg.table().find("tr.selected");
        var b2bselected = b2b.table().find("tr.selected");
        if(!b2bselected.length || (b2bselected.length > 1)){
            return alert("请选择一条记录");
        }
        if(!b2bselected.length ){
            return alert("请选择一条记录");
        }
        var b2bdata = b2b.data(b2bselected.eq(0));
        var data = [];

        for (var i = 0 ; i < selected.length; i++){
            var d = bg.data(selected.eq(i));
            d.id2 = b2bdata.id;
            d.name2 = b2bdata.name;
            data.push(d);
        }
        bd.load(data,false);
        bg.remove(selected);
    });
    $("#save").click(function(){
        var data = bd.data();
        $.post(baseurl+"/category/save?code="+code,{data:data},function(data){
            alert(data.msg);
            if(data.type == 1){
                location.reload();
            }
        });
    });

    $("#clear").click(function () {
        modal.alert.show('it going to clear the cache');
    });

    $("#auto").click(function () {
        modal.alert.show('后台运行中，请勿进行其他操作');
        $.get(baseurl+'/category/autoFetch',{code:code},function (data) {
            modal.alert.hide();
            if(data.type == 1){
                alert("自动匹配完成，共匹配"+data.count+"条数据");
                location.reload();
            }else{
                alert("自动匹配失败");
            }
        });
    });
</script>
<style>
    td {cursor: default}
</style>