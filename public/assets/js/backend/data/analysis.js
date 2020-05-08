define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'data/analysis/index' + location.search,
                    add_url: 'data/analysis/add',
                    edit_url: 'data/analysis/edit',
                    del_url: 'data/analysis/del',
                    multi_url: 'data/analysis/multi',
                    table: 'summary_analysis',
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
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'team_id', title: __('Team_id'),operate: false,visible:false},
                        {field: 'pid', title: __('Pid'),operate: false,visible: false},
                        {field: 'admin_id', title: __('Admin_id'),operate: false,visible: false},
                        {field: 'gid', title: __('Gid'),operate: false,visible: false},
                        {field: 'date', title: __('Date'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'check_code', title: __('Check_code'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'order_sn', title: __('Order_sn'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'type', title: __('Type'),searchList: {"0":"订单量", "1": "订单商品量","2":"支付量","3":"支付商品量"},formatter:function (value,row,index) {
                            if (value == 0){return '订单量';}
                            if (value == 1){return '订单商品量';}
                            if (value == 2){return '支付量';}
                            if (value == 3){return '支付商品量';}
                        }},
                        {field: 'count', title: __('Count'),operate: 'RANGE'},
                        {field: 'data', title: __('Data'),operate: false,visible: false},
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