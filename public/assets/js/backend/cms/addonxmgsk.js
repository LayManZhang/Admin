define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/addonxmgsk/index',
                    add_url: 'cms/addonxmgsk/add',
                    edit_url: 'cms/addonxmgsk/edit',
                    del_url: 'cms/addonxmgsk/del',
                    multi_url: 'cms/addonxmgsk/multi',
                    table: 'cms_addonxmgsk',
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
                        {field: 'content', title: __('Content')},
                        {field: 'leader', title: __('Leader')},
                        {field: 'fields', title: __('Fields')},
                        {field: 'project_name', title: __('Project_name')},
                        {field: 'Amount', title: __('Amount')},
                        {field: 'ssxm', title: __('Ssxm')},
                        {field: 'year', title: __('Year'), searchList: {"2013":__('Year 2013'),"2014":__('Year 2014'),"2015":__('Year 2015'),"2016":__('Year 2016'),"2017":__('Year 2017'),"2018":__('Year 2018'),"2019":__('Year 2019'),"2020":__('Year 2020')}, formatter: Table.api.formatter.normal},
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