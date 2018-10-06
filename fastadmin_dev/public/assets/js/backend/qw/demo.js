define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'qw/demo/index',
                    add_url: 'qw/demo/add',
                    edit_url: 'qw/demo/edit',
                    del_url: 'qw/demo/del',
                    multi_url: 'qw/demo/multi',
                    table: 'qw_demo',
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
                        {field: 'uid', title: __('Uid')},
                        {field: 'region', title: __('Region')},
                        {field: 'offer_id', title: __('Offer_id')},
                        {field: 'price', title: __('Price')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'filter', title: __('Filter')},
                        {field: 'pageindex', title: __('Pageindex')},
                        {field: 'remark', title: __('Remark')},
                        {field: 'Idcard', title: __('Idcard')},
                        {field: 'name', title: __('Name')},
                        {field: 'avatar', title: __('Avatar')},
                        {field: 'weburl', title: __('Weburl'), formatter: Table.api.formatter.url},
                        {field: 'maritalstatus', title: __('Maritalstatus')},
                        {field: 'hobbies', title: __('Hobbies')},
                        {field: 'attachfile', title: __('Attachfile')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status')},
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