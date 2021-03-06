define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form,undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/xpay/index' + location.search,
                    add_url: 'sysconfig/xpay/add',
                    edit_url: 'sysconfig/xpay/edit',
                    del_url: 'sysconfig/xpay/del',
                    multi_url: 'sysconfig/xpay/multi',
                    table: 'xpay',
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
                exportTypes:['excel'],
                columns: [
                    [
                        {checkbox: true},
                        {
                            title:'序号',
                            operate:false,
                            sortable:false,
                            formatter:function(value,row,index){
                                return index+1;
                            }
                        },
                        {field: 'id', title: __('Id'),operate:false,visible: false},
                        {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                        {field: 'team_name', title: __('Team_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符',visible:false},
                        {field: 'pay_name', title: __('Pay_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'mch_code', title: __('Mch_code'),operate:false},
                        {field: 'mch_key', title: __('Mch_key'),operate: false},
                        {field: 'api_url', title: __('Api_url'), formatter: Table.api.formatter.url,operate: false},
                        {field: 'status', title: __('Status'), operate:false,editable:{
                                type: 'select',
                                source: [
                                    {value: 0, text: '禁用'},
                                    {value: 1, text: '启用'},
                                ]
                            }},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons:[
                                {
                                    name: '支付通道测试',
                                    text: '支付通道测试',
                                    title: '支付通道测试',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'sysconfig/xpay/url',
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：", {title: "回传数据"});
                                    }
                                }
                            ]

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