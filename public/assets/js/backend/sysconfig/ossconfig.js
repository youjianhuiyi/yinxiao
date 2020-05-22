define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysconfig/ossconfig/index' + location.search,
                    add_url: 'sysconfig/ossconfig/add',
                    edit_url: 'sysconfig/ossconfig/edit',
                    del_url: 'sysconfig/ossconfig/del',
                    multi_url: 'sysconfig/ossconfig/multi',
                    table: 'oss_config',
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
                        {field: 'id', title: __('Id'),operate: false,visible: false},
                        {field: 'team_id', title: __('Team_id'),operate: false,visible: false},
                        {field: 'name', title: '备注名称',operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'access_key_id', title: __('Access_key_id'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'access_key_secret', title: __('Access_key_secret'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'endpoint', title: __('Endpoint'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'bucket', title: __('Bucket'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'object', title: __('Object'),operate: false,visible:false},
                        {field: 'type', title: __('Type'),searchList: {"0": "全平台", "1": "非全平台"},formatter:function (value,row,index) {
                                if (value ===0){return '<span class="label bg-green">全平台</span>';}
                                if (value ===1){return '<span class="label bg-red">非全平台</span>';}
                            }},
                        {field: 'status', title: __('Status'),searchList: {"0": "可用", "1": "不可用"},formatter:function (value,row,index) {
                                if (value ===0){return '<span class="label bg-green">可用</span>';}
                                if (value ===1){return '<span class="label bg-red">不可用</span>';}
                            }},
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