angular.module('myApp', []).controller('baseCtrl', function($scope) {
    $scope.test = "I am testing";
    $scope.events = [
        {title:"Event1", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event2", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event3", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event4", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event5", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."}
    ];

    $scope.createAccount = function(uname) {
        //we would have some sort of ajax call here if we were going this route
        //but since we aren't, this is just for show
        console.log(uname);
        $scope.user = {name : uname};
    }
});