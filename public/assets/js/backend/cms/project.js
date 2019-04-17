define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/project/index',
                    add_url: 'cms/project/add',
                    edit_url: 'cms/project/edit',
                    del_url: 'cms/project/del',
                    multi_url: 'cms/project/multi',
                    import_url: 'cms/project/import',
                    table: 'project',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'project_name', title: __('Project_name')},
                        {field: 'Completion_date', title: __('Completion_date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'Start_date', title: __('Start_date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'Budget', title: __('Budget'), operate:'BETWEEN'},
                        {field: 'Laborcosts_salary', title: __('Laborcosts_salary'), operate:'BETWEEN'},
                        {field: 'Laborcosts_si', title: __('Laborcosts_si'), operate:'BETWEEN'},
                        {field: 'Laborcosts_services', title: __('Laborcosts_services'), operate:'BETWEEN'},
                        {field: 'Directinput_materials', title: __('Directinput_materials'), operate:'BETWEEN'},
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