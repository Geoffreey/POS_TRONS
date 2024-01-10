window.angularApp.controller("PrinterController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
    "PrinterEditModal",
    "PrinterDeleteModal",
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce,
    PrinterEditModal,
    PrinterDeleteModal
) {
    "use strict";

    var dt = $("#printer-printer-list");
    var printerId;
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
        "ajax": API_URL + "/_inc/printer.php?from=" + $from + "&to=" + $to,
        "order": [[ 0, "desc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"targets": [6, 7], "orderable": false},
            {"className": "text-center", "targets": [0, 4, 5, 6, 7, 8]},
            {"visible": false,  "targets": hideColumsArray},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(7)").html());
                }
            },
            { 
                "targets": [7],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#printer-printer-list thead tr th:eq(8)").html());
                }
            },
        ],
        "aoColumns": [
            {data : "printer_id"},
            {data : "title"},
            {data : "type"},
            {data : "path"},
            {data : "ip_address"},
            {data : "port"},
            {data : "status"},
            {data : "btn_edit"},
            {data : "btn_delete"},
        ],
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",footer: 'true',
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: window.store.name + " > printers",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Printer Listing",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "excelHtml5",
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > Printer Listing",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "csvHtml5",
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > Printer Listing",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > Printer Listing",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
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

    // Create printer
    $(document).delegate("#create-printer-submit", "click", function(e) {
        e.preventDefault();

        var $tag = $(this);
        var $btn = $tag.button("loading");
        var form = $($tag.data("form"));
        form.find(".alert").remove();
        var actionUrl = form.attr("action");
        
        $http({
            url: window.baseUrl + "/_inc/" + actionUrl,
            method: "POST",
            data: form.serialize(),
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {

            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = response.data.msg;
            window.toastr.success(alertMsg, "Success!");

            printerId = response.data.id;
            
            dt.DataTable().ajax.reload(function(json) {
                if ($("#row_"+printerId).length) {
                    $("#row_"+printerId).flash("yellow", 5000);
                }
            }, false);

            // Restablecer formulario
            $("#reset").trigger("click");
            $("#printer_sex").val(null).trigger("change");

        }, function(response) {

            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = "<div>";
            window.angular.forEach(response.data, function(value) {
                alertMsg += "<p>" + value + ".</p>";
            });
            alertMsg += "</div>";
            window.toastr.warning(alertMsg, "Warning!");
        });
    });

    // Edit printer
    $(document).delegate("#edit-printer", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        $scope.printer = d;
        PrinterEditModal($scope);
    });

    // Delete printer
    $(document).delegate("#delete-printer", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        // Alert
        window.swal({
          title: "Delete!",
          text: "Are you sure?",
          icon: "warning",
          buttons: true,
          dangerMode: false,
        })
        .then(function(willDelete) {
            if (willDelete) {
                $http({
                    method: "POST",
                    url: API_URL + "/_inc/printer.php",
                    data: "printer_id="+d.printer_id+"&action_type=DELETE",
                    dataType: "JSON"
                })
                .then(function(response) {
                    dt.DataTable().ajax.reload( null, false );
                    window.swal("success!", response.data.msg, "success");
                }, function(response) {
                    window.swal("Oops!", response.data.errorMsg, "error");
                });
            }
        });
    });

    // Abrir cuadro de diálogo modal de edición por cadena de consulta
    if (window.getParameterByName("printer_id") && window.getParameterByName("printer_name")) {
        printerId = window.getParameterByName("printer_id");
        var printerName = window.getParameterByName("printer_name");
        dt.DataTable().search(printerName).draw();
        dt.DataTable().ajax.reload(function(json) {
            $.each(json.data, function(index, obj) {
                if (obj.DT_RowId === "row_" + printerId) {
                    window.modalPrinterEdit({printer_id: printerId, printer_name: obj.printer_name});
                    return false;
                }
            });
        }, false);
    }
    $scope.printerType = 'network';
    $scope.isWindows = false;
    $scope.isLinux = false;
    $scope.isNetwork = true;
    $("#printer-type").on("select2:select", function (e) {
        e.stopImmediatePropagation();
        e.stopPropagation();
        e.preventDefault();
        var data = e.params.data;
        var isWindows = false;
        var isLinux = false;
        var isNetwork = false;

        if (data.element.value == "windows") {
            isWindows = true;
            isLinux = false;
            isNetwork = false;
        }
        if (data.element.value == "linux") {
            isWindows = false;
            isLinux = true;
            isNetwork = false;
        }
        if (data.element.value == "network") {
            isWindows = false;
            isLinux = false;
            isNetwork = true;
        }

        $scope.$apply(function () {
            $scope.isWindows = isWindows;
            $scope.isLinux = isLinux;
            $scope.isNetwork = isNetwork;
        });
    });
}]);