window.angularApp.controller("ReportGastosController", [
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

    var dt = $("#loss-loss-list");
    var id = null;
    var i;

    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    var $from = window.getParameterByName("from");
    var $to = window.getParameterByName("to");

    //================
    // Start datatable
    //================

    dt.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/reporte_gastos.php?from="+$from+"&to="+$to,
        "order": [[ 0, "asc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [3, 4, 5]}, // nueva ubicación de valores monetarios
            {"className": "text-center", "targets": [0]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#loss-loss-list thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#loss-loss-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#loss-loss-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#loss-loss-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#loss-loss-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#loss-loss-list thead tr th:eq(2)").html());
                }
            },
            
        ],
        "aoColumns": [
            {data : "serial_no"},
            {data : "title"},
            {data : "fecha_gasto"},
            {data : "this_month"},
            {data : "this_year"},
            {data : "till_now"},
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
            // Total de todas las páginas en la columna 2
            pageTotal = api
                .column( 2, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 2 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over all pages at column 3
            pageTotal = api
                .column( 3, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 3 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over all pages at column 4
            pageTotal = api
                .column( 4, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Actualizar pie de página
            $( api.column( 4 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );
        },
        "pageLength": window.settings.datatable_item_limit,
        "fnRowCallback" : function(nRow, aData, iDisplayIndex){
            $("td:first", nRow).html(iDisplayIndex +1);
            return nRow;
        },
    });

    $(".dt-buttons").remove();

    //================
    // Finalizar tabla de datos
    //================
}]);