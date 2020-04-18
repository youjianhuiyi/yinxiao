define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'production/url/index' + location.search,
                    add_url: 'production/url/add',
                    edit_url: 'production/url/edit',
                    del_url: 'production/url/del',
                    multi_url: 'production/url/multi',
                    table: 'production_url',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false,visible:false},
                        {field: 'production_id', title: __('Production_id'),operate:false,visible:false},
                        {field: 'production_name', title: __('Production_name')},
                        {field: 'team_id', title: __('Team_id'),operate:false,visible:false},
                        {field: 'team_name', title: __('Team_name'),operate:false,visible:false},
                        {field: 'url', title: __('Url'), formatter: Table.api.formatter.url,operate:false,visible:false},
                        {field: 'count', title: __('Count')},
                        {field: 'order_done', title: __('Order_done')},
                        {field: 'is_forbidden', title: __('Is_forbidden'),operate:false,visible:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'detail',
                                    text: '获取商品推广链接',
                                    title: '获取商品推广链接',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'production/url/url'
                                }],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 启动和暂停按钮
            $(document).on("click", ".btn-start,.btn-pause", function () {
                //在table外不可以使用添加.btn-change的方法
                //只能自己调用Table.api.multi实现
                //如果操作全部则ids可以置为空
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
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
                url: 'production/url/recyclebin' + location.search,
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
                                    url: 'production/url/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'production/url/destroy',
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