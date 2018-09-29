define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'qw/hlylockednum/index',
                    add_url: 'qw/hlylockednum/add',
                    edit_url: 'qw/hlylockednum/edit',
                    del_url: 'qw/hlylockednum/del',
                    multi_url: 'qw/hlylockednum/multi',
                    table: 'qw_hlylockednum',
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
                        {field: 'bizseq', title: __('Bizseq')},
                        {field: 'telnum', title: __('Telnum')},
                        {field: 'region', title: __('Region')},
                        {field: 'idcard', title: __('Idcard')},
                        {field: 'username', title: __('Username')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'offer_id', title: __('Offer_id')},
                        {field: 'offer_name', title: __('Offer_name')},
                        {field: 'created_at', title: __('Created_at')},
                        {field: 'updated_at', title: __('Updated_at')},
                        {field: 'locked_at', title: __('Locked_at')},
                        {field: 'returncode', title: __('Returncode')},
                        {field: 'returnmessage', title: __('Returnmessage')},
                        {field: 'z_attachid', title: __('Z_attachid')},
                        {field: 'zpicurl', title: __('Zpicurl'), formatter: Table.api.formatter.url},
                        {field: 'zcode', title: __('Zcode')},
                        {field: 'zmsg', title: __('Zmsg')},
                        {field: 'f_attachid', title: __('F_attachid')},
                        {field: 'fpicurl', title: __('Fpicurl'), formatter: Table.api.formatter.url},
                        {field: 'fcode', title: __('Fcode')},
                        {field: 'fmsg', title: __('Fmsg')},
                        {field: 'm_attachid', title: __('M_attachid')},
                        {field: 'mpicurl', title: __('Mpicurl'), formatter: Table.api.formatter.url},
                        {field: 'mcode', title: __('Mcode')},
                        {field: 'mmsg', title: __('Mmsg')},
                        {field: 'picnamerpath', title: __('Picnamerpath')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
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