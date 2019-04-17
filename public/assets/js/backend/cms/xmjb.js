define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/xmjb/index',
                    add_url: 'cms/xmjb/add',
                    edit_url: 'cms/xmjb/edit',
                    del_url: 'cms/xmjb/del',
                    import_url: 'cms/xmjb/import',
                    multi_url: 'cms/xmjb/multi',
                    table: 'cms_xmjb',
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
                        // {field: 'parent_id', title: __('Parent_id')},
                        {field: 'parent_name', title: __('Parent_id'),operate : false},
                        {field: 'grade_name', title: __('Grade_name'),operate : 'like %...%'},
                        {field: 'grade_tname', title: __('Grade_tname'),operate : 'like %...%'},
                        {field: 'grade_fname', title: __('Grade_fname'),operate : 'like %...%'},
                        {field: 'grade_type', title: __('Grade_type'), searchList: {"1":__('一级'),"2":__('二级'),"3":__('三级')}, formatter: Table.api.formatter.normal},
                        {field: 'myorder', title: __('Myorder')},
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