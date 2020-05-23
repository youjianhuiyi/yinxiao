define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'data/backup/index' + location.search,
                    add_url: 'data/backup/add',
                    edit_url: 'data/backup/edit',
                    del_url: 'data/backup/del',
                    multi_url: 'data/backup/multi',
                    table: 'backup_record',
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
                        {field: 'team_id_text', title: __('Team_id'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'admin_id', title: __('Admin_id'),operate: false,visible: false},
                        {field: 'admin_id_text', title: __('Admin_id'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'file_name', title: __('File_name'),operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'data_count', title: __('Data_count'),operate: false},
                        {field: 'date', title: __('Date'),operate: false},
                        {field: 'status', title: __('Status'),searchList: {
                                "0":"失败",
                                "1":"成功",
                        },formatter:function (value,row,index) {
                            if (value == 0){return '<span class="label bg-red">失败</span>';}
                            if (value == 1){return '<span class="label bg-green">成功</span>';}
                        }},
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