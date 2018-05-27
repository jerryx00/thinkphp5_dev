define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/index',
                    add_url: 'goods/add',
                    edit_url: 'goods/edit',
                    del_url: 'goods/del',
                    multi_url: 'goods/multi',
                    table: 'goods',
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
                        {field: 'code', title: __('Code')},
                        {field: 'name', title: __('Name')},
                        {field: 'ptype', title: __('Ptype'), visible:false, searchList: {"\u6708\u5305":__('\u6708\u5305'),"\u65e5\u5305":__('\u65e5\u5305'),"7\u5929\u5305":__('7\u5929\u5305')}},
                        {field: 'ptype_text', title: __('Ptype'), operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"\u5f85\u5904\u7406":__('\u5f85\u5904\u7406'),"\u5904\u7406\u4e2d":__('\u5904\u7406\u4e2d'),"\u5f85\u786e\u8ba4":__('\u5f85\u786e\u8ba4'),"\u6210\u529f":__('\u6210\u529f'),"\u5931\u8d25":__('\u5931\u8d25')}},
                        {field: 'status_text', title: __('Status'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'created_time', title: __('Created_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'updated_time', title: __('Updated_time'), operate:'RANGE', addclass:'datetimerange'},
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