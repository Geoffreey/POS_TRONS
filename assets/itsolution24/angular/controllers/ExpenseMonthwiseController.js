window.angularApp.controller("ExpenseMonthwiseController", [
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

    $(document).delegate("#selectMonthChange", "change", function (e) {
        e.stopPropagation();
        e.preventDefault();
        window.location.href='?month='+e.target.value;
    });
}]);