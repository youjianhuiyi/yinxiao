define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            //
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'data/summary/index',
                    add_url: 'data/summary/add',
                    edit_url: 'data/summary/edit',
                    del_url: 'data/summary/del',
                    multi_url: 'data/summary/multi',
                    table: '',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
                searchFormTemplate: 'customformtpl',
                search:false,
                showExport:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: '编号', operate: false},
                        {field: 'team_name', title: "团队名称",visible:false},
                        {field: 'pid', title: "组长"},
                        {field: 'admin_name', title: "业务员"},
                        {field: 'visit', title:"访问量"},
                        {field: 'order', title: "下单量"},
                        {field: 'done', title: "成交量"},
                        {field: 'createtime', title: __('Create time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
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