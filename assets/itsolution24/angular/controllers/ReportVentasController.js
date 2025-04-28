window.angularApp.controller("ReportVentasController", [
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

    var profitDt = $("#profit-profit-list");
    var id = null;
    var i;

    var hideColums = profitDt.data("hide-colums").split(",");
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

    profitDt.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/reporte_venta.php?from="+$from+"&to="+$to,
        "order": [[ 0, "asc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [2, 3, 4]},
            {"className": "text-center", "targets": [0]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#profit-profit-list thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#profit-profit-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#profit-profit-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#profit-profit-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#profit-profit-list thead tr th:eq(4)").html());
                }
            },
        ],
        "aoColumns": [
            { "data": "serial_no" },   // 👈 Primera columna debe ser serial_no
            { "data": "invoice_id" },
            { "data": "created_at" },
            { "data": "title" },
            { "data": "this_month" },
            { "data": "this_year" },
            { "data": "till_now" }
        ],
        "footerCallback": function (row, data, start, end, display) {
    var api = this.api();

    // Función para limpiar el formato de números
    var intVal = function (i) {
        return typeof i === "string" ?
            i.replace(/[\$,]/g, "") * 1 :
            typeof i === "number" ?
                i : 0;
    };

    // Total this_month (columna 4)
    var totalThisMonth = api
        .column(4, { page: 'current' })
        .data()
        .reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);

    $(api.column(4).footer()).html(window.formatDecimal(totalThisMonth, 2));

    // Total this_year (columna 5)
    var totalThisYear = api
        .column(5, { page: 'current' })
        .data()
        .reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);

    $(api.column(5).footer()).html(window.formatDecimal(totalThisYear, 2));

    // Total till_now (columna 6)
    var totalTillNow = api
        .column(6, { page: 'current' })
        .data()
        .reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);

    $(api.column(6).footer()).html(window.formatDecimal(totalTillNow, 2));
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