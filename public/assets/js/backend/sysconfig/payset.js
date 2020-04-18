define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form,undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/payset/index' + location.search,
                    add_url: 'sysconfig/payset/add',
                    edit_url: 'sysconfig/payset/edit',
                    del_url: 'sysconfig/payset/del',
                    multi_url: 'sysconfig/payset/multi',
                    table: 'pay_set',
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
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'team_id', title: __('Team_id'),operate: false,visible:false},
                        {field: 'team_name', title: __('Team_name'),operate: false,visible:false},
                        {field: 'type', title: __('Type'),operate: false,formatter:function (value,row,index) {
                                if (value === 0) {return '微信支付';}
                                if (value === 1) {return '享钱支付';}
                                if (value === 2) {return '如意支付';}
                            }},
                        {field: 'pay_channel', title: __('Pay_channel'),operate: false},
                        {field: 'is_multiple', title: __('Is_multiple'),editable:{
                                type: 'select',
                                source: [
                                    {value: 0, text: '禁用'},
                                    {value: 1, text: '启用'},
                                ]
                            }},
                        {field: 'count', title: __('Count'),operate: false},
                        {field: 'status', title: __('Status'),operate: false,editable:{
                                type: 'select',
                                source: [
                                    {value: 0, text: '禁用'},
                                    {value: 1, text: '启用'},
                                ]
                            }},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible: false},
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
                url: 'sysconfig/payset/recyclebin' + location.search,
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
                                    url: 'sysconfig/payset/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sysconfig/payset/destroy',
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