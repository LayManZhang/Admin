define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ask/order/index',
                    add_url: 'ask/order/add',
                    edit_url: 'ask/order/edit',
                    del_url: 'ask/order/del',
                    multi_url: 'ask/order/multi',
                    table: 'ask_order',
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
                        {field: 'orderid', title: __('Orderid'), formatter: Table.api.formatter.search},
                        {field: 'user_id', title: __('User_id'), formatter: Table.api.formatter.search},
                        {field: 'user.nickname', title: __('Nickname'), operate: false},
                        {field: 'type', title: __('Type'), searchList: {"question": __('Question'), "article": __('Article'), "answer": __('Answer'), "thanks": __('Thanks')}, formatter: Table.api.formatter.normal},
                        {field: 'source_id', title: __('Source_id'), formatter: Table.api.formatter.search},
                        {field: 'title', title: __('Title')},
                        {field: 'amount', title: __('Amount'), operate: 'BETWEEN'},
                        {field: 'payamount', title: __('Payamount'), operate: 'BETWEEN'},
                        {field: 'memo', title: __('Memo'), visible: false},
                        {field: 'ip', title: __('Ip')},
                        {field: 'useragent', title: __('Useragent'), visible: false},
                        {field: 'paytype', title: __('Paytype')},
                        {field: 'paytime', title: __('Paytime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"created": __('Created'), "paid": __('Paid')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });


            // 绑定TAB事件
            $('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var field = $(this).closest("ul").data("field");
                var value = $(this).data("value");
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    var filter = {};
                    if (value !== '') {
                        filter[field] = value;
                    }
                    params.filter = JSON.stringify(filter);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
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