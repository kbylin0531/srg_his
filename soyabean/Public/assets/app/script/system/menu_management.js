/**
 * Created by kbylin on 17/05/16.
 */
soya.ready(function () {
    "use strict";

    var active_index = 0;

    //create header-menu and sidebar-menu,all assigned to same group
    var HeaderNestable = Dazzling.nestable.create(1).attachTo("#top_nestable_attach");
    var SiderNestable = Dazzling.nestable.create(1).attachTo("#side_nestable_attach");

    var MenuAEModal = Dazzling.modal.create("#MenuItemAddModal", {
        'confirmText': '提交',
        'cancelText': '关闭'
    }).onCancel(function () {
        MenuAEModal.hide();/* cancel btn is always to close the window,but confirm not */
    });

    //上下文菜单对象
    var MenuContextMenu = Dazzling.contextmenu.create(
        [{'edit':'修改','delete':'删除'}],
        function (element,tabindex) {
            var obj = element.get(0).dataset;
            if('edit' === tabindex){
                operator.menu.initMenuEdit(element,obj);
            }else if('delete' === tabindex){
                if(element.find('ol>li').length) return Dazzling.toast.warning('删除该节点前请先删除子节点项!');
                operator.menu.deleteMenu(element,obj['id']);
            }else{
                throw "Unknown operation!";
            }
        }
    );

    var MenuItemContextMenu = Dazzling.contextmenu.create(
        [{'edit':'修改','delete':'删除'}],
        function (element,tabindex) {
            var obj = element.get(0).dataset;
            if('edit' === tabindex){
                operator.menu.initMenuItemEdit(element,obj);
            }else if('delete' === tabindex){
                if(element.find('ol>li').length) return Dazzling.toast.warning('删除该节点前请先删除子节点项!');
                operator.menu.deleteMenuItem(element,obj['id']);
            }else{
                throw "Unknown operation!";
            }
        }
    );

    //操作列表
    var operator = (function () {
        var header_menu = null;
        var sidebar_menu = null;
        return {
            //load the top menu(whose index is 1)
            loadHeaderMenu:function (menu) {
                var isfirst = true;
                HeaderNestable.load(menu,function (data, element) {
                    MenuContextMenu.bind(element);
                    if(isfirst) {
                        //active the first element default
                        active_index = data['id'];
                        Dazzling.nestable.active(element);
                        isfirst = false;
                    }
                });
            },
            loadSidebarMenuByIndex:function (index) {
                if(index instanceof Object){
                    index = index.id;
                }
                // console.log(index);
                if(sidebar_menu.hasOwnProperty(index)){
                    var menu = sidebar_menu[index];
                    if(menu.hasOwnProperty('value')){
                        active_index = index;
                        SiderNestable.load(menu['value'],function (data,element) {
                            MenuItemContextMenu.bind(element);
                        });
                    }
                }
            },
            loadMenus : function () {
                var env = this;
                Dazzling.post(public_url + 'getMenus', {}, function (data) {
                    sidebar_menu = data['sidebar'];
                    env.loadHeaderMenu(header_menu = data['header']);
                    // env.loadSidebarMenuByIndex();
                });
            },
            saveHeaderMenuConfig : function () {
                var header = HeaderNestable.serialize(true);
                Dazzling.post(public_url+'saveHeaderMenuConfig',{ header:header});
            },
            saveSideMenuConfig:function () {
                var value = SiderNestable.serialize(true);
                Dazzling.post(public_url+"saveSidebarMenuConfig",{sidebar:value,id:active_index});
            },
            addSideMenuConfig:function () {
                MenuAEModal.getElement("[name=value]").attr('readonly','readonly');
                MenuAEModal.title('添加顶部菜单').show().onConfirm(function () {
                    var obj = soya.utils.parseUrl(Dazzling.form.serialize("#MenuItemAddForm"));
                    Dazzling.post(public_url+'createMenu',obj,function (data,ismsg,msgtype) {
                        if(ismsg){
                            //大于0成功，小于0警告，等于0表示发生了错误
                            if (msgtype > 0) {
                                return Dazzling.toast.success(data['_msg']);
                            } else if (msgtype < 0) {
                                return Dazzling.toast.warning(data['_msg']);
                            } else {
                                return Dazzling.toast.error(data['_msg']);
                            }
                        }else{
                            obj.id = data.id;
                            var item = HeaderNestable.createItem(obj);
                            operator.saveHeaderMenuConfig();//添加的同时保存配置
                            MenuContextMenu.bind(item);
                            MenuAEModal.hide();
                        }
                        return true;
                    });
                });
            },
            addMenuItem:function () {
                MenuAEModal.getElement("[name=value]").removeAttr('readonly');
                MenuAEModal.title('添加侧边栏菜单项').show().onConfirm(function () {
                    var obj = $("#MenuItemAddForm").fetchObject();
                    Dazzling.post(public_url+"createMenuItem",obj,function (data,ismsg,msgtype) {
                        if(ismsg){
                            //大于0成功，小于0警告，等于0表示发生了错误
                            if (msgtype > 0) {
                                return Dazzling.toast.success(data['_msg']);
                            } else if (msgtype < 0) {
                                return Dazzling.toast.warning(data['_msg']);
                            } else {
                                return Dazzling.toast.error(data['_msg']);
                            }
                        }else{
                            obj.id = data.id;
                            var item = SiderNestable.createItem(obj);
                            operator.saveSideMenuConfig();//添加的同时保存配置
                            MenuContextMenu.bind(item);
                            //重新加载菜单
                            Dazzling.post(public_url + 'getMenus', {}, function (data) {
                                sidebar_menu = data['sidebar'];
                            });
                            MenuAEModal.hide();
                        }
                        return true;
                    });

                });
            },
            menu:{
                initMenuEdit:function (element,obj) {
                    if(soya.utils.isObject(obj)){
                        MenuAEModal.getElement("[name=value]").attr('readonly','readonly');
                        MenuAEModal.title('修改顶部菜单');
                        var form = MenuAEModal.getElement("#MenuItemAddForm");
                        soya.utils.each(obj,function (value, key) {
                            var input = form.find("[name="+key+"]");
                            input.val(value);
                        });

                        MenuAEModal.show().onConfirm(function () {
                            var obj = form.fetchObject();
                            Dazzling.post(public_url+"updateMenu",obj,function (data, msg,msgtype) {
                                // console.log(msg,msgtype)
                                if(msg && (msgtype > 0)){
                                    Dazzling.nestable.updateItemData(element,obj);
                                    MenuAEModal.hide();
                                }
                            });
                        });
                    }
                },
                initMenuItemEdit:function (element, obj) {
                    if(soya.utils.isObject(obj)){
                        MenuAEModal.title('修改顶部菜单');
                        MenuAEModal.getElement("[name=value]").removeAttr('readonly');
                        var form = MenuAEModal.getElement("#MenuItemAddForm");
                        soya.utils.each(obj,function (value, key) {
                            var input = form.find("[name="+key+"]");
                            input.val(value);
                        });

                        MenuAEModal.show().onConfirm(function () {
                            var obj = form.fetchObject();
                            Dazzling.post(public_url+"updateMenuItem",obj,function (data, msg,msgtype) {
                                // console.log(msg,msgtype)
                                if(msg && (msgtype > 0)){
                                    Dazzling.nestable.updateItemData(element,obj);
                                    MenuAEModal.hide();
                                }
                            });
                        });
                    }
                },
                deleteMenu:function (element,id) {/* delete */
                    Dazzling.post(public_url+"deleteMenu",{id:id},function (data, msg, type) {
                        if(msg && (type > 0)){
                            element.remove();
                        }
                    });
                },
                deleteMenuItem:function (element, id) {
                    Dazzling.post(public_url+"deleteMenuItem",{id:id},function (data, msg, type) {
                        if(msg && (type > 0)){
                            element.remove();
                        }
                    });
                }
            }
        };
    })();

    //init event handler registeration
    (function () {
        Dazzling.page.registerAction('全部展开', function () {$(".dd").nestable("expandAll");}, "fa-expand");
        Dazzling.page.registerAction('全部折叠', function () {$(".dd").nestable("collapseAll");}, "fa-compress");
        $("#addTop").click(operator.addSideMenuConfig);
        $("#saveTop").click(operator.saveHeaderMenuConfig);
        $("#addSide").click(operator.addMenuItem);
        $("#saveSide").click(operator.saveSideMenuConfig);
        HeaderNestable.onItemClick = operator.loadSidebarMenuByIndex;
    })();

    operator.loadMenus();
});