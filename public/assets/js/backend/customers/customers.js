define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'customers/customers/index' + location.search,
                    add_url: 'customers/customers/add',
                    edit_url: 'customers/customers/edit',
                    del_url: 'customers/customers/del',
                    multi_url: 'customers/customers/multi',
                    table: 'customers',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                showToggle:false,
                exportTypes:['excel'],
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'team_id', title: __('Team_id'),operate: false,visible:false},
                        {field: 'team_name_text', title: __('Team_name'),operate:false,visible:false},
                        {field: 'admin_id', title: __('Admin_id'),operate:false,visible: false},
                        {field: 'name', title: __('Name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'type', title: __('Type'),operate: false,visible: false},
                        {field: 'sn', title: __('Sn'),operate: false,visible: false},
                        {field: 'sex', title: __('Sex'),operate: false,visible: false},
                        {field: 'birthday', title: __('Birthday'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'address', title: __('Address'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'phone', title: __('Phone'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'phone1', title: __('Phone1'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'email', title: __('Email'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'send_status', title: __('Send_status'),searchList: {"0": "未发送","1": "已发送","2": "发送失败"},formatter:function (value,row,index) {
                                if (value == 0){return '<span class="label bg-info">未发送</span>';}
                                if (value == 1){return '<span class="label bg-green">已发送</span>';}
                                if (value == 2){return '<span class="label bg-green">发送失败</span>';}
                            }},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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