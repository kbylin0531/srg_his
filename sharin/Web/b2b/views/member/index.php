<div class="container">
    <br>
    <div class="toolbar">
        <button type="button" id="register" class="btn btn-default">register</button>
    </div>
    <br>
    <table id="dtable" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
</div>
<div id="addform">
    <form id="form">
        <img src='' id="capture" /><span id="capture_tip">正在获取验证码，清等待</span>
        <hr>
        <label >Code:</label>
        <input name="code" type="text">
        <label>Email:</label>
        <input name="email" type="text">
    </form>
</div>
<script>
    var datalist = to('<?php echo $data;?>');
    var baseurl = "<?php echo SCRIPT_URL;?>";
    $(function () {
        var dtable = datatables.create('#dtable',{
            columns: [
                {
                    title:'username',
                    data:'username'
                },
                {
                    title:'password',
                    data:'passwd'
                },
                {
                    title:'email',
                    data:'email'
                },
                {
                    title:'total',
                    data:'total'
                },
                {
                    title:'cateid',
                    data:'cateid'
                },
                {
                    title:'operation',
                    data:function (row) {
                        var query = $.param({
                            username:row.username,
                            passwd:row.passwd
                        });
                        return '<a href="#" target="_blank">login</a> <a href="__CONTROLLER__/published?'+query+'" target="_blank">publish</a>';
                    }
                }
            ]
        }).load(datalist);

        var loadone = false;
        var md = modal.create("#addform",{
            confirm:function () {
                if(loadone){
                    $.post(baseurl+"/member/register",$("#form").serialize(),function (data) {
                        //添加到新的一行
                        dtable.load([{
                            username:data.value.username,
                            passwd:data.value.passwd,
                            email:data.value.email,
                            total:0,
                            cateid:data.value['gid']
                        }],false);
                        md.hide();
                    });
                }else{
                    console.log('清等待验证码获取完毕')
                }
            },
            show:function () {
                loadone = false;
                var tip = $("#capture_tip").show();
                $.get(baseurl+"/member/capture",function (data) {
                    $("#capture").attr("src",data.src);
                    tip.hide();
                    loadone = true;
                });
            }
        });

        $("#register").click(function () {
            md.show();
        });
    });
</script>
