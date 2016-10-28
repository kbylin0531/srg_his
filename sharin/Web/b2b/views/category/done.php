<div class="container">
    <br>
    <div class="row">
        <h3>当前：<?php echo isset($url)?$url:'未设置';?></h3>
    </div>
    <div class="row">
        <div>
            <ul class="nav nav-tabs">
                <li ><a href="<?php echo SCRIPT_URL;?>/category/set?code=<?php echo $code;?>">设置</a></li>
                <li class="active"><a href="<?php echo SCRIPT_URL;?>/category/done?code=<?php echo $code;?>">完成</a></li>
            </ul>
        </div>
    </div>
    <div class="tab1">
        <br>
        <div class="row" >
            <table id="dtable" class="table table-bordered" cellspacing="0" width="100%"></table>
        </div>
    </div>
</div>
<script>
    var code = "<?php echo $code;?>";
    var baseurl = "<?php echo SCRIPT_URL;?>";
    var dtable = datatables.create("#dtable",{
        columns: [
            {
                title:'id',
                data:'id',
                width: "6%"
            },
            {
                title:'name',
                data:'name',
                width: "40%"
            },
            {
                title:'cateid',
                data:'cateid',
                width: "6%"
            },
            {
                title:'catename',
                data:'catename',
                width: "40%"
            },
            {
                title:'operation',
                data:function () {
                    return '<a href="javascript:void(0);" class="rm">remove</a>';
                }
            }
        ]
    }).onDraw(function(){
        $(".rm").unbind("click").click(function () {
            var tr = $(this).closest("tr");
            var data = dtable.data(tr);
            $.get(baseurl+"/category/removeSaved",{id:data.id,code:code},function (data) {
                if(data.type == 1){
                    dtable.remove(tr);
                }else{
                    alert('删除失败');
                }
            });
        });
    });
    $.get(baseurl+"/category/savedList",{code:code},function(data){
        dtable.load(data);
    });
</script>