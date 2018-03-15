 <?php
    $key="AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM";
   if(isset($_POST["search"])){       
    if(isset($_POST["category"])) $type = $_POST['category'];
    if(isset($_POST["distance"])) $radius = $_POST["distance"];   
    if(isset($_POST["keyword"])) $keyword = $_POST['keyword'];
    if(isset($_POST["start"])) $start = $_POST["start"];
    if(isset($_POST["latitude"])) $lat = $_POST["latitude"];
    if(isset($_POST["longitude"])) $lng = $_POST["longitude"];
    $location = 0;
    if(isset($_POST["location"])) $location = $_POST["location"];    
    $saveRadius = $radius;
    if($radius=="") $radius=10 * 1609.34;


    if($start == "userloc"){
        $location = $_POST["location"];
        $location = str_replace(' ', '+', $location);
        $geoLocation = "https://maps.googleapis.com/maps/api/geocode/json?address=".rawurlencode($location)."&key=".$key;
        $locationObject = json_decode(file_get_contents($geoLocation),true);   
        if(isset($locationObject['results'][0]['geometry']['location']['lat'])){
        $lat = $locationObject['results'][0]['geometry']['location']['lat'];}
        else {$lat = "";}
        if(isset($locationObject['results'][0]['geometry']['location']['lng'])){
        $lng = $locationObject['results'][0]['geometry']['location']['lng'];}
        else {$lng="";}
    }

    $keyword = str_replace(' ', '+', $keyword);
    $queryGoogleApi = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$lat.','.$lng."&radius=".$radius."&keyword=".rawurlencode($keyword)."&key=".$key;
    if($type!="default") $queryGoogleApi .= "&type=".$type;
    $outputSearch = file_get_contents($queryGoogleApi);
  }

        if(isset($_REQUEST["place_id"])){
        $placeID = $_REQUEST["place_id"];
        $query_place = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$placeID."&key=".$key;
        $place_details = json_decode(file_get_contents($query_place),true);
                
        $i =1;
        $photos = array();
        if(isset($place_details['result']['photos'])){
        $result_photos = $place_details['result']['photos']; 
            $len = count($place_details['result']['photos']);
            foreach($result_photos as $photo)
            {
                if($i<=5){
                $photo_query = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=".$photo['width']."&photoreference=".$photo['photo_reference']."&key=".$key;
                $fileName = 'image'. $i .'.jpeg';
                $photo_obj = file_get_contents($photo_query);
                file_put_contents($fileName,$photo_obj);
                array_push($photos, $fileName);
                $i = $i+1;
                }
        } 
        }
        
        
        $i =1;
        $reviews = array();
        $profile_pic = "";
        if(isset($place_details['result']['reviews'])){
            $result_reviews = $place_details['result']['reviews']; 
            $len = count($result_reviews);
            foreach($result_reviews as $review)
            {
                if($i<=5){
                if(isset($review['profile_photo_url'])){ 
                    $profile_pic = $review['profile_photo_url'];
                }
                    
                if(isset($review['author_name'])){ 
                    $author_name = $review['author_name'];
                }
                else {
                    $author_name = "";
                }

                if(isset($review['text'])){ 
                    $text = $review['text'];
                }
                else {
                    $text = "";
                }

                array_push($reviews,array('photo_url' => $profile_pic,'author_name' => $author_name,
                  'text' => $text));
                $i = $i+1;
                }

            }
        }
        
    $place_details_ouput = array('photos'=>$photos,'reviews'=>$reviews);
    echo json_encode($place_details_ouput);
    exit();
    }
?>


<!DOCTYPE html>
<html>
  <head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">
    <title>Homework 6</title>
    <style type="text/css">
      .input_class {
        border: 2px solid #ddd;
        width: 38%;
        margin: auto;
          margin-top: 20px;
        padding: 9px 9px 9px 9px;
        background-color: #F8F8F8;
      }

        hr{
            color: #ddd;
            width: 98%;
        }

        .distanceInput {
        float: right;
        margin-right: 13%;
        }

        .heading{
            font-size: 2em;
            font-weight: 200;
            font-style: italic;
            text-align: center;
            margin: -5px -5px -5px -5px;
        }
        
        input{
            margin-bottom:  5px;
            margin-top: 5px;
            margin-left: 4px;

        }
        
        #category {
            margin-left: 4px;
            width: 100px;
            height: 20px;
            
        }
        .from{
            margin-left: 5px;
        }
        
        .buttons {
            margin-top: 30px;
            margin-left: 60px; 
        }
        
        #search { margin-right: 5px;}
        
        #location_input {margin-left: 0px;}
        
        .map {
            display: none;
            position: absolute;
            overflow: hidden;
        }
        
        .direction_list{
            width: 100px;
            position: absolute;
            z-index: 4;
            margin-left: -0em;
            border-color: #F0F0F0	;
            background-color: #F0F0F0	;
        }
        
        .details_table{
            margin: auto;
            width: 80%;
            border: 2px solid #ddd;
        }
        
        .details_table a:link, a:active, a:visited, a:hover{
            text-decoration: none;
            color: black;
        }
        
        .address a:hover{
            color:#808080;
        }
        
        
        table, th, td {
            border: 2px solid #ddd;
            padding: 0px 10px 0px 10px;
        }
        
        table {
        border-collapse: collapse;
        }
        
        .resultIcon img{
            width: 90%;
            height: 90%;
        }
        
        .direction_entry{
            background-color: #F0F0F0	;
            border-color: #F0F0F0	;
            display: block;
            padding: 10px 10px 10px 10px;
            margin-bottom: -20px;
        }
        
        .direction_list {
            height: 40px;
            border: 1px solid #F0F0F0	;
            display: block;
            
        }
        
        
        .table_r_ph {
            margin: auto;
            width: 600px;
            border: 2px solid #ddd;
            text-align: left;
        }

        #heading1, #heading2{
            margin: auto;
            text-align: center;
        }
        
        .namePlace{
            margin: auto;
            padding-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        #reviews_op, #photos_op{
            width: 600px;
            margin:auto;
        }
        
        .arrow{
            margin: auto;
            margin-left: 48.5%;
        }
        
        .image_review { margin:auto; padding-left: 230px; width: 50px; height: 50px;}
        
        .image_photo { margin: 10px 10px 10px 0px;}
        
        
    .direction_list a:hover{
        background-color: #D3D3D3;
      }        
    

    </style>
      
    <script type="text/javascript">
        
            function changeAppearanceLocation(){

            if(document.getElementById('startLoc').checked == true && document.getElementById('startLoc').value=="userloc")
            {
            document.getElementById('location_input').required = true;
            document.getElementById('location_input').disabled = false;
            document.getElementById('startHere').checked = false;    
            }

            else if(document.getElementById('startHere').checked == true && document.getElementById('startHere').value=="here")
            {
            document.getElementById('location_input').required = false;
            document.getElementById('location_input').disabled = true;
            document.getElementById('startLoc').checked = false;    

            }        

        }

        function invokeIP(){
            var xhttp=new XMLHttpRequest();
            var usc_lat = 34.0266;
            var usc_lng = -118.2831;
            xhttp.open("GET","http://ip-api.com/json",false);
            try{
            xhttp.send();
            var jsonObj = xhttp.responseText;
            locationObject = JSON.parse(jsonObj);
            var status = locationObject.status;
            if(status == "success"){
                document.getElementById("latitude").value = locationObject.lat;
                document.getElementById("longitude").value = locationObject.lon;
                document.getElementById("search").disabled = false;
            }

            else{
                document.getElementById("latitude").value = usc_lat;
                document.getElementById("longitude").value = usc_lng;
                document.getElementById("search").disabled = true;
            }
            }
            catch(e){
            alert(e.message);
            } 
        }
        
        

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSx87XGNw7CHF__Q0HBjccdN4p4rGzpIg"></script>

  </head>
    
    
    
   <body onload="invokeIP()">
    <div class="input_class">
        <form name="inputForm"  accept-charset="utf-8" method="post" action="">        
            <p class="heading">
            Travel and Entertainment Search
            <hr>
            <div class="leftSide">
                <b>Keyword </b><input type="text" name="keyword"  id="keyword" required>
              <br>
                <b>Category</b> 
                <select name="category" id="category">
                    <option value="default">default</option>
                    <option value="cafe">cafe</option>
                    <option value="bakery">bakery</option>
                    <option value="restaurant">restaurant</option>
                    <option value="beauty_salon">beauty salon</option>
                    <option value="casino" >casino</option>
                    <option value="movie_theater">movie theater</option>
                    <option value="lodging">lodging</option>
                    <option value="airport">airport</option>
                    <option value="train_station">train station</option>
                    <option value="subway_station">subway station</option>
                    <option value="bus_station">bus station</option>
              </select>

              <br>
                  
              <div class="rightSide">
                  <b>Distance (miles)</b><input type="text" name="distance" placeholder="10" id="distance"><b><span class="from">from</span></b>
                    <div class="distanceInput">
                        <input type="radio" name="start" value="here" id="startHere"  onchange="changeAppearanceLocation()" checked="checked">Here<br>
                        <input type="radio" name="start" value="userloc" id="startLoc" onchange="changeAppearanceLocation()" >
                        <input type="text" placeholder="location" name="location" id="location_input" !required disabled>
                  </div>
                  <input type="hidden" name="latitude" id="latitude">
                  <input type="hidden" name="longitude" id="longitude">
            </div>
            
            <div class="buttons">
              <input type="submit" name="search" value="Search" id="search" disabled>
              <button id="clear" type="button" onclick="clearAll()">Clear</button>
            </div>
          </div>
    </form>
  </div>
  <br>
<br>
<div id="output"></div>
   
 
    <script>
        
//    Global variables
    var locationObject;    
    var arrayPlacesNames = new Array();
    var down_arrow_path="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
    var up_arrow_path = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png";
    
//    Function to print JSON as a table    
      function displayJSON(objectPhp){

        if(objectPhp == "empty")
            return;
        var count=0;
        var html_output;
        var parsedJSON = JSON.parse(objectPhp);
          
        if(parsedJSON.results=="")
        {
        html_output = "<div style='text-align:center;border: 2px solid #ddd;width:800px;margin:auto;margin-top:-25px;background-color:#F0F0F0;'>No Records have been found</div>";
        document.getElementById("output").innerHTML=html_output;
        }
        else
        {
        html_output = "<table class='details_table' border='1'>"
        html_output += "<tr><th>";
        html_output +="Category</th><th>Name</th><th>Address</th></tr>";  

        var i = 0;
        var len = parsedJSON.results.length;
        var result = parsedJSON.results;
        for(i=0;i<len; i++)
        {
        arrayPlacesNames.push(result[i].name);
        html_output += "<tr><td class='resultIcon'>";
        html_output += "<img  src='"+result[i].icon+"'></td>";
        html_output += "<td><a class='placeName' href='javascript:print_photos_reviews(\""+result[i].place_id+"\","+count+")'>";
        html_output += result[i].name+"</a></td>";
        html_output += "<td><div class='address'><a href='javascript:initMap("+result[i].geometry.location.lat+","+result[i].geometry.location.lng+","+count+")'>"+result[i].vicinity+"</a>";
        html_output += "<div class='map_area' id='direction"+count+"'>";
        html_output += "</div><div id='map"+count+"' class='map' style='display: none;'></div>";
        html_output += "</div></td></tr>";
        count++;
        }
        html_output += "</table>";
        }        
        document.getElementById("output").innerHTML=html_output;
          
//        Retain variable values  
          
        var category;
        var radius;
        var keyword;
        var start;
        var location;
        keyword = "<?php echo isset($keyword)? $keyword: ""; ?>";
        document.getElementById("keyword").value = keyword.replace(/\+/g," ");

        category = "<?php echo isset($type)? $type: ""; ?>";
        document.getElementById("category").value = category;
        radius = "<?php echo isset($saveRadius)? $saveRadius:""; ?>";   
        document.getElementById("distance").value = radius;


        start = "<?php echo isset($start)? $start:""; ?>";
        if(start == "here") 
        {
            document.getElementById("startHere").checked = true;
            document.getElementById("startLoc").checked = false;

        }
        else {
            location = "<?php echo isset($location)? $location:""; ?>";    
            document.getElementById("startLoc").checked = true;
            document.getElementById("startHere").checked = false;
            document.getElementById("location_input").value = location.replace(/\+/g," ");
            document.getElementById("location_input").required = false;
            document.getElementById("location_input").disabled = false;
        }

          
      }

        
        
        function clearAll(){
            
            if(document.getElementById("keyword").value != "")
                document.getElementById("keyword").value = "";
            if(document.getElementById("location_input").value != "")
                document.getElementById("location_input").value = "";
            if(document.getElementById("category").selectedIndex != 0)
                document.getElementById("category").selectedIndex = 0;
            if(document.getElementById("startHere").checked == false)
                document.getElementById("startHere").checked = true;
            if(document.getElementById("startLoc").checked == true)
                document.getElementById("startLoc").checked = false;
            if(document.getElementById("location_input").disabled == false)
                document.getElementById("location_input").disabled = true;
            if(document.getElementById("distance").value != "")
                document.getElementById("distance").value = "";
            
            document.getElementById("output").innerHTML = "";

        }
        
        function print_photos_reviews(pid,index){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange  = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                var photos = new Array(response.photos);
                var output = "<p class='namePlace'>"+arrayPlacesNames[index]+"</p>";
                output += "<div id='heading1'>click to show reviews<br></div>";
                 
                document.getElementById("output").innerHTML = output;
                var div = document.getElementById("output");
                var image_1 = document.createElement('img');
                image_1.setAttribute('src',down_arrow_path);
                image_1.setAttribute('class', 'arrow');
                image_1.setAttribute('width','42px');
                image_1.setAttribute('id','review_arrow');                
                image_1.addEventListener('click',function(){display(response.reviews,true);}, false);
                div.appendChild(image_1);
                output = "<div id='reviews_op'></div>"
                var div_heading = document.createElement('div');
                output += "<div id='heading2'>click to show photos<br></div>";
                image_1.insertAdjacentHTML("afterend",output);
                var image_2 = document.createElement('img');
                image_2.setAttribute('src',down_arrow_path);
                image_2.setAttribute('width','42px');
                image_2.setAttribute('class', 'arrow');
                image_2.setAttribute('id','photos_arrow');                
                image_2.addEventListener('click',function(){display(response.photos,false);}, false);
                div.appendChild(image_2);
                output = "<div id='photos_op'>";
                image_2.insertAdjacentHTML("afterend",output);
            }
        };
        xhttp.open("GET", "ak18ty12cs571hw6.php?place_id="+pid, true);
        xhttp.send();
            
        }
        
        
        function display(element, flag){
//        Display photos
            if(flag==false){
                var img = document.getElementById("photos_arrow");
                if(img.src == down_arrow_path)
                {
                    img.src = up_arrow_path;
                    document.getElementById("reviews_op").innerHTML = "";
                    document.getElementById("heading2").innerHTML = "<div id='heading2'>click to hide photos<br></div>";   
                    var output = "<table class='table_r_ph'>";
                    document.getElementById("review_arrow").src = down_arrow_path;
                    document.getElementById("heading1").innerHTML = "<div id='heading1'>click to show reviews<br></div>";   

                    if(element.length>0){
                        for(photo of element){
                            output += "<tr><td>";
                            output += "<a href='"+photo+"'><img src='"+photo+"' width=100% height=500px class='image_photo'></a></td></tr>";
                        }
                    }
                    else{
                        output += "<tr><td style='font-weight: bold; text-align: center; margin:auto; width:600px;'>No Photos Found</td></tr>";
                    }
                    output+="</table>";
                    document.getElementById("photos_op").innerHTML = output;
                }
                else{
                    document.getElementById("photos_op").innerHTML = "";
                    document.getElementById("heading2").innerHTML = "<div id='heading2'>click to show photos<br></div>";   
                    img.src = down_arrow_path;                    
                }

            }
            
            else if(flag==true)
                {
                    var img = document.getElementById("review_arrow");
                    if(img.src == down_arrow_path){
                    img.src = up_arrow_path;

                    document.getElementById("heading2").innerHTML = "<div id='heading2'>click to show photos<br></div>";   
                    document.getElementById("photos_arrow").src = down_arrow_path;
                    document.getElementById("photos_op").innerHTML = "";
                    document.getElementById("heading1").innerHTML = "<div id='heading1'>click to hide reviews<br></div>";
                    var output ="<table class='table_r_ph'>";
                        if(element.length>0){
                            for(review of element){
                                    if(review.photo_url == ""){
                                    output += "<tr><td id='review_header' style='padding-left:250px;'>"+review.author_name+"</td></tr>";
                                        
                                    }
                                else{
                                    output += "<tr><td id='review_header'><img src='"+review.photo_url+"' class='image_review' id='profile_pic'>  "+review.author_name+"</td></tr>";}
                                    output += "<tr><td>"+review.text;
                                    output += "</td></tr>";
                                    }
                            }
                        else{
                                output += "<tr><td style='font-weight: bold; text-align: center;margin:auto; width:600px'>No Reviews Found</td></tr>";
                            }
                                output += "</table>";
                                document.getElementById("reviews_op").innerHTML = output;
                        }
                    else{
                        img.src = down_arrow_path;
                        document.getElementById("heading1").innerHTML = "<div id='heading1'>click to show reviews<br></div>";   
                        document.getElementById("reviews_op").innerHTML = "";
                    }

                }
        }

        function initMap(place_lat,place_lng,ind)
        {
                var directionsService = new google.maps.DirectionsService();
                var directionsDisplay = new google.maps.DirectionsRenderer();
                var uluru = {lat: place_lat, lng: place_lng};

                
                var mapID = "map"+ind;
                var mapDiv = document.getElementById(mapID);
                if(mapDiv.style.display != "none"){
                        var dir_div = document.getElementById("direction"+ind);
                        dir_div.innerHTML = "";
                        mapDiv.innerHTML ="";
                        mapDiv.style.display = "none";
                        mapDiv.style.zIndex = 0;

                }
            
                else {
                    var map = new google.maps.Map(document.getElementById(mapID), {
                    zoom: 10,
                    center: uluru
                    });

                    var marker = new google.maps.Marker({
                    position: uluru,
                    map: map
                    });

                    
                    var output = "<div class='direction_list'>";
                    output += "<a class='direction_entry'  href='javascript:calcRoute("+place_lat+","+place_lng+","+ind+",1)'>Walk there</a><br>";
                    output += "<a class='direction_entry'  href='javascript:calcRoute("+place_lat+","+place_lng+","+ind+",2)'>Bike there</a><br>";
                    output +="<a class='direction_entry'  href='javascript:calcRoute("+place_lat+","+place_lng+","+ind+",3)'>Drive there</a><br>";
                    output += "</div>";

                    mapDiv.style.display = "block";
                    mapDiv.style.width = "500px";
                    mapDiv.style.height = "400px";
                    mapDiv.style.overflow = "hidden";
                    mapDiv.style.zIndex = "1";
                    document.getElementById("direction"+ind).innerHTML = output;
                }
            
        }

        
        function calcRoute(d_lat,d_lng,ind,m) {

                        var directionsDisplay = new google.maps.DirectionsRenderer;
                        var directionsService = new google.maps.DirectionsService;
                        var map = new google.maps.Map(document.getElementById('map'+ind), {
                        zoom: 14,
                        center: {lat: d_lat, lng: d_lng}
                        });
                        directionsDisplay.setMap(map);

                        var sMode;
                        var s_lat = <?php echo (isset($lat))? $lat:"";?>;
                        var s_lng = <?php echo (isset($lng))? $lng:"";?>;

                        if(m==1){sMode = "WALKING";}
                        else if(m==2){sMode="BICYCLING";}
                        else if(m==3){sMode="DRIVING";}

                        var request = {
                            origin: {lat: s_lat, lng: s_lng},
                            destination: {lat: d_lat, lng: d_lng},
                            travelMode: google.maps.TravelMode[sMode]
                        };

                        directionsDisplay.setMap(map);
                        directionsService.route(request, function(result, status) {
                        if (status == 'OK') {
                        directionsDisplay.setDirections(result);
                        }
                        else {
                        alert('Error occurred while requesting. Status code:' + status);
                            }
                        });
                    
                }
        
        var object;
        object = <?php echo isset($outputSearch)? json_encode($outputSearch): "empty"; ?>;
        displayJSON(object);
        

    </script>       
  </body>
</html>