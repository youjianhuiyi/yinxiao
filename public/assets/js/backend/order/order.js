define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var show_column = Config.show_column;
    var admin_level = Config.admin_level;
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
            if (admin_level == 0) {
                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    search:false,
                    showColumns: show_column,
                    showExport:show_column,
                    exportTypes:["excel"],
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'sn', title: __('Sn')},
                            {field: 'transaction_id', title: __('Transaction_id'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                            {field: 'team_name_text', title: __('Team_name'),operate:false},
                            {field: 'name', title: __('Name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'phone', title: __('Phone'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'production_id', title: __('Production_id'),operate:false,visible:false},
                            {field: 'production_name', title: __('Production_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'address', title: __('Address'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'goods_info', title: __('Goods_info'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'price', title: __('Price'),operate: 'LIKE %...%',placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'num', title: __('Num')},
                            {field: 'admin_id', title: __('Admin_id'),operate:false,visible:false},
                            {field: 'admin_name', title: __('Admin_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'pid', title: __('pid'),operate:false,visible:false},
                            {field: 'pid_text', title: __('上级'),operate:false},
                            {field: 'express_com', title: __('express_com'),operate:false},
                            {field: 'express_no', title: __('express_no'),operate:false},
                            {field: 'openid', title: __('openid'),operate:false,visible:false},
                            {field: 'pay_id', title: __('pay_id'),operate:false,visible:false},
                            {field: 'order_ip', title: __('order_ip'),operate:false,visible:false},
                            {field: 'xdd_trade_no', title: '享钱单号',operate:false},
                            {field: 'ry_order_no', title: '如意单号',operate:false},
                            {field: 'pay_type', title: __('Pay_type'),searchList: {"0":"微信支付", "1": "其他支付"},visible:false,formatter:function (value,row,index) {
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
                                    "1":"退货",
                                    "2":"补货",
                                    "3":"退款",
                                    "4":"退货退款",
                                },formatter:function (value,row,index) {
                                    if (value ===0){return '正在出库中';}
                                    if (value ===1){return '退货';}
                                    if (value ===2){return '补货';}
                                    if (value ===3){return '退款';}
                                    if (value ===4){return '退货退款';}
                                }},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                            {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                                buttons:[
                                    {
                                        name: 'detail',
                                        text: '订单详情',
                                        title: '订单详情',
                                        classname: 'btn btn-xs btn-primary btn-dialog',
                                        icon: 'fa fa-list',
                                        url: 'order/order/detail',
                                        callback: function (data) {
                                            Layer.alert("接收到回传数据：", {title: "回传数据"});
                                        }
                                    }
                                ]

                            }
                        ]
                    ]
                });
            } else if (admin_level === 1) {
                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    search:false,
                    showColumns: show_column,
                    showExport:show_column,
                    exportTypes:["excel"],
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'sn', title: __('Sn')},
                            {field: 'transaction_id', title: __('Transaction_id'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                            {field: 'team_name', title: __('Team_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符',visible:false},
                            {field: 'name', title: __('Name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'production_id', title: __('Production_id'),operate:false,visible:false},
                            {field: 'production_name', title: __('Production_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'goods_info', title: __('Goods_info'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'price', title: __('Price'),operate: 'LIKE %...%',placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'num', title: __('Num')},
                            {field: 'admin_id', title: __('Admin_id'),operate:false,visible:false},
                            {field: 'admin_name', title: __('Admin_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'pid', title: __('pid'),operate:false,visible:false},
                            {field: 'pid_text', title: __('上级'),operate:false},
                            {field: 'express_com', title: __('express_com'),operate:false,visible:false},
                            {field: 'express_no', title: __('express_no'),operate:false,visible:false},
                            {field: 'openid', title: __('openid'),operate:false,visible:false},
                            {field: 'pay_id', title: __('pay_id'),operate:false,visible:false},
                            {field: 'order_ip', title: __('order_ip'),operate:false,visible:false},
                            {field: 'xdd_trade_no', title: '享钱单号',operate:false,visible:false},
                            {field: 'ry_order_no', title: '如意单号',operate:false,visible:false},
                            {field: 'pay_type', title: __('Pay_type'),searchList: {"0":"微信支付", "1": "其他支付"},visible:false,formatter:function (value,row,index) {
                                    if (value ===0){return '微信支付';}
                                    if (value ===1){return '享钱支付';}
                                    if (value ===2){return '如意支付';}
                                }},
                            {field: 'pay_status', title: __('Pay_status'),searchList: {"1": "已付款", "0": "未付款"},formatter:function (value,row,index) {
                                    if (value ===0){return '未付款';}
                                    if (value ===1){return '已付款';}
                                }},
                            {field: 'order_status', title: __('Order_status'),searchList:{
                                    "0":"正在出库中",
                                    "1":"退货",
                                    "2":"补货",
                                    "3":"退款",
                                    "4":"退货退款"
                                },formatter:function (value,row,index) {
                                    if (value ===0){return '正在出库中';}
                                    if (value ===1){return '退货';}
                                    if (value ===2){return '补货';}
                                    if (value ===3){return '退款';}
                                    if (value ===4){return '退货退款';}
                                }},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                            {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                                buttons:[
                                    {
                                        name: 'detail',
                                        text: '订单详情',
                                        title: '订单详情',
                                        classname: 'btn btn-xs btn-primary btn-dialog',
                                        icon: 'fa fa-list',
                                        url: 'order/order/detail',
                                        callback: function (data) {
                                            Layer.alert("接收到回传数据：", {title: "回传数据"});
                                        }
                                    }
                                ]

                            }
                        ]
                    ]
                });
            } else {
                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    search:false,
                    showColumns: show_column,
                    showExport:show_column,
                    exportTypes:["excel"],
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'sn', title: __('Sn')},
                            {field: 'transaction_id', title: __('Transaction_id'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                            {field: 'team_name', title: __('Team_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符',visible:false},
                            {field: 'name', title: __('Name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'production_id', title: __('Production_id'),operate:false,visible:false},
                            {field: 'production_name', title: __('Production_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'goods_info', title: __('Goods_info'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'price', title: __('Price'),operate: 'LIKE %...%',placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'num', title: __('Num')},
                            {field: 'admin_id', title: __('Admin_id'),operate:false,visible:false},
                            {field: 'admin_name', title: __('Admin_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                            {field: 'pid', title: __('pid'),operate:false,visible:false},
                            {field: 'pid_text', title: __('上级'),operate:false},
                            {field: 'express_com', title: __('express_com'),operate:false,visible:false},
                            {field: 'express_no', title: __('express_no'),operate:false,visible:false},
                            {field: 'openid', title: __('openid'),operate:false,visible:false},
                            {field: 'pay_id', title: __('pay_id'),operate:false,visible:false},
                            {field: 'order_ip', title: __('order_ip'),operate:false,visible:false},
                            {field: 'xdd_trade_no', title: __('xdd_trade_no'),operate:false,visible:false},
                            {field: 'ry_order_no', title: __('ry_order_no'),operate:false,visible:false},
                            {field: 'pay_type', title: __('Pay_type'),searchList: {"0":"微信支付", "1": "其他支付"},visible:false,formatter:function (value,row,index) {
                                    if (value ===0){return '微信支付';}
                                    if (value ===1){return '享钱支付';}
                                    if (value ===2){return '如意支付';}
                                }},
                            {field: 'pay_status', title: __('Pay_status'),searchList: {"1": "已付款", "0": "未付款"},formatter:function (value,row,index) {
                                    if (value ===0){return '未付款';}
                                    if (value ===1){return '已付款';}
                                }},
                            {field: 'order_status',title: __('Order_status'),searchList: {
                                    "0":"正在出库中",
                                    "1":"退货",
                                    "2":"补货",
                                    "3":"退款",
                                    "4":"退货退款"
                                },formatter:function (value,row,index) {
                                    if (value ===0){return '正在出库中';}
                                    if (value ===1){return '退货';}
                                    if (value ===2){return '补货';}
                                    if (value ===3){return '退款';}
                                    if (value ===4){return '退货退款';}
                                }},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                            {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                                buttons:[
                                    {
                                        name: 'detail',
                                        text: '订单详情',
                                        title: '订单详情',
                                        classname: 'btn btn-xs btn-primary btn-dialog',
                                        icon: 'fa fa-list',
                                        url: 'order/order/detail',
                                        callback: function (data) {
                                            Layer.alert("接收到回传数据：", {title: "回传数据"});
                                        }
                                    }
                                ]
                            },
                        ]
                    ]
                });
            }


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
                url: 'order/order/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
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
                                    url: 'order/order/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'order/order/destroy',
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