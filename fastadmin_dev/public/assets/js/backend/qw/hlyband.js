define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'qw/hlyband/index',
                    add_url: 'qw/hlyband/idencheckindex',
                    edit_url: 'qw/hlyband/edit',
                    del_url: 'qw/hlyband/del',
                    multi_url: 'qw/hlyband/multi',
                    table: 'qw_hlyband',
                }
            });

            var table = $("#table");
            //给添加按钮添加`data-area`属性
            $(".btn-add").data("area", ["100%", "100%"]);
            //当内容渲染完成给编辑按钮添加`data-area`属性
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-editone").data("area", ["100%", "100%"]);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        //                        {field: 'id', title: __('Id')},
                        {field: 'custname', title: __('custname')},
                        {field: 'accnbr', title: __('accnbr')},


                        //                        {field: 'icno', title: __('Icno')},
                        {field: 'contactname', title: __('contactname')},
                        {field: 'contactphone', title: __('contactphone')},
                        {field: 'orderid', title: __('orderid')},
                        {field: 'bookingid', title: __('bookingid')},
                        {field: 'region', title: __('region')},
                        //                        {field: 'countyname', title: __('Countyname')},
                        {
                            field: 'type', 
                            title: __('type'),    
                           
                        },
                        //                        {field: 'remark', title: __('Remark')},
                        {field: 'goodcode', title: __('goodcode')},
                        //                        {field: 'addressid', title: __('Addressid')},
                        {field: 'addressname', title: __('Addressname')},
                        //                        {field: 'areaid', title: __('Areaid')},
                        //                        {field: 'areaname', title: __('Areaname')},
                        //                        {field: 'networkaccess', title: __('Networkaccess')},
                        //                        {field: 'uptownid', title: __('Uptownid')},
                        //                        {field: 'created_at', title: __('Created_at')},
                        //                        {field: 'updated_at', title: __('Updated_at')},
                        //                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
                        //                        {field: 'statusMsg', title: __('Statusmsg')},
                        //                        {field: 'cancel_at', title: __('Cancel_at')},
                        //                        {field: 'cancel_reason', title: __('Cancel_reason')},
                        //                        {field: 'comments', title: __('Comments')},
                        {
                            field: 'resp_code', 
                            title: __('resp_code'),
                            table: table,
                            custom: {'0000': 'success', '-2': 'danger'},
                            formatter: Table.api.formatter.flag
                        },

                        {
                            field: 'resp_msg',
                            title: __('resp_msg'),
                            custom: {'扣款失败': 'danger'},
                            formatter: Table.api.formatter.resp_msg
                        },
                        {field: 'created_at', title: __('Created_at')},

                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ] ,
                  
                
                showToggle: false,
                
            });

            $("#cxselect-example .col-xs-12").each(function () {
                $("textarea", this).val($(this).prev().prev().html().replace(/[ ]{2}/g, ''));
            });

            //这里需要手动为Form绑定上元素事件
            Form.api.bindevent($("form#cxselectform"));



            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        address: function () {
            $("#cxselect-example .col-xs-12").each(function () {
                $("textarea", this).val($(this).prev().prev().html().replace(/[ ]{2}/g, ''));
            });

            //这里需要手动为Form绑定上元素事件
            Form.api.bindevent($("form#cxselectform"));
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});