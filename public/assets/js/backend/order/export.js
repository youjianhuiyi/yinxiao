define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var show_column = Config.show_column;
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    add_url: 'order/order/add',
                    edit_url: 'order/order/edit',
                    del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");
                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    search:false,
                    showColumns: false,
                    showToggle:false,
                    showExport:show_column,
                    searchFormVisible:true,
                    exportTypes:["excel"],
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'production_id', title: __('Production_id'),operate:false,visible:false},
                            {field: 'production_name', title: __('Production_name'),operate: false},
                            {field: 'sn', title: __('Sn'),operate: false},
                            {field: 'transaction_id', title: __('Transaction_id'),operate: false},
                            {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                            {field: 'team_name_text', title: __('Team_name'),operate:false,visible:false},
                            {field: 'name', title: __('Name'),operate:false},
                            {field: 'phone', title: __('Phone'),operate:false},
                            {field: 'address', title: __('Address'),operate: false},
                            {field: 'goods_info', title: __('Goods_info'),operate:false},
                            {field: 'comment', title: '第二双备注',operate:false},
                            {field: 'price', title: __('Price'),operate: false},
                            {field: 'num', title: __('Num'),operate: false},
                            {field: 'admin_id', title: __('Admin_id'),operate:false,visible:false},
                            {field: 'admin_name', title: __('Admin_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'pid', title: __('pid'),operate:false,visible:false},
                            {field: 'pid_text', title: __('上级'),operate:false},
                            {field: 'express_com', title: __('express_com'),operate:false,visible:false},
                            {field: 'express_no', title: __('express_no'),operate:false,visible:false},
                            {field: 'openid', title: __('openid'),operate:false,visible:false},
                            {field: 'pay_id', title: __('pay_id'),operate:false,visible:false},
                            {field: 'order_ip', title: '下单IP',operate:false,visible:false},
                            {field: 'xdd_trade_no', title: '享钱单号',operate:false,visible:false},
                            {field: 'ry_order_no', title: '如意单号',operate:false,visible:false},
                            {field: 'pay_type', title: __('Pay_type'),searchList: {"0":"微信支付", "1": "其他支付"},operate:false,visible:false,formatter:function (value,row,index) {
                                    if (value ===0){return '微信支付';}
                                    if (value ===1){return '享钱支付';}
                                    if (value ===2){return '如意支付';}
                                }},
                            {field: 'pay_status', title: __('Pay_status'),searchList: {"1": "已付款", "0": "未付款"},formatter:function (value,row,index) {
                                    if (value ===0){return '未付款';}
                                    if (value ===1){return '已付款';}
                                }},
                            {field: 'order_status', title: __('Order_status'),searchList: {
                                    "0":"正在出库中",
                                    "1":"已发货",
                                    "2":"补货",
                                    "3":"退款",
                                    "4":"退货退款",
                                },formatter:function (value,row,index) {
                                    if (value ===0){return '正在出库中';}
                                    if (value ===1){return '已发货';}
                                    if (value ===2){return '补货';}
                                    if (value ===3){return '退款';}
                                    if (value ===4){return '退货退款';}
                                }},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
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