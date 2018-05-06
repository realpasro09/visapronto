var mainControllers = angular.module('myApp.mainControllers', []);

mainControllers.controller('formController', ['$scope', '$http', function ($scope, $http) {

  $scope.send_email = function(){
    console.log('Entered send_email().... ');

    const url = 'smart-form/contact/php/smartprocess.php';
    const emailData = {
        sendername: $scope.sendername,
        emailaddress: $scope.emailaddress,
        sendersubject: $scope.sendersubject,
        sendermessage: $scope.sendermessage,
    };

    console.log('BEFORE POST REQUEST.... ', emailData);
    $http.post(url, emailData)
        .then(function(response){
            console.log(' 200 OK >> ', response);
        }).catch(function onError(error) {
           console.log('404 ERROR... ', error);
    });
  }
}]);
