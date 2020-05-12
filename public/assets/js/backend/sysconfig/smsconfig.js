define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/smsconfig/index' + location.search,
                    add_url: 'sysconfig/smsconfig/add',
                    edit_url: 'sysconfig/smsconfig/edit',
                    del_url: 'sysconfig/smsconfig/del',
                    multi_url: 'sysconfig/smsconfig/multi',
                    table: 'sms_config',
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
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'team_id', title: __('Team_id'),visible: false},
                        {field: 'username', title: __('Username')},
                        {field: 'password', title: __('Password'),operate: false,visible: false},
                        {field: 'app_uid', title: __('App_uid'),operate: false,visible: false},
                        {field: 'app_key', title: __('App_key'),operate: false,visible: false},
                        {field: 'code', title: __('Code'),operate: false,visible: false},
                        {field: 'server_id', title: __('Server_id'),operate: false,visible: false},
                        {field: 'ip', title: __('Ip')},
                        {field: 'port', title: __('Port'),operate: false},
                        {field: 'template_1', title: __('Template_1'),operate: false},
                        {field: 'api_url', title: __('Api_url'), visible: false,operate: false},
                        {field: 'status', title: __('Status'),searchList: {
                                "0":"正常",
                                "1":"关闭",
                            },formatter:function (value,row,index) {
                                if (value == 0){return '正常';}
                                if (value == 1){return '关闭';}
                            }
                        },
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
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