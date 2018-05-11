//Angular JS code to call ip-api
var app = angular.module('myApp', []);
var latitude;
var longitude;
var previous = [];
var data =  "";

//Code for autocomplete
app.directive('googleplace', function() {
    return {
        require : 'ngModel',
        link : function(scope, element, attrs, model) {
            var options = {
                types : [],
            };
            
//            console.log(element[0]);
            scope.gPlace = new google.maps.places.Autocomplete(element[0],options);
 
            google.maps.event.addListener(scope.gPlace, 'place_changed',
                    function() {
                        scope.$apply(function() {
                            model.$setViewValue(element.val());
                        });
                    });
        }
    };
});

function myI($scope) {
    $scope.gPlace;
}

//code for calling ip-api
app.controller("myIP", ['$scope','$http', '$compile', function ($scope,$http,$compile) {
$scope.valueDropdown = "Google Reviews";
$scope.showGoogleReviews = true;
$scope.valueSort = "Default Order";
$scope.destResult = {};
$scope.destination_latitude='';
$scope.destination_longitude='';
$scope.parameters_direction = {};
$scope.parameters_direction.from_loc='';
$scope.parameters_direction.to_loc='';
$scope.parameters_direction.mode_travel_map='DRIVING';
$scope.favoriteContainer = false;
$scope.data = {progressBar:false, directionsPanel:false, map:false};
$scope.pegman_map = 'http://cs-server.usc.edu:45678/hw/hw8/images/Pegman.png';

$scope.error = {failed:false, norecords:false, yelpError:false, googleError:false,favorite:false,photos:false};    
    
$scope.getIP = function () {
    
$scope.placesHeading = false;
   $http.get("http://ip-api.com/json")
   .then(function(response) {
       
       if(response.status="200"){
        latitude = response.data.lat;
        longitude = response.data.lon;
        console.log(response.status);
       }
      else {
//        Static usc coordinates  
        latitude = 34.0266;
        longitude = -118.2831;
        console.log(response.status);
       }
    
       $scope.buttonSubmit = false;

  });
    
};
    
    $scope.formData = {};
    
    $scope.formData.cats = [

                                  { value:"default" , name:"Default"},
                                 {value:"airport",name:"Airport"},
                                 { value:"amusement_park", name:"Amusement Park"},
                                 { value:"aquarium", name:"Aquarium"},
                                 { value:"art_gallery", name:"Art Gallery"},
                                 { value:"bakery", name:"Bakery"},

                                 { value:"bar", name:"Bar"},
                                 { value:"beauty_salon", name:"Beauty Salon"},

                                 { value:"bowling_alley", name:"Bowling Alley"},
                                 { value:"bus_station", name:"Bus Station"},
                                 { value:"cafe", name:"Cafe"},
                                 { value:"campground" , name:"Campground"},
                                 { value:"car_rental", name:"Car Rental "},
                                 { value:"casino" , name:"Casino"},
                                 { value:"lodging", name:"Lodging"},

                                 { value:"movie_theater", name:"Movie Theater"},

                                 { value:"museum", name:"Museum"},
                                 { value:"night_club", name:"Night Club"},
                                 { value:"park", name:"Park"},
                                 { value:"parking", name:"Parking"},
                                 { value:"restaurant", name:"Restaurant"},
                                 { value:"shopping_mall", name:"Shopping Mall"},

                                 { value:"stadium", name:"Stadium"},
                                 { value:"subway_station", name:"subway station"},

                                 { value:"taxi_stand", name:"Taxi Stand"},
                                 { value:"train_station", name:"train station"},

                                 { value:"transit_station", name:"Transit Station"},
                                 { value:"travel_agency", name:"Travel Agency"},
                                 { value:"zoo", name:"Zoo"}
];
    
    //default value in category list
    $scope.formData.selectcategory =  $scope.formData.cats[0];
    $scope.formData.loc = 1;
    
    
    $scope.clearAll = function(){
        $scope.formData.textkeyword = "";
        $scope.formData.textdistance = ""; 
        $scope.formData.selectcategory = $scope.formData.cats[0];
        if($scope.formData.loc == 2) {
            $scope.formData.loc=1
            $scope.formData.locText = "";
        }
        
        $scope.nearbyPlacesContainer = false;
        $scope.infoTab = false;
        $scope.data.progressBar = false;
        
        //alerts
        $scope.error.failed = false;
        $scope.error.norecords = false;
        $scope.showGoogleReviews = false;
        $scope.showYelpReviews = false;
        $scope.error.favorite = false;
        $scope.error.photos = false;

    }
    //Calling Google API, token represents next page token, dir represents direction - prev/next
    $scope.callNearbySearch = function(token,dir) {
            
            var textdistance=0;
            
            //disabling fav and infotab
            $scope.favoriteContainer = false;
            $scope.infoTab = false;
            $scope.error.failed = false;
            $scope.error.norecords = false;
            $scope.error.favorite = false;
            $scope.error.photos = false;

        
            //Checking if distance was entered or not
            if(!$scope.formData.textdistance){
                console.log("here");
                textdistance = 10;
            }
            else{
                textdistance = $scope.formData.textdistance;
            }
        

            if(!token) $scope.next=false;

            //for progress bar
            if(!$scope.nearbyPlaces && !dir) 
                $scope.data.progressBar = true;

            //when loaded for the first time or there is only one record in previous array - hide previous button
            if(!dir || (previous.length == 1)) {$scope.previous = false;}

            //if user clicks on previous button    
            if(dir=="prev")
                {
                    $scope.nearbyPlaces = previous[previous.length-1];
                    previous.splice(-1,1);
                    $scope.next = true;
                    return;        
                }

            //Storing data for previous button
            if($scope.nearbyPlaces && dir=="next"){
                previous.push($scope.nearbyPlaces);
        //        console.log(previous.length);
                }

            //if next token exists
            if(token && dir=="next"){
//            $scope.data.progressBar = true;

                var config = {
                    params: {
                            pagetoken : token
                            }
                    }
                $scope.previous = true;
                }

            //if it is first call and no next token 
            else{
                var config = {
                params: {keyword : $scope.formData.textkeyword,
                        type : $scope.formData.selectcategory.value,
                        latitude : latitude,
                        longitude : longitude,
                        radius : parseFloat(textdistance * 1609.34),
                        from : $scope.formData.loc,
                        userLocation : $scope.formData.locText
                        }
                    }    
                }

                //AJAX call to google nearby api
            $http.get('/view1',config).
                then(function(response) {    
                $scope.placesHeading = true;
                $scope.resultsButton = true;
                $scope.favoriteButton = true;

                $scope.data.progressBar = true;

                if(response.data.results.length==0){
                    $scope.error.norecords = true;
                    $scope.data.progressBar = false;
                    $scope.nearbyPlacesContainer = false;
                    $scope.infoTab = false;
                    $scope.error.failed = false;
                }
                else{
                    if(response.data.status == "OK")
                    {
                        $scope.nearbyPlaces = response.data;
                        $scope.infoDetails = false;
                        $scope.data.progressBar = false;
                        $scope.nearbyPlacesContainer = true;
                        $scope.infoTab = false;
                        $scope.error.failed = false;
                        $scope.error.norecords = false;

                    }

                    //error = zero results, invalid results
                    else{
                        $scope.nearbyPlacesContainer = false;
                        $scope.data.progressBar = false;
                        $scope.error.failed = true;
                        $scope.error.norecords = false;

                    }
                }
                $scope.detailsButton = true;        
                $scope.previousNext = true;    

                if(response.data.next_page_token)
                    $scope.next = true;
                else
                    $scope.next = false;

                //After getting nearby places data from nodejs    
                //call placedetails api using place id

                }).catch(function(response) {
                console.log("error in posting",response);
                
                //when bad location is provided
                    $scope.error.norecords = true;
                    $scope.data.progressBar = false;
                    $scope.nearbyPlacesContainer = false;
                    $scope.infoTab = false;
                    $scope.error.failed = false;
                
                })
    };
    
    
        $scope.changedValue = function(item) {
                if(item == "Highest_rating") {
                    $scope.itemList = 'rating';
                    $scope.orderAsc = false;
                    $scope.valueSort = "Highest Rating";}

                if(item == "Lowest_rating"){
                    $scope.itemList = 'rating';
                    $scope.orderAsc = true;
                    $scope.valueSort = "Lowest Rating";}

                if(item == "Most_Recent"){
                    $scope.itemList = 'time';
                    $scope.orderAsc = false;
                    $scope.valueSort = "Most Recent";}


                if(item == "Least_Recent"){
                    $scope.itemList = 'time';
                    $scope.orderAsc = true;
                    $scope.valueSort = "Least Recent";}


                if(item == "Default Order"){
                    $scope.itemList = '' ;
                    $scope.orderAsc = '';
                    $scope.valueSort = "Default Order";}
        }


    //Calling place details
        
    $scope.placeDetails = function(result,index){
        
        var lat = result.geometry.location.lat;
        var lng = result.geometry.location.lng;
        
        
        var place_id = result.place_id;
        $scope.nearbyPlacesContainer = false;
        $scope.infoTab = true;
        $scope.tableBody = true;
        $scope.List = true;
        $scope.active = false;
        $scope.favoriteContainer = false;
        
        $scope.destination_latitude = lat;
        $scope.destination_longitude = lng;

        
        
        var placeResult;

        var map = new google.maps.Map( document.getElementById('map'), {
          center: {lat:parseFloat(lat), lng: parseFloat(lng)},
          zoom: 15
        });

        var request = {
        placeId:place_id
        };
        
        //Creating map
        var content = new google.maps.Map(document.getElementById('map'), {
        center: { lat: parseFloat(lat), lng:  parseFloat(lng) },
        zoom: 15
        });
        
        var marker = new google.maps.Marker({
            position: { lat: parseFloat(lat), lng:  parseFloat(lng) },
            map: content
        });

        document.getElementById('map').setAttribute("style", "width: 100%; height:500px;display: grid;  z-index: 1; margin-top: 1%;overflow: hidden; ");

        service = new google.maps.places.PlacesService(map);
        service.getDetails(request, callback);
        
        function callback(place, status) {
            
        if (status == google.maps.places.PlacesServiceStatus.OK) {    
            $scope.place_name = place.name; 
            var heading_html = ` <div class="card-body"><h4 class="display-8 text-center">`+place.name+`</h4></div> `;
            compliledPlaceName = $compile(heading_html)($scope);

            var heading_div = angular.element(document.querySelector("#heading"));
            heading_div.empty();
            heading_div.append(compliledPlaceName);            
            
            var infoDetails =`<div id="tableDetails">`;
            infoDetails += `<table class="table table-striped table-sm " >`;
            infoDetails +=`<tbody ng-show="tableBody" ng-model="infoDetails"><tr>`;
            
            if(place.formatted_address) {infoDetails += `<th scope="row">Address</th><td>`+place.formatted_address+`</td></tr>`;
                                        $scope.address = place.formatted_address;
                                        }
            
            if(place.international_phone_number) infoDetails += `<tr><th scope="row">Phone Number</th><td>`+place.international_phone_number+`</td></tr>`;

            var dollarsPrice ='$';
            //Converting price_level to $$
            if(place.price_level) {
                if(place.price_level==0)
                    dollarsPrice="Free";
                else{
                    for(i = 2;i<=place.price_level;i++){
                        dollarsPrice +='$';
                    }
                }
            }

            infoDetails += `<tr><th scope="row">Price Level</th><td>`+dollarsPrice+`</td></tr>`;
            
            if (place.rating) infoDetails += `<tr> <th scope="col" style="text-align:left">Rating</th> <td style="text-align:left"><span style="display:inline-block;">` + place.rating + `</span>&nbsp<span id="my_star_rating" style="display:inline-block;"></span></div></td></tr>`;
            
            if(place.url) infoDetails +=   `<tr><th scope="row">Google Page</th><td><a target="_blank" href=`+place.url+`>`+place.url+`</a></td></tr>`;
            if(place.website)  {infoDetails +=  `<tr><th scope="row">Website</th><td><a target="_blank" href=`+place.website+`>`+place.website+`</a></td></tr>`;
                               $scope.url = place.website;}
             
            var utc_offset = place.utc_offset;

            var currentdate = moment();
            var date_today = currentdate.day();
            
            if(place.open_now){
            var index_date = place.opening_hours.weekday_text[date_today - 1].indexOf(":") + 1;
            
            if (place.opening_hours.open_now == true)
              infoDetails += `<tr> 
                <th scope="col" style="text-align:left">Hours</th> 
                <td style="text-align:left">
                Open Now: ` + place.opening_hours.weekday_text[date_today - 1].slice(index_date) + 
                  ` &nbsp&nbsp<a id="openhours"data-toggle="modal" data-target="#modal_date"><small>Daily open hours</small></a></td></tr>`;
            else if (place.opening_hours.open_now == false)
              infoDetails += `<tr><th scope="col" style="text-align:left">
                Hours</th> 
                <td style="text-align:left">
                Closed&nbsp&nbsp<a id="openhours"data-toggle="modal" data-target="#modal_date"><small>Daily open hours</small></a></td></tr>`;

            infoDetails += `</tbody> </table> </div>`;

            infoDetails += `<div class="fade modal" id="modal_date" tabindex=-1 role=dialog aria-hidden=true aria-labelledby=exampleModalCenterTitle>
            <div class="modal-dialog modal-dialog-centered"role=document><div class=modal-content>
            <div class=modal-header><h5 class=modal-title id=exampleModalLongTitle>Open Hours</h5><button aria-label=Close class=close data-dismiss=modal type=button>
            <span aria-hidden=true>×</span></button></div><div class=modal-body><table class="table table-sm">`;

            infoDetails += `<tr><td><strong>` + place.opening_hours.weekday_text[date_today - 1] + `</strong></td></tr>`;
            for (var i = 0; i < place.opening_hours.weekday_text.length; i++) {
              if (i != date_today - 1) {
                infoDetails += `<tr><td>` + place.opening_hours.weekday_text[i] + `</td></tr>`;
              }
            }

            infoDetails += `</table><div class=modal-footer>
                <button class="btn btn-secondary"data-dismiss=modal type=button>Close</button></div>`;
            
            }
            
            
            infoDetails += `</tbody></table></div></div>`;
            
            //Check if tableDetails already exists, if it does then replace html content
            //else append to infoTab
            
            var compDetails = $compile(infoDetails)($scope);
            var page = angular.element(document.querySelector('#info'));
            page.empty();
            page.append(compDetails); 
            $scope.result = result;
            $scope.index = index;
            
            }
            
            
            $(function () {

              $("#my_star_rating").rateYo({
                rating: (place.rating/Math.ceil(place.rating))*100 + '%',
                readOnly: true,
              starWidth: "16px",
                normalFill: "#FFFFFF",
                numStars: Math.ceil(place.rating)
              });

            });

           $scope.reviews_google_arr = place.reviews;
            $scope.reviews_google_arr = Object.keys($scope.reviews_google_arr).map(function(key) {
                return $scope.reviews_google_arr[key];
              });

            if(!place.reviews)
                $scope.googleRevErr = true;
            
            //Yelp Reviews
            var address1="";
            var city = "";
            var state = "";

            var name = place.name;
            var country = "";
            
            var location={};
            location = place.geometry.location;
            var lat = location.lat();
            var lng = location.lng();
            
            for(var addr_comp of place.address_components){
                if(addr_comp.types.indexOf("locality") > -1)  
                    city = addr_comp.short_name;

                if(addr_comp.types.indexOf("administrative_area_level_1") > -1)  
                    state = addr_comp.short_name;
                if(addr_comp.types.indexOf("country") > -1)  
                    country = addr_comp.short_name;
                if(addr_comp.types.indexOf("route") > -1)  
                    address1 = addr_comp.short_name;
                
            }

            $scope.yelpQuery= {
                "address1": address1,
                "latitude": lat,
                "longitude" : lng,
                "city": city,
                "state": state,
                "country": country,
                "name": name
            }

            var request = $http.post('/yelp_reviews',$scope.yelpQuery).then(function(response){
                if(response == "Error"){
                            $scope.error.yelpError = true;
                }
                else{
                $scope.yelpReviews = response.data;}

                $scope.reviews_yelp_arr = Object.keys($scope.yelpReviews).map(function(key) {
                    return $scope.yelpReviews[key];
                });
            },function(error){
                    $scope.error.yelpError = true;

                console.log(error);
            });


//        }
        //-----------------------------------------------------------------------------------------------------------------------------------------
        // Populating Photos
            
            
            var photos_tab_html = ``;
            if (place.photos != null) {
               photos_tab_html += `<div class="card-columns">`;
                  for (element of place.photos) {
                    photos_tab_html += `<div class="card">
                   <a href= "`+  element.getUrl({ "maxWidth": 2000, "maxHeight": 2000 }) 
                        +`" target="_blank">
                        <img class="card-img-top img-fluid img-thumbnail" src="`
                     + element.getUrl({  "maxHeight": 300, "maxWidth": 300 }) +`"></img></a></div>`;
                  }

                  photos_tab_html += `</div>`;
                  compiled_photos_tab = $compile(photos_tab_html)($scope);

               var photos_tab = angular.element(document.getElementById("images_tab"));
              photos_tab.empty();
               photos_tab.append(compiled_photos_tab);
            }
            else{
                $scope.error.photos = true;

            }
            
            document.getElementById("map_to_loc").value=place.name+", "+place.formatted_address;
            }      
        
    };
   
     $scope.setDropdown = function(choice){
         if(choice=="yelp")
         {
            $scope.valueDropdown="Yelp Reviews";
            $scope.showYelpReviews = true;
            $scope.showGoogleReviews = false;
         }
         else if(choice=="google"){
            $scope.showYelpReviews = false;
            $scope.showGoogleReviews = true;
             $scope.valueDropdown = "Google Reviews";
         }
         console.log(choice);
  };
    
    //to get map directions
    $scope.directions = function(){
        var directionsPanelDiv = angular.element(document.getElementById("directionsPanel"));
        directionsPanelDiv.empty();
        
        var directionsDisplay = new google.maps.DirectionsRenderer();
        var directionsService = new google.maps.DirectionsService();
        var start=$scope.parameters_direction.from_loc;
        
        if($scope.parameters_direction.from_loc=="Your Location" || $scope.parameters_direction.from_loc=="My location"){
            start = new google.maps.LatLng(latitude, longitude);
        }
        
        var end = new google.maps.LatLng($scope.destination_latitude, $scope.destination_longitude);
        var request = {
            origin:start,
            destination:end,
            travelMode: $scope.parameters_direction.mode_travel_map,
            provideRouteAlternatives: true
        };

        var map = new google.maps.Map(document.getElementById('map'), {
            center: { 
                lat: latitude, 
                lng: longitude },
            zoom: 15
        });
        
        directionsDisplay.setMap(map);
        directionsDisplay.setPanel(document.getElementById('directionsPanel'));

        
        directionsService.route(request, function(response, status) {
            if (status == 'OK') {
              directionsDisplay.setDirections(response);
            }
        });
    };


    
    $scope.map_view = function(){
        
        var map_url = 'http://cs-server.usc.edu:45678/hw/hw8/images/Map.png';
        
        if($scope.pegman_map == 'http://cs-server.usc.edu:45678/hw/hw8/images/Pegman.png'){
            $scope.pegman_map = map_url;
            var panorama = new google.maps.StreetViewPanorama(
                document.getElementById('map'), {
                    position: {
                                lat: $scope.destination_latitude, 
                               lng: $scope.destination_longitude}
                });
        }
        else{
            $scope.pegman_map = 'http://cs-server.usc.edu:45678/hw/hw8/images/Pegman.png';
            var content = new google.maps.Map(document.getElementById('map'), {
                center: { lat: $scope.destination_latitude, lng: $scope.destination_longitude },
                zoom: 15
            });
            
            var marker = new google.maps.Marker({
                position: { lat: $scope.destination_latitude, lng: $scope.destination_longitude },
                map: content
            });
        }
    };


    //switch to results
    $scope.activateResults  = function(){
        $scope.active = false;
        $scope.favoriteContainer = false;
        $scope.showFavTable = true;
        $scope.error.failed = false;
        $scope.error.norecords = false;
        $scope.error.favorite = false;
        $scope.error.photos = false;

        if(!$scope.formData.textkeyword || $scope.buttonSubmit)
            $scope.nearbyPlacesContainer=false;
        else
            $scope.nearbyPlacesContainer=true;
        


    };
    
    
    
    //Adding to local storage
    $scope.addToFav = function(result,index){
        console.log(result);
        if(typeof(Storage) !== "undefined"){
            
        if(localStorage.length > 0){
            var data = [];
            var placeIDs = [];

            data = JSON.parse(localStorage.getItem("array"));
            placeIDs = JSON.parse(localStorage.getItem("place_id"));

            if(placeIDs.indexOf(result.place_id) < 0){    
            data.push(result);
            placeIDs.push(result.place_id);
                console.log("adding");

            localStorage.setItem("array",JSON.stringify(data));
            localStorage.setItem("place_id",JSON.stringify(placeIDs));
            }
            this.added = true;
            }
            
            else{
                var data = [];
                var placeIDs = [];
                data.push(result);
                placeIDs.push(result.place_id);
                localStorage.setItem("array",JSON.stringify(data));
                localStorage.setItem("place_id",JSON.stringify(placeIDs));
            }
        }        
    };
    
    
    //switching back to results
    $scope.backToResults = function()
    {
//        $scope.showFavTable = true;
        $scope.nearbyPlacesContainer = true;
        $scope.List = false;
        $scope.infoTab = false;
        $scope.favoriteContainer = false;
    };
    
    function pageResult(favoritePage) {
    favoritePage.currentPage = favoritePage.results.slice(0 + favoritePage.offset, favoritePage.pageSize + favoritePage.offset);
    };
    
    //View favorites
    $scope.viewFav = function()
    {
        $scope.active = true;
        $scope.nearbyPlacesContainer=false;
        $scope.infoTab = false;
        $scope.disablePrev = false;
        $scope.error.favorite = false;
        
        if(!$scope.fav || $scope.fav.results.length == 0){
            $scope.favoriteContainer = false;
            $scope.error.favorite = true;
            
        }
        else{
            $scope.favoriteContainer = true;
            $scope.error.favorite = false;
        }

        var data = JSON.parse(localStorage.getItem("array"));
        $scope.fav= {results: data};
        if($scope.fav.results.length<20) {$scope.disableNext = false;}
        else{$scope.disableNext = true;}

        var favoritePage = $scope.fav;

        // array to hold 20.
        favoritePage.currentPage = [];
        // keeping track of how many pages we've moved along.
        favoritePage.offset = 0;
        // variable to hold page size
        favoritePage.pageSize = 20;

                
        pageResult(favoritePage);
        
        $scope.fav.nextPage = function() {
    
        $scope.disablePrev = true;        
        favoritePage.offset += favoritePage.pageSize;
        pageResult(favoritePage);
        }

        $scope.fav.previousPage = function () {



        if(favoritePage.offset === favoritePage.pageSize) {
                $scope.disablePrev = false;
            }
            
        favoritePage.offset -= favoritePage.pageSize;
        pageResult(favoritePage);
        }

    };
    
    
    //to delete a record from favorite list
    $scope.delete = function(index,val){
        
        //first delete from view
		var comArr = eval( $scope.fav.currentPage );
        var toRemove;
        $scope.fav.currentPage.splice(index, 1);
                
        //now delete from local storage
        var place_id_list = JSON.parse(localStorage.getItem("place_id")); 
        var indexToDel = place_id_list.indexOf(val.place_id);

//        if(indexToDel > -1){
        var data_old = JSON.parse(localStorage.getItem("array"));
        var r =data_old.splice(indexToDel,1);
        var s = place_id_list.splice(indexToDel,1);

        localStorage.clear();
        localStorage.setItem("array",JSON.stringify(data_old));
        localStorage.setItem("place_id",JSON.stringify(place_id_list));

        var favoritePage = $scope.fav;
        $scope.fav.offset += 1;
        favoritePage.currentPage = favoritePage.results.slice(0 + favoritePage.offset, favoritePage.pageSize + favoritePage.offset);
        console.log($scope.fav.currentPage.length);
        console.log($scope.fav.results.length);

        if(favoritePage.currentPage.length < 1 && favoritePage.results.length >=20) {
            favoritePage.offset -= favoritePage.pageSize;
            pageResult(favoritePage);            
            }
//        }
        
        if(favoritePage.currentPage.length <= 0 ){
            $scope.favoriteContainer = false;
            $scope.nearbyPlacesContainer = true;
            $scope.active = false;
        }
	};
    
}]);








