define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form,undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/wxdomain/index' + location.search,
                    add_url: 'sysconfig/wxdomain/add',
                    edit_url: 'sysconfig/wxdomain/edit',
                    del_url: 'sysconfig/wxdomain/del',
                    multi_url: 'sysconfig/wxdomain/multi',
                    table: 'wechat_domain',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'pay_id', title: __('Pay_id'),operate:false,visible:false},
                        {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                        {field: 'domain', title: __('Domain')},
                        {field: 'type', title: __('Type'),editable:{
                                type: 'select',
                                // pk: id,
                                source: [
                                    {value: 0, text: '授权'},
                                    {value: 1, text: '支付'},
                                ]
                            }},
                        {field: 'is_inuse', title: __('Is_inuse'),editable:{
                                type: 'select',
                                // pk: id,
                                source: [
                                    {value: 0, text: '未使用'},
                                    {value: 1, text: '使用中'},
                                ]
                            }},
                        {field: 'is_forbidden', title: __('Is_forbidden'),editable: {
                                type: 'select',
                                // pk: id,
                                source: [
                                {value: 0, text: '正常'},
                                {value: 1, text: '被封'},
                            ]
                            }},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'sysconfig/wxdomain/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'sysconfig/wxdomain/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sysconfig/wxdomain/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
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