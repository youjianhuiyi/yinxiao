define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/kzdomain/index' + location.search,
                    add_url: 'sysconfig/kzdomain/add',
                    edit_url: 'sysconfig/kzdomain/edit',
                    del_url: 'sysconfig/kzdomain/del',
                    multi_url: 'sysconfig/kzdomain/multi',
                    table: 'kz_domain',
                }
            });

            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                exportTypes:['excel'],
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'domain_url', title: __('Domain_url')},
                        {field: 'count', title: __('Count'),operate: false,visible: false},
                        {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                        {field: 'team_name', title: __('Team_name'),operate: false,visible:false},
                        {field: 'is_rand', title: __('Is_rand'),formatter: function (value,row,index) {
                            if (value == 0) {return "随机";}
                            if (value == 1) {return "固定";}
                            }},
                        {field: 'status', title: __('Status'),searchList: {"0":"未使用","1": "正在使用", "2": "已封"},formatter: function (value,row,index) {
                                if (value == 0){return '<span class="label bg-green">未使用</span>';}
                                if (value == 1){return '<span class="label bg-red">正在使用</span>';}
                                if (value == 1){return '<span class="label bg-yellow-active">已封</span>';}
                            },},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible: false},
                        {field: 'forbiddentime', title: __('Forbiddentime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                url: 'sysconfig/kzdomain/recyclebin' + location.search,
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
                                    url: 'sysconfig/kzdomain/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'sysconfig/kzdomain/destroy',
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