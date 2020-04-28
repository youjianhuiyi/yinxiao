define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/record/index' + location.search,
                    add_url: 'sysconfig/record/add',
                    edit_url: 'sysconfig/record/edit',
                    del_url: 'sysconfig/record/del',
                    multi_url: 'sysconfig/record/multi',
                    table: 'pay_record',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                showExport:false,
                showToggle:false,
                showColumns:false,
                columns: [
                    [
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'team_id', title: __('Team_id'),operate: false,visible:false},
                        {field: 'date', title: __('Date'),operate: 'LIKE %...%', placeholder:'使用日期格式，如05-01，06-24等搜索'},
                        {field: 'pay_id', title: __('Pay_id'),operate: false,visible: false},
                        {field: 'pay_id_text', title: '商户名称',operate: false,formatter: function (value,row,index) {
                                if (value ===0){return '微信支付';}
                                if (value ===1){return '享钱支付';}
                                if (value ===2){return '如意支付';}
                            }},
                        {field: 'pay_type', title: __('Pay_type'),searchList: {"0":"微信支付", "1": "享钱支付","2":"如意支付"},formatter: function (value,row,index) {
                                if (value ===0){return '微信支付';}
                                if (value ===1){return '享钱支付';}
                                if (value ===2){return '如意支付';}
                            }},
                        {field: 'check_code', title: __('Check_code'),operate: false,visible: false},
                        {field: 'use_count', title: __('Use_count')},
                        {field: 'pay_nums', title: __('Pay_nums')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible: false},
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
                url: 'sysconfig/record/recyclebin' + location.search,
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
                                    url: 'sysconfig/record/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sysconfig/record/destroy',
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