define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/researchlledger/index',
                    add_url: 'cms/researchlledger/add',
                    edit_url: 'cms/researchlledger/edit',
                    del_url: 'cms/researchlledger/del',
                    multi_url: 'cms/researchlledger/multi',
                    import_url: 'cms/researchlledger/import',
                    table: 'research_ledger',
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
                        {field: 'Date', title: __('Date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'Document_number', title: __('Document_number')},
                        {field: 'Acct_Tit', title: __('Acct_Tit')},
                        {field: 'Abstract', title: __('Abstract')},
                        {field: 'Debit_amount', title: __('Debit_amount'), operate:'BETWEEN'},
                        {field: 'Category', title: __('Category')},
                        {field: 'Credit_amount', title: __('Credit_amount'), operate:'BETWEEN'},
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