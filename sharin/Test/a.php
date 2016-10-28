
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<HTML>
<HEAD>
    <title>Select your category</title>
    <style type="text/css"> BODY { MARGIN-TOP: 0px; FONT-SIZE: 12px; COLOR: #000; FONT-FAMILY: Arial,sans-serif }
        TD { MARGIN-TOP: 0px; FONT-SIZE: 12px; COLOR: #000; FONT-FAMILY: Arial,sans-serif }
        A:link { COLOR: #039; TEXT-DECORATION: underline }
        A:visited { COLOR: #600; TEXT-DECORATION: underline }
        A:hover { COLOR: #f60; TEXT-DECORATION: underline }
        .X { FONT: bold 24px Verdana,sans-serif }
        .L { FONT: bold 18px Verdana,sans-serif }
        .M { FONT: bold 12px Verdana,sans-serif }
        .C { FONT: 12px Verdana,sans-serif }
        .S { FONT: 10px Verdana,sans-serif }
        .WH { FONT: bold 12px Verdana,sans-serif; COLOR: #fff }
        .WH:link { FONT: bold 12px Verdana,sans-serif; COLOR: #fff }
        .WH:visited { FONT: bold 12px Verdana,sans-serif; COLOR: #fff }
        .WH:hover { FONT: bold 12px Verdana,sans-serif; COLOR: #fff }
        .SWH { FONT: bold 9px Verdana,sans-serif; COLOR: #fff }
        .OR { FONT: bold 12px Verdana,sans-serif; COLOR: #f60 }
        .OR:link { FONT: bold 12px Verdana,sans-serif; COLOR: #f60 }
        .OR:visited { FONT: bold 12px Verdana,sans-serif; COLOR: #f60 }
        .YL { FONT: bold 11px Verdana,sans-serif; COLOR: #ff0 }
        .RD { FONT: bold 12px Verdana,sans-serif; COLOR: #f00 }
        .GR { COLOR: #999 }
        .NO { COLOR: #900 }
        .MORE { FONT: bold 10px Verdana,sans-serif; TEXT-DECORATION: underline }
        .TA { FONT: 11px Verdana,sans-serif; COLOR: #000; TEXT-DECORATION: none }
        .TA:link { FONT: 11px Verdana,sans-serif; COLOR: #000; TEXT-DECORATION: none }
        .TA:visited { FONT: 11px Verdana,sans-serif; COLOR: #000; TEXT-DECORATION: none }
        .TA:hover { FONT: 11px Verdana,sans-serif; COLOR: #f60; TEXT-DECORATION: none }
        .TB { FONT: bold 11px Verdana,sans-serif; COLOR: #fff; TEXT-DECORATION: none }
        .TB:link { FONT: bold 11px Verdana,sans-serif; COLOR: #fff; TEXT-DECORATION: none }
        .TB:visited { FONT: bold 11px Verdana,sans-serif; COLOR: #fff; TEXT-DECORATION: none }
        .TB:hover { FONT: bold 11px Verdana,sans-serif; COLOR: #f60; TEXT-DECORATION: none }
        .ST { FONT: bold 11px Verdana,sans-serif }
        .SOR:link { FONT: bold 11px Verdana,sans-serif; COLOR: #f60 }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>
<body leftmargin="1" topmargin="4" marginwidth="4" marginheight="4">
<script language="JavaScript" type="text/JavaScript">
    <!--

    function expandIt(el) {
        var on_img="image/menu_open.gif";
        var off_img="image/menu_add.gif";



        whichEl = eval(el + "Child");
        imageEl = eval("CategoryForm." + el + "Image");
        if (whichEl.style.display == "none") {
            whichEl.style.display = "block";
            imageEl.src = on_img;
        }
        else {
            whichEl.style.display = "none";
            imageEl.src = off_img;
        }

    }

    function select(Id ,str,str1){
        //var selectedCategory = window.document.CategoryForm.categoryIdsStr.value;
        //var count=parseInt(window.document.CategoryForm.count.value);
        /*str2="selectcategory.aspx?catId="+Id;


         if(str!="")
         {
         str2+="&catalog="+str+"&catalogid="+str1;
         }
         alert(str2);

         //alert(window.location.href);
         */
    }

    function selectcontrol(Object,str){
        /* var count1= window.document.CategoryForm.count.value;
         var count=parseInt(count1);
         var appendValue,appendID;
         if(count==0)
         {
         appendValue = str;
         appendID = Object.value;
         }
         else
         {
         appendValue = ','+str;
         appendID = ','+ Object.value;
         }
         if(Object.checked){
         count++;
         if (count>4){
         alert("Sorry, you have chosen more than 4 categories. Please check and choose again.");
         Object.checked=false;
         count--;
         }
         else {
         window.document.CategoryForm.categoryIdsStr.value = window.document.CategoryForm.categoryIdsStr.value +appendValue;
         window.document.CategoryForm.categoryIds.value = window.document.CategoryForm.categoryIds.value +appendID;
         }
         }
         else {
         count--;
         window.document.CategoryForm.categoryIdsStr.value = removeSubFromString(appendValue,window.document.CategoryForm.categoryIdsStr.value);
         window.document.CategoryForm.categoryIds.value = removeSubFromString(appendID,window.document.CategoryForm.categoryIds.value);

         }
         window.document.CategoryForm.count.value=count;*/
        window.document.CategoryForm.categoryIdsStr.value = str;
        window.document.CategoryForm.categoryIds.value = Object.value;

    }

    function removeSubFromString(sub,str) {
        var ret = str;
        var index = str.indexOf(sub);
        if (index != -1){
            var len = sub.length;
            ret = str.substring(0,index) + str.substring(index + len,str.length);
        }
        return ret;
    }
    function return_item()
    {

        if(window.document.CategoryForm.path.value=="1")
        {
            window.opener.document.Form1.commonCategoryName.value =window.document.CategoryForm.categoryIdsStr.value ;
            window.opener.document.Form1.commonCategory.value = window.document.CategoryForm.categoryIds.value ;
        }
        else
        {
            window.opener.document.Form1.commonCategoryName1.value =window.document.CategoryForm.categoryIdsStr.value ;
            window.opener.document.Form1.commonCategory1.value = window.document.CategoryForm.categoryIds.value ;
        }
        self.close();
        window.opener.focus();
    }
    -->
</script>
<form name="CategoryForm" method="post">

    <input type="hidden" name="path" value='1'>
    <input type="hidden" name="categoryIdsStr1" value=''>
    <input type="hidden" name="categoryIds1" value=''>
    <input type="hidden" name="categoryIdsStr">
    <input type="hidden" name="categoryIds">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" background="image/top_bg.gif">
        <tr>
            <td height="30" align="right"><img src="image/title_select.gif" width="242"
                                               height="30"></td>
        </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td height="4"></td>
        </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td width="170" height="48" align="center" valign="top" bgcolor="#cccccc">
                <table id="total_list" width="100%" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=1&catalog=&catalogid=&path=1'>Agriculture</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=2&catalog=&catalogid=&path=1'>Apparel</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=3&catalog=&catalogid=&path=1'>Automobile</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=28&catalog=&catalogid=&path=1'>Bags, Cases & Boxes</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=4&catalog=&catalogid=&path=1'>Business Services</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=5&catalog=&catalogid=&path=1'>Chemical</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;" bgcolor="#f5f5f5">.</td>
                        <td bgcolor="#f5f5f5"><b>Computer</b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=7&catalog=&catalogid=&path=1'>Construction & Real Estate</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=8&catalog=&catalogid=&path=1'>Electronics & Electrical</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=9&catalog=&catalogid=&path=1'>Energy</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=10&catalog=&catalogid=&path=1'>Environment</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=30&catalog=&catalogid=&path=1'>Fashion Accessories</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=12&catalog=&catalogid=&path=1'>Food & Beverage</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=16&catalog=&catalogid=&path=1'>Furniture & Furnishings </a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=13&catalog=&catalogid=&path=1'>Gifts & Crafts</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=11&catalog=&catalogid=&path=1'>Hardware & Tools</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=14&catalog=&catalogid=&path=1'>Health & Beauty</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=20&catalog=&catalogid=&path=1'>Home Appliance</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=31&catalog=&catalogid=&path=1'>Light & Lighting</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=15&catalog=&catalogid=&path=1'>Light Industry & Daily Use</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=17&catalog=&catalogid=&path=1'>Machinery</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=21&catalog=&catalogid=&path=1'>Measurement & Analysis Instruments </a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=18&catalog=&catalogid=&path=1'>Minerals & Materials</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=19&catalog=&catalogid=&path=1'>Office & School Supplies</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=29&catalog=&catalogid=&path=1'>Packaging & Printing </a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=22&catalog=&catalogid=&path=1'>Security & Protection</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=23&catalog=&catalogid=&path=1'>Sports & Leisure</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=24&catalog=&catalogid=&path=1'>Telecommunications</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=25&catalog=&catalogid=&path=1'>Textiles & Leather Products</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=26&catalog=&catalogid=&path=1'>Toys</a></b></td>
                    </tr>
                    <tr>
                        <td align="right" width="4" style=".M: FONT: bold 12px Verdana,sans-serif ;">.</td>
                        <td><b><a href='selectcategory.aspx?catId=27&catalog=&catalogid=&path=1'>Transportation</a></b></td>
                    </tr>
                </table>

                <br>
            </td>
            <td height="48" align="center" valign="top" bgcolor="#f5f5f5">
                <table width="100%" border="0" align="center" cellpadding="4" cellspacing="2">
                    <tr>
                        <td valign="top">
                            <table id="list" width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Computer');return true;" name='categoryId' value='51'>Computer</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Computer Accessories ');return true;" name='categoryId' value='48'>Computer Accessories </td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Computer Cables Connectors');return true;" name='categoryId' value='59'>Computer Cables Connectors</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Computer Case, PC Case');return true;" name='categoryId' value='52'>Computer Case, PC Case</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Comsumables');return true;" name='categoryId' value='53'>Comsumables</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Copiers');return true;" name='categoryId' value='943'>Copiers</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Drives Storage Devices');return true;" name='categoryId' value='67'>Drives Storage Devices</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Graphics Cards');return true;" name='categoryId' value='944'>Graphics Cards</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Hard Drives');return true;" name='categoryId' value='946'>Hard Drives</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Hardware Components');return true;" name='categoryId' value='54'>Hardware Components</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'HDD Enclosure');return true;" name='categoryId' value='945'>HDD Enclosure</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Keypad and Keyboard');return true;" name='categoryId' value='248'>Keypad and Keyboard</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Laptop Accessories');return true;" name='categoryId' value='610'>Laptop Accessories</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Memory');return true;" name='categoryId' value='1238'>Memory</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Memory Card');return true;" name='categoryId' value='49'>Memory Card</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Modems');return true;" name='categoryId' value='47'>Modems</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Monitors Displays');return true;" name='categoryId' value='56'>Monitors Displays</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Mouse');return true;" name='categoryId' value='956'>Mouse</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Mouse Pads');return true;" name='categoryId' value='955'>Mouse Pads</td>
                                </tr>
                            </table>

                        </td>
                        <td width="50%" valign="top">
                            <table id="list2" width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Network Cards');return true;" name='categoryId' value='948'>Network Cards</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Network Device');return true;" name='categoryId' value='58'>Network Device</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Network Switches');return true;" name='categoryId' value='954'>Network Switches</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Notebook and Laptop');return true;" name='categoryId' value='61'>Notebook and Laptop</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Other Computer');return true;" name='categoryId' value='69'>Other Computer</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'PDA');return true;" name='categoryId' value='62'>PDA</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Peripherals');return true;" name='categoryId' value='63'>Peripherals</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Processors');return true;" name='categoryId' value='953'>Processors</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Routers');return true;" name='categoryId' value='952'>Routers</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Scanners');return true;" name='categoryId' value='57'>Scanners</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Second Hand');return true;" name='categoryId' value='64'>Second Hand</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Server and Workstation');return true;" name='categoryId' value='65'>Server and Workstation</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Software');return true;" name='categoryId' value='66'>Software</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Tablet PC');return true;" name='categoryId' value='949'>Tablet PC</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'UPS and Powers');return true;" name='categoryId' value='68'>UPS and Powers</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'USB Flash Drives');return true;" name='categoryId' value='50'>USB Flash Drives</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'USB Gadgets');return true;" name='categoryId' value='950'>USB Gadgets</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'USB Hubs');return true;" name='categoryId' value='46'>USB Hubs</td>
                                </tr>
                                <tr>
                                    <td><input type='radio'  onclick = "selectcontrol(this,'Webcam');return true;" name='categoryId' value='947'>Webcam</td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
                <br>

                <input name="button" type="button" onclick="return_item()" value=" Submit ">
                <br>
            </td>
        </tr>
    </table>
</form>
</body>
</HTML>