define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'production/production/index' + location.search,
                    add_url: 'production/production/add',
                    edit_url: 'production/production/edit',
                    del_url: 'production/production/del',
                    multi_url: 'production/production/multi',
                    table: 'production',
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
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'name', title: __('Name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'module_name', title: __('module_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'sales_price', title: __('Sales_price'), operate:false},
                        {field: 'discount', title: __('Discount'), operate:false},
                        {field: 'true_price', title: __('True_price'), operate:'BETWEEN'},
                        {field: 'phone1', title: __('Phone1'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'phone2', title: __('Phone2'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'modulefile', title: __('Modulefile'),visible: false,operate:false},
                        {field: 'special_code', title: __('Special_code'),operate:false,visible:false},
                        {field: 'tongji', title: __('Tongji'),operate:false,visible:false},
                        {field: 'status', title: __('Status'),operate:false,formatter:function (value,row,index) {
                                if (value === 0 ) {return '<span class="label bg-green">正常</span>';}
                                if (value === 1 ) {return '<span class="label bg-red">停用</span>';}
                            }},
                        {field: 'count', title: __('Count'),operate: false,visible: false},
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