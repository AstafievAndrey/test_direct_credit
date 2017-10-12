<div class="container" ng-app="fio">
    <div class="row" ng-controller="FioListController">
        <div class="col-sm-12">
            <table class="table" style="min-height: 275px">
                <thead class="thead-inverse">
                    <tr>
                        <th>#</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(key, value) in fio">
                        <td>{{value.ID}}</th>
                        <td><input ng-disabled="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" 
                                   ng-model="value.PROPERTY.SURNAME"
                                   class="table-input"></td>
                        <td><input ng-disabled="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" 
                                   ng-model="value.PROPERTY.NAME"
                                   class="table-input"></td>
                        <td><input ng-disabled="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" 
                                   ng-model="value.PROPERTY.LASTNAME"
                                   class="table-input"></td>
                        <td>
                            <span ng-show="value.PROPERTY.LOADSAVEFIO"
                                  class="glyphicon glyphicon-refresh"></span>
                            <span ng-show="value.PROPERTY.DISABLED" ng-click="update(key)" 
                                  class="glyphicon glyphicon-pencil"></span>
                            <span ng-hide="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" ng-click="update(key)"
                                  class="glyphicon glyphicon-ok"></span>
                        </td>
                    </tr>
                    <tr ng-if=" (fio.data.length == 0) && (!loading)">
                        <td colspan="5" align="center">
                            Нет данных
                        </td>
                    </tr>
                    <tr ng-if="loading">
                        <td colspan="5" align="center">
                            <div class="loader">Loading...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-xs-6 ">
            <pagination ng-model="pagination.iNumPage"
                ng-change="pageChanged()"
                total-items="pagination.totalItems"
                max-size="pagination.maxSize"  
                items-per-page="pagination.nPageSize"
                previous-text="&lsaquo;" next-text="&rsaquo;" 
                first-text="&laquo;" last-text="&raquo;"
                boundary-links="true">
            </pagination>
        </div>    
        <div class="col-xs-6 col-sm-1 col-sm-offset-5">
            <select ng-change="nPageSizeChanged()" ng-model="pagination.nPageSize" class="form-control" 
                    style="margin:20px 0px">
                <option value="1">1</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
</div>
<script>
var fioApp = angular.module('fio', ['ui.bootstrap']);
fioApp.controller('FioListController', function FioListController($http,$scope,$timeout) {
    
    $scope.pagination = {
        totalItems : 0,
        iNumPage : 1,
        maxSize : 5,
        nPageSize : '5'
    };
    
    $scope.fio;
    $scope.loading;
    $scope.url = 'index.php?ajax=1';
    
    $scope.update = function(key){
        if($scope.fio[key].PROPERTY.DISABLED){
            $scope.fio[key].PROPERTY.DISABLED = false;
        }else{
            $scope.fio[key].PROPERTY.LOADSAVEFIO = true;
            $timeout(function(){
                $scope.fio[key].PROPERTY.LOADSAVEFIO = false;
                $scope.fio[key].PROPERTY.DISABLED = true;
            },1000);  
        }
    }
    
    $scope.nPageSizeChanged = function() {
        $scope.pagination.iNumPage = 1;
        $scope.pageChanged();
    }
    
    $scope.pageChanged = function() {
        $scope.loading = true;
        $scope.fio = [];
        $http.post($scope.url,$scope.pagination).then(function(response) {
            if(response.status === 200 ){
                 $timeout(function(){
                    $scope.pagination.totalItems = response.data.totalItems;
                    $scope.fio = response.data.fio;
                    $scope.loading = false;
                },500);   
            }else{
                alert("Что то пошло не так");
                $scope.loading = false;
            }
        });
    };
    $scope.pageChanged();
    
});
</script>