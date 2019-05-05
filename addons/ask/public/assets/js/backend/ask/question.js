define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ask/question/index',
                    add_url: 'ask/question/add',
                    edit_url: 'ask/question/edit',
                    del_url: 'ask/question/del',
                    multi_url: 'ask/question/multi',
                    table: 'ask_question',
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
                        {field: 'user_id', title: __('User_id'), formatter: Table.api.formatter.search},
                        {field: 'user.nickname', title: __('Nickname'), operate: false},
                        {field: 'category_id', title: __('Category_id'), formatter: Table.api.formatter.search},
                        {field: 'category.name', title: __('Category_name'), operate: false},
                        {field: 'title', title: __('Title'), width: "200"},
                        {field: 'price', title: __('Price'), operate: 'BETWEEN'},
                        {field: 'flag', title: __('Flag'), searchList: {"index": __('Index'), "hot": __('Hot'), "recommend": __('Recommend')}, operate: 'FIND_IN_SET', formatter: Table.api.formatter.label, visible: false},
                        {field: 'voteup', title: __('Voteup'), visible: false},
                        {field: 'votedown', title: __('Votedown'), visible: false},
                        {field: 'followers', title: __('Followers'), visible: false},
                        {field: 'views', title: __('Views'), visible: false},
                        {field: 'collections', title: __('Collections'), visible: false},
                        {field: 'thanks', title: __('Thanks'), visible: false},
                        {field: 'reports', title: __('Reports'), visible: false},
                        {field: 'answers', title: __('Answers')},
                        {field: 'comments', title: __('Comments'), visible: false},
                        {field: 'peeps', title: __('Peeps'), visible: false},
                        {field: 'best_answer_id', title: __('Best_answer_id'), visible: false},
                        {field: 'isanonymous', title: __('Isanonymous'), visible: false, visible: false},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"normal": __('Normal'), "hidden": __('Hidden'), "solved": __('Solved'), "closed": __('Closed')}, formatter: Table.api.formatter.status},
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
                url: 'ask/question/recyclebin',
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
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
                                    classname: 'btn btn-xs btn-info btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'ask/question/restore'
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'ask/question/destroy'
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on('click', '.btn-destroyall', function () {
                var that = this;
                Layer.confirm(__('Are you sure you want to truncate?'), function () {
                    Fast.api.ajax($(that).attr("href"), function () {
                        Layer.closeAll();
                        table.bootstrapTable('refresh');
                    }, function () {
                        Layer.closeAll();
                    });
                });
                return false;
            });
            $(document).on('click', '.btn-restoreall,.btn-restoreit,.btn-destroyit', function () {
                Fast.api.ajax($(this).attr("href"), function () {
                    table.bootstrapTable('refresh');
                });
                return false;
            });
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