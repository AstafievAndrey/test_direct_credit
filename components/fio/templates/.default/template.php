<div class="container-fluid" ng-app="fio">
    <div class="container" ng-controller="FioListController">
        <div class="row">
            <div class="col-sm-3">
                <button ng-click="showAddForm = ((showAddForm) ? false : true) " class="btn btn-primary">
                    {{(showAddForm) ? 'Скрыть' : 'Добавить'}}
                </button>
            </div>
        </div>
        <div class="row" ng-hide="showAddForm">
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
                                       ng-model="value.PROPERTY.SURNAME" ng-pattern="/^[А-Яа-я]+$/" 
                                       class="table-input"></td>
                            <td><input ng-disabled="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" 
                                       ng-model="value.PROPERTY.FIRSTNAME" ng-pattern="/^[А-Яа-я]+$/"
                                       class="table-input"></td>
                            <td><input ng-disabled="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" 
                                       ng-model="value.PROPERTY.LASTNAME" ng-pattern="/^[А-Яа-я]+$/"
                                       class="table-input"></td>
                            <td>
                                <span ng-show="value.PROPERTY.LOADSAVEFIO"
                                      class="glyphicon glyphicon-refresh"></span>
                                <span ng-show="value.PROPERTY.DISABLED" ng-click="update(key)" 
                                      class="glyphicon glyphicon-pencil"></span>
                                <span ng-hide="value.PROPERTY.DISABLED || value.PROPERTY.LOADSAVEFIO" 
                                      ng-click="update(key,value)"
                                      class="glyphicon glyphicon-ok"></span>
                            </td>
                        </tr>
                        <tr ng-if=" (!loading) && (pagination.totalItems == 0)">
                            <td colspan="5" align="center">
                                Нет данных
                            </td>
                        </tr>
                        <tr ng-if="message.show">
                            <td colspan="5" align="center">
                                {{message.text}}
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
            <div class="col-xs-6 " ng-if=" (!loading) && (pagination.totalItems !== 0)">
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
            <div class="col-xs-6 col-sm-1 col-sm-offset-5" ng-if=" (!loading) && (pagination.totalItems !== 0)">
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
        <div class="row" ng-show="showAddForm">
            <div class="col-sm-offset-3 col-sm-6">
                <form novalidate ng-submit="sendFio(addFioForm)" name="addFioForm" id="addFio" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3">Фамилия</label>
                        <div class="col-sm-9">
                            <input ng-model="addFio.surname" pattern="^[А-Яа-я]+$"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Имя</label>
                        <div class="col-sm-9">
                            <input ng-model="addFio.firstname" 
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Отчество</label>
                        <div class="col-sm-9">
                            <input ng-model="addFio.lastname" pattern="^[А-Яа-я]+$"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">
                            <button ng-disabled="addFioForm.$invalid"
                                type="submit" class="btn btn-primary">
                                Добавить
                            </button>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
var fioApp = angular.module('fio', ['ui.bootstrap']);
fioApp.controller('FioListController', function FioListController($http,$scope,$timeout) {
    
    $scope.showAddForm = false;
    $scope.addFio = {surname : null, firstname : null, lastname : null};
    $scope.message = {show:false, text:""};
    $scope.pagination = {
        totalItems : 0,
        iNumPage : 1,
        maxSize : 5,
        nPageSize : '5'
    };
    $scope.fio;
    $scope.loading;
    $scope.url = 'index.php?ajax=1';
    
    $scope.clearMessage = function(){
        $scope.message = {show:false, text:""};
    }
    $scope.setMessage = function(text){
        $scope.message = {show:true, text:text};
        $timeout(function(){$scope.clearMessage();},2000);
    }
    
    $scope.sendFio = function(form){
        $http.post($scope.url+"&action=addFio",{
            surname: $scope.addFio.surname,
            name: $scope.addFio.firstname,
            lastname: $scope.addFio.lastname
        })
        .then(function(response) {
            if(response.status === 200 ){
                $scope.addFio = {surname : null, firstname : null, lastname : null};
                form.$setPristine();
                $scope.pageChanged();
            }else{
                alert("Что то пошло не так");
            }
        },function(){
            alert("Error action addFio");
        });
    };
    
    $scope.check = function(value){
        if( (value.PROPERTY.SURNAME.search(/^[А-Яа-я]+$/) === -1) || 
            (value.PROPERTY.FIRSTNAME.search(/^[А-Яа-я]+$/) === -1) ||
            (value.PROPERTY.LASTNAME.search(/^[А-Яа-я]+$/) === -1)){
            return false;
        }
        return true;
    }
    
    $scope.update = function(key,value){
        if($scope.fio[key].PROPERTY.DISABLED){
            $scope.fio[key].PROPERTY.DISABLED = false;
        }else{
            if($scope.check(value)){
                $scope.fio[key].PROPERTY.LOADSAVEFIO = true;
                $http.post($scope.url+"&action=updateFio",$scope.fio[key])
                .then(function(response) {
                    if(response.status !== 200 ){
                        alert("Что то пошло не так");
                    }
                    $timeout(function(){
                        $scope.fio[key].PROPERTY.LOADSAVEFIO = false;
                        $scope.fio[key].PROPERTY.DISABLED = true;
                    },1000);
                },function(){
                    alert("Error action editFio");
                });  
            }else{
                $scope.setMessage("Проверьте правильность написания ФИО");
            }
        }
    }
    
    $scope.nPageSizeChanged = function() {
        $scope.pagination.iNumPage = 1;
        $scope.pageChanged();
    }
    
    $scope.pageChanged = function() {
        $scope.loading = true;
        $scope.fio = [];
        $http.post($scope.url+"&action=list",$scope.pagination).then(function(response) {
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
        },function(){
            alert("Error action list");
        });
    };
    $scope.pageChanged();
    
});
</script>