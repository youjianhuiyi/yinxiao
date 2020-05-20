define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/sharedata/index' + location.search,
                    add_url: 'order/sharedata/add',
                    edit_url: 'order/sharedata/edit',
                    del_url: 'order/sharedata/del',
                    multi_url: 'order/sharedata/multi',
                    table: 'share_data',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                showColumns:false,
                showToggle:false,
                exportTypes:["excel"],
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'sn', title: __('Sn'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'team_id', title: __('Team_id'),operate: false,visible:false},
                        {field: 'team_name_text', title: __('Team_name'),operate:false,visible:false},
                        {field: 'admin_id', title: __('Admin_id'),operate: false,visible:false},
                        {field: 'admin_id_text', title: "业务员",operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'pid', title: __('Pid'),operate: false,visible:false},
                        {field: 'pid_text', title: '上级',operate:false},
                        {field: 'production_id', title: __('Production_id'),operate: false,visible:false},
                        {field: 'production_name', title: __('Production_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'date', title: __('Date'),operate: false,visible:false},
                        {field: 'name', title: __('Name')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'address', title: __('Address'),visible: false,operate: false},
                        {field: 'goods_info', title: __('Goods_info'),visible: false,operate: false},
                        {field: 'share_code', title: __('Share_code'),operate: false,visible:false},
                        {field: 'send_status', title: __('Send_status'),searchList: {"0":"发送失败", "1": "发送成功"},formatter:function (value,row,index) {
                                if (value ===0){return '<span class="bg-red label">发送失败</span>';}
                                if (value ===1){return '<span class="bg-green label">发送成功</span>';}
                            }},
                        {field: 'comment', title: __('Comment'),operate: false,visible:false},
                        {field: 'summary_status', title: __('Summary_status'),operate: false,visible:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), addclass:'datetimerange', formatter: Table.api.formatter.datetime,operate:false,visible: false},
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