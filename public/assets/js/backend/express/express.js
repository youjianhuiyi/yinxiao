define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'express/express/index' + location.search,
                    add_url: 'express/express/add',
                    edit_url: 'express/express/edit',
                    del_url: 'express/express/del',
                    download_url: 'express/express/template',
                    import_url: 'express/express/import',
                    multi_url: 'express/express/multi',
                    table: 'express',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                showToggle:false,
                showExport:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'order_id', title: __('Order_id'),operate:false,visible:false},
                        {field: 'order_sn', title: __('Order_sn'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'express_no', title: __('Express_no'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'phone', title: __('Phone'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'express_com', title: __('Express_com')},
                        {field: 'is_send', title: __('Is_send'),searchList:{"0":"未发送","1":"已发送"},formatter: function (value,data,index) {
                                if (value == 0) {return '<span class="label bg-red">未发送</span>'}
                                if (value == 1) {return '<span class="label bg-green">已发送</span>'}
                            }},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
            // 启动和暂停按钮
            $(document).on("click", ".btn-start,.btn-pause", function () {
                //在table外不可以使用添加.btn-change的方法
                //只能自己调用Table.api.multi实现
                //如果操作全部则ids可以置为空
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});