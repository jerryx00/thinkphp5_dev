define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'qw/hlyoffer/index',
                    add_url: 'qw/hlyoffer/add',
                    edit_url: 'qw/hlyoffer/edit',
                    del_url: 'qw/hlyoffer/del',
                    multi_url: 'qw/hlyoffer/multi',
                    table: 'qw_hlyoffer',
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'offer_id', title: __('Offer_id')},
                        {field: 'offer_name', title: __('Offer_name')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'period', title: __('Period')},
                        {field: 'tariffdesc', title: __('Tariffdesc')},
                        {field: 'marketingcampaign', title: __('Marketingcampaign')},
                        {field: 'remark', title: __('Remark')},
                        {field: 'status', title: __('Status'),formatter: Table.api.formatter.toggle},
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