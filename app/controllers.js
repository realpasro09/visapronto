var mainControllers = angular.module('myApp.mainControllers', []);

mainControllers.controller('formController', ['$scope', '$http', function ($scope, $http) {

  $scope.send_email = function(){
    const url = 'smart-form/contact/php/smartprocess.php';
    const emailData = {
        sendername: $scope.sendername || '',
        emailaddress: $scope.emailaddress || '',
        sendersubject: $scope.sendersubject || '',
        sendermessage: $scope.sendermessage || '',
    };

    $http.post(url, emailData)
        .then(function(response){
            $('.form-footer').removeClass('progress');
            $('#smart-form').resetForm();

            var txtswap = $(".form-footer button[type='submit']");
            txtswap.text(txtswap.data("btntext-original"));
        }).catch(function onError(error) {
           console.log('ERROR >> ', error);
    });
  }
}]);
