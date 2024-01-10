window.angularApp.controller("ReportSupplierSellController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce
) {
    "use strict";

    var dt = $("#report-report-list");
    
    var $from = window.getParameterByName("from");
    var $to = window.getParameterByName("to");
    var $supid = window.getParameterByName("sup_id");

    var printColumns = dt.data("print-columns");
    var i;
    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    dt.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/report_sell_the_supplierwise.php?from="+$from+"&to="+$to+"&supid="+$supid,
        "fixedHeader": true,
        "order": [[ 0, "desc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [3, 4, 5, 6, 7, 8, 9]},
            {"className": "text-center", "targets": [0]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(6)").html());
                }
            },
            { 
                "targets": [7],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(7)").html());
                }
            },
            { 
                "targets": [8],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(8)").html());
                }
            },
            { 
                "targets": [9],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report-report-list-list thead tr th:eq(9)").html());
                }
            },
        ],
        "aoColumns": [
            {data : "id"},
            {data : "invoice_id"},
            {data : "selling_date"},
            {data : "sup_name"},
            {data : "total_item"},
            {data : "purchase_price"},
            {data : "sell_price"},
            {data : "tax"},
            {data : "discount"},
            {data : "profit"}
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

            // Total over all pages at column 5
            pageTotal = api
                .column( 5, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 5 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

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

            // Total over all pages at column 7
            pageTotal = api
                .column( 7, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 7 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over all pages at column 8
            pageTotal = api
                .column( 8, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 8 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over all pages at column 9
            pageTotal = api
                .column( 9, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 9 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );
        },
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",footer: 'true',
                footer: true,
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: "Sell Report (Supplier wise)-"+from+" to "+to,
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
                    columns: [printColumns]
                }
            },
            {
                extend:    "copyHtml5",
                footer: true,
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Sell Report (Supplier wise)-"+from+" to "+to,
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "excelHtml5",
                footer: true,
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > Sell Report (Supplier wise)-"+from+" to "+to,
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "csvHtml5",
                footer:     true,
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > Sell Report (Supplier wise)-"+from+" to "+to,
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "pdfHtml5",
                footer: true,
                download: "open",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                title: window.store.name + " > Sell Report (Supplier wise)-"+from+" to "+to,
                exportOptions: {
                    columns: [printColumns]
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
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){
            $("td:first", nRow).html(iDisplayIndex +1);
            return nRow;
        },
    });
    
    //================
    // Finalizar tabla de datos
    //================
}]);