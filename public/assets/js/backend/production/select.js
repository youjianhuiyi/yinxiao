define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form,undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'production/select/index' + location.search,
                    add_url: 'production/select/add',
                    edit_url: 'production/select/edit',
                    del_url: 'production/select/del',
                    multi_url: 'production/select/multi',
                    table: 'production_select',
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
                commonSearch:false,
                showToggle:false,
                showColumns:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'own_name', title: __('Own_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                        {field: 'team_name', title: __('Team_name'),operate:false,visible:false},
                        {field: 'sales_price', title: __('Sales_price'), operate:'BETWEEN'},
                        {field: 'discount', title: __('Discount'), operate:'BETWEEN'},
                        {field: 'true_price', title: __('True_price'), operate:'BETWEEN'},
                        {field: 'phone1', title: __('Phone1'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'phone2', title: __('Phone2'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'is_use', title: __('Is_use'), operate:false,editable:{
                                type: 'select',
                                source: [
                                    {value: 0, text: '禁用'},
                                    {value: 1, text: '启用'}
                                ]
                            }},
                        {field: 'url', title: "查看文案模板",operate: false,visible: false},
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