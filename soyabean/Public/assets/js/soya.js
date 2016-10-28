/**
 * Created by linzh on 2016/6/30.
 *
 * A JavaScript implementation of the Secure Hash Algorithm, SHA-1, as defined
 * in FIPS PUB 180-1
 *
 * By lizq
 *
 * 2006-11-11
 *
 * 原生javascript类库
 * typeof 运算符把类型信息当作字符串返回。typeof 返回值有六种可能：
 *  "number," "string," "boolean," "object," "function," 和 "undefined."
 * Created by kbylin on 5/14/16.
 *
 * 性能分析结果（By chrome51）：
 *  144.36 ms Loading
 *  744.11 ms Scripting
 *  52.64 ms Rendering
 *  2.51 ms Painting
 *
 *
 *
 * 不支持IE8及以下的浏览器
 *  ① querySelector() 方法仅仅返回匹配指定选择器的第一个元素。如果你需要返回所有的元素，请使用 querySelectorAll() 方法替代。
 *
 */
window.soya = (function(){
    //开启严格模式节约时间
    "use strict";

    //常见的兼容性问题处理
    //处理console对象缺失
    !window.console &&  (window.console = (function(){var c = {}; c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile = c.clear = c.exception = c.trace = c.assert = function(){}; return c;})());
    //解决IE8不支持indexOf方法的问题
    if (!Array.prototype.indexOf){
        Array.prototype.indexOf = function(elt){
            var len = this.length >>> 0;
            var from = Number(arguments[1]) || 0;
            from = (from < 0) ? Math.ceil(from) : Math.floor(from);
            if (from < 0) from += len;
            for (; from < len; from++) {
                if (from in this && this[from] === elt) return from;
            }
            return -1;
        };
    }
    if (!Array.prototype.max){
        Array.prototype.max = function(){
            return Math.max.apply({},this)
        };
    }
    if (!Array.prototype.min){
        Array.prototype.min = function(){
            return Math.min.apply({},this)
        };
    }

    if (!String.prototype.trim){
        String.prototype.trim=function()    {
            return this.replace(/(^\s*)|(\s*$)/g,'');
        };
    }
    if (!String.prototype.ltrim){
        String.prototype.ltrim=function()    {
            return this.replace(/(^\s*)/g,'');
        };
    }
    if (!String.prototype.rtrim){
        String.prototype.rtrim=function()    {
            return this.replace(/(\s*$)/g,'');
        };
    }
    if(!String.prototype.beginWith){
        String.prototype.beginWith = function (chars) {
            return this.indexOf(chars) === 0;
        };
    }

//-------------------------------------------- sha1 ----------------------------------------------------------------//

    var options = {
        //公共资源的URL路径
        'public_url':'',
        //自动加载路径
        'auto_url':'',
        //debug模式
        'debug_on':false,
        //hex output format. 0 - lowercase; 1 - uppercase
        hexcase:0,
        //bits per input character. 8 - ASCII; 16 - Unicode};
        chrsz:8
    };

    var clone = function (obj) {
        // Handle the 3 simple types, and null or undefined
        // "number," "string," "boolean," "object," "function," 和 "undefined"
        //null 本身就是一个空的对象
        if (!obj || "object" !== typeof obj) return obj;
        var copy = null;
        // Handle Date
        if (obj instanceof Date) {
            copy = new Date();
            copy.setTime(obj.getTime());
            return copy;
        }

        // Handle Array
        if (obj instanceof Array) {
            copy = [];
            var len = obj.length;
            for (var i = 0; i < len; ++i) {
                copy[i] = clone(obj[i]);
            }
            return copy;
        }

        // Handle Object
        if (obj instanceof Object) {
            copy = {};
            for (var attr in obj) {
                if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
            }
            return copy;
        }

        throw new Error("Unable to copy obj! Its type isn't supported.");
    };

    /**
     * The standard SHA1 needs the input string to fit into a block
     * This function align the input string to meet the requirement
     */
    var AlignSHA1 = function (str){
        var nblk = ((str.length + 8) >> 6) + 1, blks = new Array(nblk * 16);
        for (var i = 0; i < nblk * 16; i++) blks[i] = 0;
        for (i = 0; i < str.length; i++) blks[i >> 2] |= str.charCodeAt(i) << (24 - (i & 3) * 8);
        blks[i >> 2] |= 0x80 << (24 - (i & 3) * 8);
        blks[nblk * 16 - 1] = str.length * 8;
        return blks;
    };
    /**
     * Bitwise rotate a 32-bit number to the left.
     * 32位二进制数循环左移
     */
    var rol = function (num, cnt){
        return (num << cnt) | (num >>> (32 - cnt));
    };

    /**
     * Calculate the SHA-1 of an array of big-endian words, and a bit length
     */
    var core_sha1 = function (blockArray){
        var x = blockArray; // append padding
        var w = new Array(80);
        var a = 1732584193;
        var b = -271733879;
        var c = -1732584194;
        var d = 271733878;
        var e = -1009589776;
        for (var i = 0; i < x.length; i += 16)  {// 每次处理512位 16*32
            var olda = a;
            var oldb = b;
            var oldc = c;
            var oldd = d;
            var olde = e;
            for (var j = 0; j < 80; j++) {// 对每个512位进行80步操作
                if (j < 16) w[j] = x[i + j];
                else w[j] = rol(w[j - 3] ^ w[j - 8] ^ w[j - 14] ^ w[j - 16], 1);
                var t = safe_add(safe_add(rol(a, 5), sha1_ft(j, b, c, d)), safe_add(safe_add(e, w[j]), sha1_kt(j)));
                e = d;
                d = c;
                c = rol(b, 30);
                b = a;
                a = t;
            }
            a = safe_add(a, olda);
            b = safe_add(b, oldb);
            c = safe_add(c, oldc);
            d = safe_add(d, oldd);
            e = safe_add(e, olde);
        }
        return [a, b, c, d, e];
    };

    /**
     * Convert an array of big-endian words to a hex string.
     */
    var binb2hex = function (binarray){
        var hex_tab = options.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
        var str = "";
        for (var i = 0; i < binarray.length * 4; i++) str += hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8 + 4)) & 0xF) +hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8)) & 0xF);
        return str;
    };

    /**
     * Perform the appropriate triplet combination function for the current
     * iteration
     * 返回对应F函数的值
     */
    var sha1_ft = function (t, b, c, d){
        if (t < 20) return (b & c) | ((~ b) & d);
        if (t < 40)  return b ^ c ^ d;
        if (t < 60) return (b & c) | (b & d) | (c & d);
        return b ^ c ^ d; // t<80
    };
    /**
     * Determine the appropriate additive constant for the current iteration
     * 返回对应的Kt值
     */
    var sha1_kt = function (t){
        return (t < 20) ? 1518500249 : (t < 40) ? 1859775393 : (t < 60) ? -1894007588 : -899497514;
    };
    /**
     * Add integers, wrapping at 2^32. This uses 16-bit operations internally
     * to work around bugs in some JS interpreters.
     * 将32位数拆成高16位和低16位分别进行相加，从而实现 MOD 2^32 的加法
     */
    var safe_add = function (x, y){
        var lsw = (x & 0xFFFF) + (y & 0xFFFF);
        var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    };

//-------------------------------------------- md5 ----------------------------------------------------------------//

    var rotateLeft = function (lValue, iShiftBits) {
        var a = lValue<<iShiftBits;
        var b = lValue>>>(32-iShiftBits);
        return a|b;
    };

    var addUnsigned = function (lX,lY) {
        var lX4,lY4,lX8,lY8,lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
        var c = lX4 & lY4;
        if (c) {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        var v = 0;
        c = lX4 | lY4;
        if (c) {
            c = lResult & 0x40000000;
            if (c) {
                v =  (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            } else {
                v =  (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        } else {
            v = (lResult ^ lX8 ^ lY8);
        }
        return v;
    };

    var f = function (x,y,z) { return (x & y) | ((~x) & z); };
    var g = function (x,y,z) { return (x & z) | (y & (~z)); };
    var h = function (x,y,z) { return (x ^ y ^ z); };
    var i = function (x,y,z) { return (y ^ (x | (~z))); };

    var FF = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(f(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var GG = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(g(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var HH = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(h(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var II = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(i(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };
    /**
     *
     * @param str string
     * @returns {Array}
     * @constructor
     */
    var convertToWordArray = function (str) {
        var lWordCount;
        var lMessageLength = str.length;
        var lNumberOfWords_temp1=lMessageLength + 8;
        var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
        var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
        var lWordArray= new Array(lNumberOfWords-1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while ( lByteCount < lMessageLength ) {
            lWordCount = (lByteCount-(lByteCount % 4))/4;
            lBytePosition = (lByteCount % 4)*8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount)<<lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount-(lByteCount % 4))/4;
        lBytePosition = (lByteCount % 4)*8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
        lWordArray[lNumberOfWords-2] = lMessageLength<<3;
        lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
        return lWordArray;
    };

    var wordToHex = function (lValue) {
        var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
        for (lCount = 0;lCount<=3;lCount++) {
            lByte = (lValue>>>(lCount*8)) & 255;
            WordToHexValue_temp = "0" + lByte.toString(16);
            WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
        }
        return WordToHexValue;
    };

    var utf8Encode = function (str) {
        str = str.replace(/\r\n/g,"\n");
        var utftext = "";
        for (var n = 0; n < str.length; n++) {
            var c = str.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            }else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    };


//-------------------------------------------- 其他函数 ----------------------------------------------------------------//
    var readyStack = [];
    //加载的类库
    var _library = [];

    var pathen = function (path) {
        if((path.length > 4) && (path.substr(0,4) !== 'http')){
            if(!options['public_url']) options['public_url'] = '/';//throw "Public uri not defined!";
            path = options['public_url']+path;
        }
        return path;
    };

    //上下文环境相关的信息
    var context = {
        /**
         * get the hash of uri
         * @returns {string}
         */
        getHash:function () {
            if(!location.hash) return "";
            var hash = location.hash;
            var index = hash.indexOf('#');
            if(index >= 0) hash = hash.substring(index+1);
            return ""+hash;
        },
        /**
         * get script path
         * there are some diffrence between domain access(virtual machine) and ip access of href
         * domian   :http://192.168.1.29:8085/edu/Public/admin.php/Admin/System/Menu/PageManagement#dsds
         * ip       :http://edu.kbylin.com:8085/admin.php/Admin/System/Menu/PageManagement#dsds
         * what we should do is SPLIT '.php' from href
         *
         * ps:location.hash
         */
        getBaseUri:function () {
            var href = location.href;
            var index = href.indexOf('.php');
            if(index > 0 ){//exist
                return href.substring(0,index+4);
            }else{
                if(location.origin){
                    return location.origin;
                }else{
                    return location.protocol+"//"+location.host;
                }
            }
        },
        /**
         * 跳转到指定的链接地址
         * 增加检查url是否合法
         * @param url
         */
        redirect:function (url) {
            location.href = url;
        },
        //get real path to this action
        getPath:function () {
            var path = location.pathname;
            // var path = "/admin.php/Syse/dsds/dsdsds#dsds";
            var index = path.indexOf('.php');
            if(index >= 0 ) path = path.substring(index+4);
            index = path.indexOf('?');
            if(index >= 0) path = path.substring(0,index);
            index = path.indexOf("#");
            if(index >= 0) path = path.substring(0,index);
            // console.log(index,location.pathname,path);
            //trim '/'
            var startindex = 0;
            for(var i = 0 ; i < path.length; i ++){
                if(path[i] === '/' || path[i] === ' '){
                    startindex ++;
                }else{
                    break;
                }
            }

            return "/"+path.substring(startindex);
        },
        //获得可视区域的大小
        getViewPort:function () {
            var win = window;
            var type = 'inner';
            if (!('innerWidth' in window)) {
                type = 'client';
                win = document.documentElement ?document.documentElement: document.body;
            }
            return {
                width: win[type + 'Width'],
                height: win[type + 'Height']
            };
        },
        //获取浏览器信息 返回如 Object {type: "Chrome", version: "50.0.2661.94"}
        getBrowserInfo:function () {
            var ret = {}; //用户返回的对象
            var _tom = {};
            var _nick;
            var ua = navigator.userAgent.toLowerCase();
            (_nick = ua.match(/msie ([\d.]+)/)) ? _tom.ie = _nick[1] :
                (_nick = ua.match(/firefox\/([\d.]+)/)) ? _tom.firefox = _nick[1] :
                    (_nick = ua.match(/chrome\/([\d.]+)/)) ? _tom.chrome = _nick[1] :
                        (_nick = ua.match(/opera.([\d.]+)/)) ? _tom.opera = _nick[1] :
                            (_nick = ua.match(/version\/([\d.]+).*safari/)) ? _tom.safari = _nick[1] : 0;
            if (_tom.ie) {
                ret.type = "ie";
                ret.version = parseInt(_tom.ie);
            } else if (_tom.firefox) {
                ret.type = "firefox";
                ret.version = parseInt(_tom.firefox);
            } else if (_tom.chrome) {
                ret.type = "chrome";
                ret.version = parseInt(_tom.chrome);
            } else if (_tom.opera) {
                ret.type = "opera";
                ret.version = parseInt(_tom.opera);
            } else if (_tom.safari) {
                ret.type = "safari";
                ret.version = parseInt(_tom.safari);
            }else{
                ret.type = ret.version ="unknown";
            }
            return ret;
        },
        /**
         * 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符,年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
         * @param fmt
         * @returns {*}
         */
        date:function(fmt){ //author: meizz
            if(!fmt) fmt = "yyyy-MM-dd hh:mm:ss.S";//2006-07-02 08:09:04.423
            var o = {
                "M+" : this.getMonth()+1,                 //月份
                "d+" : this.getDate(),                    //日
                "h+" : this.getHours(),                   //小时
                "m+" : this.getMinutes(),                 //分
                "s+" : this.getSeconds(),                 //秒
                "q+" : Math.floor((this.getMonth()+3)/3), //季度
                "S"  : this.getMilliseconds()             //毫秒
            };
            if(/(y+)/.test(fmt))
                fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
            for(var k in o){
                if(!o.hasOwnProperty(k)) continue;
                if(new RegExp("("+ k +")").test(fmt))
                    fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
            }
            return fmt;
        },
        ieVersion:function () {
            var version;
            if(version = navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/)){
                version = parseInt(version[1]);
            }else{
                version = 11;//如果是其他浏览器，默认判断为版本11
            }
            return version;
        }
    };

    var utils = {
        /**
         * 检查dom对象是否存在指定的类名称
         * @param obj
         * @param cls
         * @returns {Array|{index: number, input: string}}
         */
        hasClass:function(obj, cls) {
            return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
        },
        /**
         * 添加类
         * @param obj
         * @param cls
         */
        addClass:function (obj, cls) {
            if (!this.hasClass(obj, cls)) obj.className += " " + cls;
        },
        /**
         * 删除类
         * @param obj
         * @param cls
         */
        removeClass: function (obj, cls) {
            if (hasClass(obj, cls)) {
                var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
                obj.className = obj.className.replace(reg, ' ');
            }
        },
        /**
         * 逆转类
         * @param obj
         * @param cls
         */
        toggleClass:function (obj,cls){
            if(hasClass(obj,cls)){
                removeClass(obj, cls);
            }else{
                addClass(obj, cls);
            }
        },
        /**
         * sha1加密
         * @param s
         */
        sha1:function (s) {return binb2hex(core_sha1(AlignSHA1(s)));},
        /**
         * md5加密
         * @param str
         * @returns {string}
         */
        md5:function (str) {
            var k,AA,BB,CC,DD,a,b,c,d;
            var S11=7, S12=12, S13=17, S14=22;
            var S21=5, S22=9 , S23=14, S24=20;
            var S31=4, S32=11, S33=16, S34=23;
            var S41=6, S42=10, S43=15, S44=21;
            str = utf8Encode(str);
            var x = convertToWordArray(str);
            a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
            for (k=0;k<x.length;k+=16) {
                AA=a; BB=b; CC=c; DD=d;
                a=FF(a,b,c,d,x[k], S11,0xD76AA478);
                d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
                c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
                b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
                a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
                d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
                c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
                b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
                a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
                d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
                c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
                b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
                a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
                d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
                c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
                b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
                a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
                d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
                c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
                b=GG(b,c,d,a,x[k], S24,0xE9B6C7AA);
                a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
                d=GG(d,a,b,c,x[k+10],S22,0x2441453);
                c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
                b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
                a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
                d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
                c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
                b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
                a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
                d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
                c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
                b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
                a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
                d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
                c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
                b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
                a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
                d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
                c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
                b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
                a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
                d=HH(d,a,b,c,x[k], S32,0xEAA127FA);
                c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
                b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
                a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
                d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
                c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
                b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
                a=II(a,b,c,d,x[k], S41,0xF4292244);
                d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
                c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
                b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
                a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
                d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
                c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
                b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
                a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
                d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
                c=II(c,d,a,b,x[k+6], S43,0xA3014314);
                b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
                a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
                d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
                c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
                b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
                a=addUnsigned(a,AA);
                b=addUnsigned(b,BB);
                c=addUnsigned(c,CC);
                d=addUnsigned(d,DD);
            }
            var temp = wordToHex(a)+wordToHex(b)+wordToHex(c)+wordToHex(d);
            return temp.toLowerCase();
        },
        cookie:{
            /**
             * set cookie
             * @param name
             * @param value
             * @param expire
             * @param path
             */
            set:function (name, value, expire,path) {
                // console.log(name, value, expire,path);
                path = ";path="+(path ? path : '/');// all will access if not set the path
                var cookie;
                if(undefined === expire || false === expire){
                    //set or modified the cookie, and it will be remove while leave from browser
                    cookie = name+"="+value;
                }else if(!isNaN(expire)){// is numeric
                    var _date = new Date();//current time
                    if(expire > 0){
                        _date.setTime(_date.getTime()+expire);//count as millisecond
                    }else if(expire === 0){
                        _date.setDate(_date.getDate()+365);//expire after an year
                    }else{
                        //delete cookie while expire < 0
                        _date.setDate(_date.getDate()-1);//expire after an year
                    }
                    cookie = name+"="+value+";expires="+_date.toUTCString();
                }else{
                    console.log([name, value, expire,path],"require the param 3 to be false/undefined/numeric !");
                }
                document.cookie = cookie+path;
            },
            //get a cookie with a name
            get:function (name) {
                if(document.cookie.length > 0){
                    var cstart = document.cookie.indexOf(name+"=");
                    if(cstart >= 0){
                        cstart = cstart + name.length + 1;
                        var cend = document.cookie.indexOf(';',cstart);//begin from the index of param 2
                        (-1 === cend) && (cend = document.cookie.length);
                        return document.cookie.substring(cstart,cend);
                    }
                }
                return "";
            }
        },
        setOpacity: function(ele, opacity) {
            if (ele.style.opacity != undefined) {
                ///兼容FF和GG和新版本IE
                ele.style.opacity = opacity / 100;
            } else {
                ///兼容老版本ie
                ele.style.filter = "alpha(opacity=" + opacity + ")";
            }
        },
        fadein:function (ele, opacity, speed) {
            if (ele) {
                var v = ele.style.filter.replace("alpha(opacity=", "").replace(")", "") || ele.style.opacity;
                (v < 1) && (v *= 100);
                var count = speed / 1000;
                var avg = count < 2 ? (opacity / count) : (opacity / count - 1);
                var timer = null;
                timer = setInterval(function() {
                    if (v < opacity) {
                        v += avg;
                        this.setOpacity(ele, v);
                    } else {
                        clearInterval(timer);
                    }
                }, 500);
            }
        },
        fadeout:function (ele, opacity, speed) {
            if (ele) {
                var v = ele.style.filter.replace("alpha(opacity=", "").replace(")", "") || ele.style.opacity || 100;
                v < 1 && (v *= 100);
                var count = speed / 1000;
                var avg = (100 - opacity) / count;
                var timer = null;
                timer = setInterval(function() {
                    if (v - avg > opacity) {
                        v -= avg;
                        this.setOpacity(ele, v);
                    } else {
                        clearInterval(timer);
                    }
                }, 500);
            }
        },
        /**
         * 原先的设计是在Object中添加一个方法,但与jquery的遍历难兼容
         *  Object.prototype.utils
         * 避免发生错误修改为参数加返回值的设计
         * @param options {{}}
         * @param config {{}}
         * @param covermode
         * @returns {*}
         */
        initOption:function (options,config,covermode) {
            for(var x in config){
                if(!config.hasOwnProperty(x)) continue;
                if(covermode || options.hasOwnProperty(x)) options[x] = config[x];
            }
            return options;
        },
        /**
         * 随机获取一个GUID
         * @returns {string}
         */
        guid:function() {
            var s = [];
            var hexDigits = "0123456789abcdef";
            for (var i = 0; i < 36; i++) {
                s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
            }
            s[14] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
            s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
            s[8] = s[13] = s[18] = s[23] = "-";
            return s.join("");
        },
        /**
         * PHP中的parse_url 的javascript实现
         * @param str json字符串
         * @returns {Object}
         */
        parseUrl:function (str) {
            var obj = {};
            if(!str) return obj;

            str = decodeURI(str);
            var arr = str.split("&");
            for(var i=0;i<arr.length;i++){
                var d = arr[i].split("=");
                obj[d[0]] = d[1]?d[1]:'';
            }
            return obj;
        },
        /**
         * 判断是否是Object类的实例,也可以指定参数二来判断是否是某一个类的实例
         * 例如:isObject({}) 得到 [object Object] isObject([]) 得到 [object Array]
         * @param obj
         * @param classname
         * @returns {boolean}
         */
        isObject:function (obj,classname) {
            if(undefined === classname){
                return obj instanceof Object;
            }
            return Object.prototype.toString.call(obj) === '[object '+classname+']';
        },
        /**
         * 判断一个元素是否是数组
         * @param el
         * @returns {boolean}
         */
        isArray  : function (el) {
            return Object.prototype.toString.call(el) === '[object Array]';
        },
        /**
         * 判断元素是否是一个函数
         * @param el
         * @returns {boolean}
         */
        isFunc:function (el) {
            return '[object Function]' === Object.prototype.toString.call(el);
        },
        //注意安全性问题,并不推荐使用
        toObject:function (str) {
            if(str instanceof Object) return str;/* 已经是对象的清空下直接返回 */
            return eval ("(" + str + ")");//将括号内的表达式转化为对象而不是作为语句来处理
        },
        /**
         * 遍历对象
         * @param object {{}|[]} 待遍历的对象或者数组
         * @param itemcallback 返回
         * @param userdata
         */
        each:function (object,itemcallback,userdata) {
            var result = undefined;
            if(this.isArray(object)){
                for(var i=0; i < object.length; i++){
                    result = itemcallback(object[i],i,userdata);
                    if(result === '[break]') break;
                    if(result === '[continue]') continue;
                    if(result !== undefined) return result;//如果返回了什么东西解释实际返回了，当然除了命令外
                }
            }else if(this.isObject(object)){
                for(var key in object){
                    if(!object.hasOwnProperty(key)) continue;
                    result = itemcallback(object[key],key,userdata);
                    if(result === '[break]') break;
                    if(result === '[continue]') continue;
                    if(result !== undefined) return result;
                }
            }else{
                console.log(object," is not an object or array,continue!");
            }
        },
        /**
         * 复制一个数组或者对象
         * @param array 要拷贝的数组或者对象
         * @param isObject bool 是否是对象
         * @returns array|{}
         */
        copy:function (array,isObject) {
            var kakashi;
            if(!isObject){
                kakashi = [];
                for(var i =0;i < array.length;i++){
                    kakashi[i] = array[i];
                }
            }else{
                kakashi = {};
                utils.each(array,function (item,key) {
                    kakashi[key] = item;
                });
            }
            return kakashi;
        },
        /**
         * 检查对象是否有指定的属性
         * @param object {{}}
         * @param prop 属性数组
         * @return int 返回1表示全部属性都拥有,返回0表示全部都没有,部分有的情况下返回-1
         */
        checkProperty:function (object, prop) {
            if(!utils.isArray(prop)) prop = [prop];
            var count = 0;
            for(var i = 0; i < prop.length;i++){
                if(object.hasOwnProperty(prop[i])) count++;
            }
            if(count === prop.length) return 1;
            else if(count === 0) return 0;
            else return -1;
        },
        /**
         * 停止事件冒泡
         * 如果提供了事件对象，则这是一个非IE浏览器,因此它支持W3C的stopPropagation()方法
         * 否则，我们需要使用IE的方式来取消事件冒泡
         * @param e
         */
        stopBubble: function (e) {
            if ( e && e.stopPropagation ) {
                e.stopPropagation();
            } else {
                window.event.cancelBubble = true;
            }
        },
        /**
         * 阻止事件默认行为
         * 阻止默认浏览器动作(W3C)
         * IE中阻止函数器默认动作的方式
         * @param e
         * @returns {boolean}
         */
        stopDefault: function ( e ) {
            if ( e && e.preventDefault ) {
                e.preventDefault();
            } else {
                window.event.returnValue = false;
            }
            return false;
        }
    };

    //http://www.cnblogs.com/rubylouvre/archive/2009/07/24/1529640.html
    var dom = {
        //支持多个类名的查找
        getElementsByClassName:function (className, element) {
            var children = (element || document).getElementsByTagName('*');
            var elements = [];

            for (var i = 0; i < children.length; i++) {
                var child = children[i];
                var classNames = child.className.split(' ');
                for (var j = 0; j < classNames.length; j++) {
                    if (classNames[j] == className) {
                        elements.push(child);
                        break;
                    }
                }
            }

            return elements;
        }
    };

    /**
     * 设计目标：
     * ① 按照类选择加载的对象
     */
    var template = {
        /**
         * 加载对象中的数据
         * @param data object
         */
        load:function (data) {
            if(!utils.isObject(data)) throw "template.load(object)!!";
            console.log(data);
            utils.each(data,function (perdata, selector) {
                // console.log(perdata,selector); return ;
                selector = document.querySelector(selector);
                if(selector){
                    var clone = selector.cloneNode(true);
                    var parent = selector.parentNode;
                    var result = template.dispatch(perdata,clone);

                    parent.removeChild(selector);//remove self
                    if(utils.isArray(result)) {
                        // console.log(result);
                        utils.each(result, function (item) {
                            parent.appendChild(item);
                        });
                    }
                }
            });
        },
        dispatch:function (data, ele) {
            // return console.log(data,ele);
            var result = undefined;
            if(utils.isArray(data)){
                result = template.cloneList(ele,data);
            }else if(utils.isObject(data)){
                result = template.getIndividual(ele,data);
                console.log(result);
            }else{
                result = template.parse(ele,data);
            }
            return result;
        },
        getIndividual:function (element,data) {
            var ele = clone = undefined;
            soya.utils.each(data,function (item, selector) {
                 // console.log(item,selector);return ;
                ele = element.querySelector(selector);
                ele && template.dispatch(item,ele);
            });
            return element;
        },
        /**
         * 讲元素克隆并发会列表
         * @param element 待克隆的元素，克隆完毕后将消失
         * @param datas 克隆所需要的数据列表
         * @param env 克隆的数据依附的环境
         * @returns {Array} 返回克隆的元素列表
         */
        cloneList:function (element,datas,env) {
            var clone = undefined;
            var list = [];
            soya.utils.each(datas,function (data) {
                clone = element.cloneNode(true);
                // console.log(clone,data);
                clone = template.parse(clone,data);
                if(env){
                    env.appendChild(clone);
                }
                list.push(clone);
            });
            env && element.parentNode.removeChild(element);
            return list;
        },
        /**
         * 元素数据解析
         * @param ele 待解析的元素
         * @param data 解析所需要的数据
         * @returns {*} 将解析完毕的元素原样返回
         */
        parse:function (ele,data) {
            // console.log(ele,data);
            var e , p;
            soya.utils.each(data,function (value, key) {
                if(utils.isArray(value)){
                    e = ele.querySelector(key);
                    p = e.parentNode;
                    // p.removeChild(e);
                    template.cloneList(e,value,p);
                }else if(utils.isObject(value)){
                    //
                }else{
                    var arr = key.split('&');
                    e = ele.querySelector(arr[0]);
                    // console.log(e,ele,arr[0]);
                    e || (e = ele);//找不到时以自身作为设置

                    if(2 == arr.length){
                        if(arr[1]){
                            e.setAttribute(arr[1],value);
                        }else{
                            e.innerHTML = value;
                        }
                    }else{
                        //未设置分隔符号时直接设置innerHTML
                        e.innerHTML = value;
                    }
                }
            });
            return ele;
        }
    };

    //监听窗口状态变化
    window.document.onreadystatechange = function(){
        // console.log(window.document.readyState);
        if( window.document.readyState === "complete" ){
            if(readyStack.length){
                for(var i=0;i<readyStack.length;i++) {
                    // console.log(callback)
                    (readyStack[i])();
                }
            }
        }
    };

    return {
        clone:clone,
        init:function (config) {
            utils.each(config,function (item,key) {
                options.hasOwnProperty(key) && (options[key] = item);
            });
            return this;
        },
        context:context,
        utils:utils,
        template:template,
        /**
         * 新建一个DOM元素
         * @param expression 元素表达式
         * @param inner 设置innerHTML
         */
        newElement:function (expression,inner) {
            var tagname  = expression, classes, id;
            if(expression.indexOf('.') > 0 ){
                classes = expression.split(".");
                expression = classes.shift();
            }
            if(expression.indexOf("#") > 0){
                var tempid = expression.split("#");
                tagname = tempid[0];
                id = tempid[1];
            }else{
                tagname = expression
            }
            var element = document.createElement(tagname);
            id && element.setAttribute('id',id);
            if(classes){
                var ct = '';
                for (var i =0;i <classes.length; i++){
                    ct += classes[i];
                    if(i !== classes.length - 1)  ct += ",";
                }
                element.setAttribute('class',ct);
            }
            if(inner) element.innerHTML = inner;
            return element;
        },
        //获取一个单例的操作对象作为上下文环境的深度拷贝
        newInstance:function (context) {
            var Yan = function () {return {target:null};};
            var instance = new Yan();
            if(context){
                utils.each(context,function (item,key) {
                    instance[key] = item;
                });
            }
            return instance;
        },
        load:function (path,type) {
            if(typeof path === 'object'){
                for(var x in path){
                    if(!path.hasOwnProperty(x)) continue;
                    this.load(path[x]);
                }
            }else{
                if(undefined === type){
                    var t = path.substring(path.length-3);//根据后缀自动判断类型
//                    console.log(path.substring(path.length-3));
                    switch (t){
                        case 'css':type = 'css';    break;
                        case '.js':type = 'js';     break;
                        case 'ico':type = 'ico';    break;
                        default:throw "加载了错误的类型'"+t+"',加载的类型必须是[css,js,ico]";
                    }
                }
                //本页面加载过将不再重新载入
                for(var i = 0; i < _library.length; i++) if(_library[i] === path) return;
                //现仅仅支持css,js,ico的类型
                switch (type){
                    case 'css':
                        document.write('<link href="'+pathen(path)+'" rel="stylesheet" type="text/css" />');
                        break;
                    case 'js':
                        document.write('<script src="'+pathen(path)+'"  /></script>');
                        break;
                    case 'ico':
                        document.write('<link rel="shortcut icon" href="'+pathen(path)+'" />');
                        break;
                    default:
                        return;
                }
                //记录已经加载过的
                _library.push(path);
            }
            return this;
        },
        ready:function (callback) {readyStack.push(callback);}
    };
})();
// 加密测试
// console.log(soya.utils.md5(soya.utils.sha1('123456')) === 'd93a5def7511da3d0f2d171d9c344e91');


