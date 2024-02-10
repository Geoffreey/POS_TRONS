window.angularApp.controller("InvoiceController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
    "InvoiceInfoEditModal",
    "PaymentOnlyModal",
    "SellReturnModal",
    "InstallmentViewModal",
    "EmailModal", 
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce,
    InvoiceInfoEditModal,
    PaymentOnlyModal,
    SellReturnModal,
    InstallmentViewModal,
    EmailModal
) {
    "use strict";

    var dt = $("#invoice-invoice-list");
    var i;

    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    var $type = window.getParameterByName("type");
    var $from = window.getParameterByName("from");
    var $to = window.getParameterByName("to");
    var $customerId = window.getParameterByName("customer_id");
    var $currier = window.getParameterByName("currier");
    var $estadoEnvio = window.getParameterByName("estadoEnvio");
    var $social = window.getParameterByName("social");

    var sendChangeEE = ()=>{

    }

    //================
    // Start datatable
    //================

    $("#invoice-invoice-list").dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/invoice.php?from="+$from+"&to="+$to+"&type="+$type+"&customer_id="+$customerId+"&currier="+$currier+"&estadoEnvio="+$estadoEnvio+"&social="+$social,
        "fixedHeader": true,
        "order": [[ 0, "desc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"targets": [1, 7, 8, 9, 10], "orderable": false},
            {"className": "text-center", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]},
            { "visible": false,  "targets": hideColumsArray},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(6)").html());
                }
            },
            { 
                "targets": [7],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(7)").html());
                }
            },
            { 
                "targets": [8],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(8)").html());
                }
            },
            { 
                "targets": [9],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(9)").html());
                }
            },
            { 
                "targets": [10],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(10)").html());
                }
            },
            { 
                "targets": [11],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(11)").html());
                }
            },
            { 
                "targets": [12],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#invoice-invoice-list thead tr th:eq(11)").html());
                }
            },
        ],
        "aoColumns": [
            {data : "invoice_id"},
            {data : "created_at"},
            {data : "customer_name"},
            {data : "currier"},
            {data : "estadoEnvio"},
            {data : "social"},
            {data : "amount"},
            {data : "status"},
            {data : "btn_pay"},
            {data : "btn_return"},
            {data : "btn_view"},
            {data : "btn_edit"},
            {data : "btn_delete"}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var pageTotal;
            var api = this.api();
            // Elimine el formato para obtener datos enteros para la suma
            var intVal = function ( i ) {
                return typeof i === "string" ?
                    i.replace(/[\$,]/g, "")*1 :
                    typeof i === "number" ?
                        i : 0;
            };
            // Total over all pages at column 6
            pageTotal = api
                .column( 6, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 6 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );
        },
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",footer: 'true',
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: "Invoice List-"+from+" to "+to,
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .append(
                            '<div><b><i>Powered by: web.ferrocasa.pw</i></b></div>'
                        )
                        .prepend(
                            '<div class="dt-print-heading"><img class="logo" src="'+window.logo+'"/><h2 class="title">'+window.store.name+'</h2><p>Printed on: '+window.formatDate(new Date())+'</p></div>'
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                },
                exportOptions: {
                    columns: [ 0, 1, 2, 3 ]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Invoice List-"+from+" to "+to,
                exportOptions: {
                    columns: [ 0, 1, 2, 3 ]
                }
            },
            {
                extend:    "excelHtml5",
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > Invoice List-"+from+" to "+to,
                exportOptions: {
                    columns: [ 0, 1, 2, 3 ]
                }
            },
            {
                extend:    "csvHtml5",
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > Invoice List-"+from+" to "+to,
                exportOptions: {
                    columns: [ 0, 1, 2, 3 ]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > Invoice List-"+from+" to "+to,
                exportOptions: {
                    columns: [ 0, 1, 2, 3 ]
                },
                customize: function (doc) {
                    doc.content[1].table.widths =  Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    doc.pageMargins = [10,10,10,10];
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 8;doc.styles.tableHeader.alignment = "left";
                    doc.styles.title.fontSize = 10;
                    // Eliminar espacios alrededor del título de la página
                    doc.content[0].text = doc.content[0].text.trim();
                    // Encabezado
                    doc.content.splice( 1, 0, {
                        margin: [ 0, 0, 0, 12 ],
                        alignment: 'center',
                        fontSize: 8,
                        text: 'Printed on: '+window.formatDate(new Date()),
                    });
                    // Crear un pie de página
                    doc['footer']=(function(page, pages) {
                        return {
                            columns: [
                                'Powered by web.ferrocasa.pw',
                                {
                                    // Esta es la columna de la derecha
                                    alignment: 'right',
                                    text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                }
                            ],
                            margin: [10, 0]
                        };
                    });
                    // Esta es la columna de la derecha
                    var objLayout = {};
                    // Grosor de la línea horizontal
                    objLayout['hLineWidth'] = function(i) { return 0.5; };
                    // Grosor de la línea vertical
                    objLayout['vLineWidth'] = function(i) { return 0.5; };
                    // Color de línea horizontal
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    // Color de línea vertical
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    // Relleno izquierdo de la celda.
                    objLayout['paddingLeft'] = function(i) { return 4; };
                    // Relleno derecho de la celda.
                    objLayout['paddingRight'] = function(i) { return 4; };
                    // Inyectar el objeto en el documento.
                    doc.content[1].layout = objLayout;
                }
            }
        ],
    });

    //================
    // Finalizar tabla de datos
    //================

    // Delete invoice
    $(document).delegate("#edit-invoice-info", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        var $tag = $(this);
        var $btn = $tag.button("loading");
        InvoiceInfoEditModal(d);
        setTimeout(function() {
            $tag.button("reset");
        }, 300);
    });

    // Delete invoice
    $(document).delegate("#delete-invoice", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        var $tag = $(this);
        var $btn = $tag.button("loading");
        window.swal({
          title: "Delete!",
          text: "Are You Sure?",
          icon: "warning",
          buttons: {
			cancel: true,
			confirm: true,
		  },
        })
        .then(function (willDelete) {
            if (willDelete) {
                $http({
                    method: "POST",
                    url: API_URL + "/_inc/invoice.php",
                    data: "invoice_id="+d.id+"&action_type=DELETE",
                    dataType: "JSON"
                })
                .then(function(response) {
                    dt.DataTable().ajax.reload( null, false );
                    window.swal("success!", response.data.msg, "success");
                    setTimeout(function() {
                        $tag.button("reset");
                    }, 300);
                }, function(response) {
                    window.swal("Oops!", response.data.errorMsg, "error");
                    setTimeout(function() {
                        $tag.button("reset");
                    }, 300);
                });
            } else {
                setTimeout(function() {
                    $tag.button("reset");
                }, 300);
            }
        });
    });

    // Pago de
    $(document).delegate("#pay_now", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        var $tag = $(this);
        var $btn = $tag.button("loading");
        $http({
          url: window.baseUrl + "/_inc/payment.php?action_type=ORDERDETAILS&invoice_id="+d.invoice_id,
          method: "GET"
        })
        .then(function(response, status, headers, config) {
            $scope.order = response.data.order;
            $scope.order.datatable = dt;
            PaymentOnlyModal($scope);
            setTimeout(function() {
                $tag.button("reset");
            }, 300);
        }, function(response) {
           window.swal("Oops!", response.data.errorMsg, "error");
           setTimeout(function() {
                $tag.button("reset");
            }, 300);
        });
    });

    // Return From
    $(document).delegate("#return_item", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        var $tag = $(this);
        var $btn = $tag.button("loading");
        $http({
          url: window.baseUrl + "/_inc/payment.php?action_type=ORDERDETAILS&invoice_id="+d.invoice_id,
          method: "GET"
        })
        .then(function(response, status, headers, config) {
            $scope.order = response.data.order;
            $scope.order.datatable = dt;
            SellReturnModal($scope);
            setTimeout(function() {
                $tag.button("reset");
            }, 300);
        }, function(response) {
           window.swal("Oops!", response.data.errorMsg, "error");
           setTimeout(function() {
                $tag.button("reset");
            }, 300);
        });
    });

    // Ver cuota
    $(document).delegate("#view-installment-btn", "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        var $tag = $(this);
        var $btn = $tag.button("loading");
        InstallmentViewModal(d);
        setTimeout(function() {
            $tag.button("reset");
        }, 300);
    });

    $(document).delegate("#customer_id", "select2:select", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //var data = e.params.data;
        var cid=$('#customer_id').val(),currier=$('#currier').val(),estadoEnvio=$('#estadoEnvio').val(),social=$('#social').val();
        window.location = window.baseUrl+"/admin/invoice.php?customer_id="+cid+"&currier="+currier+"&estadoEnvio="+estadoEnvio+"&social="+social;
    });
    $(document).delegate("#currier", "select2:select", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //var data = e.params.data;
        var cid=$('#customer_id').val(),currier=$('#currier').val(),estadoEnvio=$('#estadoEnvio').val(),social=$('#social').val();
        window.location = window.baseUrl+"/admin/invoice.php?customer_id="+cid+"&currier="+currier+"&estadoEnvio="+estadoEnvio+"&social="+social;
    });
    $(document).delegate("#estadoEnvio", "select2:select", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //var data = e.params.data;
        var cid=$('#customer_id').val(),currier=$('#currier').val(),estadoEnvio=$('#estadoEnvio').val(),social=$('#social').val();
        window.location = window.baseUrl+"/admin/invoice.php?customer_id="+cid+"&currier="+currier+"&estadoEnvio="+estadoEnvio+"&social="+social;
    });
    $(document).delegate("#social", "select2:select", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //var data = e.params.data;
        var cid=$('#customer_id').val(),currier=$('#currier').val(),estadoEnvio=$('#estadoEnvio').val(),social=$('#social').val();
        window.location = window.baseUrl+"/admin/invoice.php?customer_id="+cid+"&currier="+currier+"&estadoEnvio="+estadoEnvio+"&social="+social;
    });

    $(document).delegate("#changeEE", "change", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        if (confirm("Seguro de cambiar el estado de envio?") == true) {
            $http({
                url: window.baseUrl + "/_inc/changeEE.php?action_type=UPDATEINVOICEINFO&invoice_id=" + d.invoice_id + "&estadoEnvio=" + e.target.value,
                method: "GET"
            })
            .then(function(response, status, headers, config) {
                
            }, function(error) {
                window.swal("Oops!", error.data.errorMsg, "error");
            });
        } 
    });

    if (window.getParameterByName('customer_id')) {
        $("#customer_id").val(window.getParameterByName('customer_id')).trigger("change");
    }
    if (window.getParameterByName('currier')) {
        $("#currier").val(window.getParameterByName('currier')).trigger("change");
    }
    if (window.getParameterByName('estadoEnvio')) {
        $("#estadoEnvio").val(window.getParameterByName('estadoEnvio')).trigger("change");
    }
    if (window.getParameterByName('social')) {
        $("#social").val(window.getParameterByName('social')).trigger("change");
    }

    // Append email button into datatable buttons
	if (window.sendReportEmail) { $(".dt-buttons").append("<button id=\"email-btn\" class=\"btn btn-default buttons-email\" tabindex=\"0\" aria-controls=\"invoice-invoice-list\" type=\"button\" title=\"Email\"><span><i class=\"fa fa-envelope\"></i></span></button>"); };
	
    // Send invoice list through email
    $("#email-btn").on( "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        dt.find("thead th:nth-child(6), thead th:nth-child(7), thead th:nth-child(8), thead th:nth-child(9), tbody td:nth-child(6), tbody td:nth-child(7), tbody td:nth-child(8), tbody td:nth-child(9), tfoot th:nth-child(6), tfoot th:nth-child(7), tfoot th:nth-child(8), tfoot th:nth-child(9)").addClass("hide-in-mail");
        var thehtml = dt.html();
        EmailModal({template: "default", subject: "Sell Invoice Listing", title:"Sell Invoice Listing", html: thehtml});
    });
}]);