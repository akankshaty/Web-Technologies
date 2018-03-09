 <?php
    $key = "AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM";

   if(isset($_POST["search"]))
   { 

    if(isset($_POST["dist"])) $radius = $_POST["dist"];
    if(isset($_POST["startingPoint"])) $startingPoint = $_POST["startingPoint"];
    $location ="";   
    if(isset($_POST["keyword"])) $keyword = $_POST["keyword"];
    if(isset($_POST["type"]))    $type = $_POST["type"];
    if(isset($_POST["latitude"])) $lat = $_POST["latitude"];
    if(isset($_POST["longitude"])) $lng = $_POST["longitude"];
    $enteredRadius = $radius;
       
    if($radius=="")
            $radius=10 * 1609.34;

    if($startingPoint == "loc"){
        $location = $_POST["location"];
        $location = str_replace(' ', '+', $location);
        $geoLocQuery = "https://maps.googleapis.com/maps/api/geocode/json?address=".$location."&key=AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM";
        $jsonLoc = json_decode(file_get_contents($geoLocQuery),true);   
        $lat = $jsonLoc['results'][0]['geometry']['location']['lat'];
        $lng = $jsonLoc['results'][0]['geometry']['location']['lng'];   
    }

    $keyword = str_replace(' ', '+', $keyword);
    $query = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$lat.",".$lng."&radius=".$radius."&keyword=".$keyword."&key=AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM";

    if($type!="default")
      $query .= "&type=".$type;

    $outputSearch = file_get_contents($query);
  }
?>

<?php
  if(isset($_REQUEST["place_id"])){

    $place_id = $_REQUEST["place_id"];
    $req = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$place_id."&key=AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM";
    $placeJSON = json_decode(file_get_contents($req),true);

    //Save Top 5 photos on the server
    $i=1;
    $photoArr = array();
    foreach($placeJSON['result']['photos'] as $photo){
      if($i<=5){
       $photoQ = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=".$photo['width']."&photoreference=".$photo['photo_reference']."&key=AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM";
        //$photoQ = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=CnRtAAAATLZNl354RwP_9UKbQ_5Psy40texXePv4oAlgP4qNEkdIrkyse7rPXYGd9D_Uj1rVsQdWT4oRz4QrYAJNpFX7rzqqMlZw2h2E2y5IKMUZ7ouD_SlcHxYq1yL4KbKUv3qtWgTK0A6QbGh87GB3sscrHRIQiG2RrmU_jF4tENr9wGS_YxoUSSDrYjWmrNfeEHSGSc3FyhNLlBU&key=AIzaSyAutxHBn8Kl2PMFtTDbgYAF51TBSWmM5Js";

        $photoObj = file_get_contents($photoQ);
        $fileName = 'p'. $i .'.jpeg';
        file_put_contents($fileName,file_get_contents($photoQ));
        array_push($photoArr, $fileName);
        $i++;
      }
    }

    //Get the reviews
    $reviews=array();
    $i=1;
    foreach($placeJSON['result']['reviews'] as $review){
      if($i<=5){
        array_push($reviews,array(
          'author_name' => $review['author_name'],
          'photo_url' => $review['profile_photo_url'],
          'text' => $review['text']
          ));
        $i++;
      }
    }
    $output=array(
      'reviews'=> $reviews,
      'photos'=> $photoArr
      );

    echo json_encode($output);
    exit();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">
    <title>Homework 6</title>

    <script type="text/javascript">
        
      function invokeIP(){
        var xmlhttp=new XMLHttpRequest();
        xmlhttp.open("GET","http://ip-api.com/json",false);
        try{
          xmlhttp.send();
          var jsonLoc = xmlhttp.responseText;
          jsonLoc = JSON.parse(jsonLoc);

          if(jsonLoc.status == "success"){
            document.getElementById("search").disabled = false;
            document.getElementById("latitude").value = jsonLoc.lat;
            document.getElementById("longitude").value = jsonLoc.lng;
          }
          else{
            document.getElementById("search").disabled = false;
            document.getElementById("latitude").value = 34.0266;
            document.getElementById("longitude").value = -118.2831;
          }
        }
        catch(err){
          alert(err.message);
        } 
      }
        
      //Function to toggle the Location textbox
      function toggle_textbox(){
          
        if(document.getElementById('chkLoc').checked == true && document.getElementById('chkLoc').value=="loc")
        {
        document.getElementById('locTxt').required = false;
        document.getElementById('locTxt').disabled = false;
        document.getElementById('chkHere').checked = false;    

        }

        else if(document.getElementById('chkHere').checked == true && document.getElementById('chkHere').value=="here")
        {
        document.getElementById('locTxt').required = true;
        document.getElementById('locTxt').disabled = true;
        document.getElementById('chkLoc').checked = false;    

        }        

      }


        
    </script>

    <style type="text/css">
      .box {
            margin: auto;
            border: 1px solid black;
            width: 50%;
            background-color: #F3F3F3;

/*
        border: 2px solid #ddd;
        width: 50%;
        margin-left: 30%;
        margin-right: 30%;
        padding: 10px 10px 10px 10px;
        background-color: #F3F3F3;
*/
      }

      h1{
        font-style: italic;
        text-align: center;
      }

/*
      input{
        margin: 5px 5px 5px 5px;
      }
*/

/*
      #locDiv{
        margin-left: 27em;
      }
*/

      table{
        margin-left: 10%;
        margin-right: 10%;
        width: 80%;
      }

      table,td,th{
        border: 1px solid #ddd;
        font-size: 15px;
        border-collapse: collapse;
        padding: 10px 10px 10px 10px;
      }

      th{
        text-align: center;
        font-weight: bold;
      }

      .icon{
        width: 10%;
        margin: 0px 0px 0px 0px;
      }

      a{
        text-decoration: none;
        color: black;
      }

      h2{
        text-align: center;
      }

      #desc{
        text-align: center;
      }

      .img-circle {
        border-radius: 50%;
        vertical-align: middle;
        width: 50px;
        height: 50px;
      }

      #reviewHdr{
        text-align: center;
        font-weight: bold;
        font-size: 14px;
      }

      .arrow{
        display: block;
        margin-left: auto;
        margin-right: auto;
      }

      .detailsTable{
        border: 1px solid #ddd;
        border-collapse: collapse;
         margin-left: 30%;
        margin-right: 30%;
        width: 40%;
      }

      .small_img{
        cursor: pointer;
      }

      .map {
        display: none;
        position: absolute;
        overflow: hidden;
      }

      .addr{
        position: relative;

      }

      .dirPanel{
        background-color: #b3b3b3;
        text-align: center;
      }

      td.dirPanel:hover{
        background-color: #d9d9d9;
      }

      #floating-panel {
        position: absolute;
        z-index: 5;
        padding: 5px;
        text-align: center;
        /*line-height: 30px;*/
        /*padding-left: 10px;*/
      }

      .dirTbl{
        width: 110px;
        position: absolute;
        z-index: 5;
        margin-left: -0em;
      }
        
    .distanceInput {
        float: right;
        margin-right: 30%;
        }

    </style>
  </head>
   <body onload="invokeIP()">
    <div class="box">
    <h1>Travel and Entertainment Search</h1>
    <hr>

    <form name="searchForm" method="post" accept-charset="utf-8" action="">
    <div class="leftSide">

      Keyword<input type="text" name="keyword" required id="keyword">
      <br>
      <label>Category <label><select name="type" id="type">
        <option value="default">Default</option>
        <option value="cafe">Cafe</option>
        <option value="bakery">Bakery</option>
        <option value="restaurant">Restaurant</option>
        <option value="beauty_salon">Beauty Salon</option>
        <option value="casino" >Casino</option>
        <option value="movie_theater">Movie Theater</option>
        <option value="lodging">Lodging</option>
        <option value="airport">Airport</option>
        <option value="train_stn">Train Station</option>
        <option value="bus_stn">Bus Station</option>
        <option value="subway_stn">Subway Station</option>
      </select>

      <br>
    <div>
      Distance (miles)<input type="text" name="dist" placeholder="10" id="dist"> from
      <div class="distanceInput">        
          <input type="radio" name="startingPoint" value="here" id="chkHere" checked onchange="toggle_textbox()">Here<br>
          <input type="radio" name="startingPoint" value="loc" id="chkLoc" onchange="toggle_textbox()" >
          <input type="text" placeholder="location" name="location" id="locTxt" !required disabled>
      <br>
          
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
        </div>
        </div>
          
        <div>
      <input type="submit" name="search" value="Search" id="search" disabled>
      <button id="clear" type="button" onclick="clearAll()">Clear</button>
        </div>
          </div>
 </form>
  </div>
  <br><br>
  <div id="output"></div>
   
 
    <script>
      var jsonLoc;
      var placeArray = new Array();
      

      function displayJSON(outputPhp)
        {
        if(outputPhp == "empty")
          return;
        
        var outputSearch = JSON.parse(outputPhp);
        var output;
        var index=0;

        if(outputSearch.results==""){
          output = "<div id='box' style='text-align:center;'>No Records Have Been Found!</div>";
          document.getElementById("output").innerHTML=output;
        }
        else{
          output = "<table class='tblOutput'><tr><th>Category</th><th>Name</th><th>Address</th></tr>";      
          for(each of outputSearch.results){
            placeArray.push(each.name);
            output += "<tr><td class='icon'><img src='"+each.icon+"'></td>";
            //output += "<td><a href='javascript:getPlaceDetails(\""+each.place_id+"\",\""+each.name+"\")'>"+each.name+"</a></td>";
            output += "<td><a href='javascript:getPlaceDetails(\""+each.place_id+"\","+index+")'>"+each.name+"</a></td>";
            output += "<td><div class='addr'><a href='javascript:initMap("+each.geometry.location.lat+","+each.geometry.location.lng+","+index+")'>"+each.vicinity+"</a>";
            output += "<div class='floatingPanel' id='dir"+index+"'></div><div id='map"+index+"' class='map' style='display: none;'></div>";
            output += "</div></td></tr>";
         
            index++;
          }
          output += "</table>";
        }
        
        document.getElementById("output").innerHTML=output;
        setData();
          
      }


      function getPlaceDetails(pid, index){
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange  = function() {
            if (this.readyState == 4 && this.status == 200) {
                 var resp = JSON.parse(this.responseText);
                 var photos = new Array(resp.photos);
                var output = "<h2>"+placeArray[index]+"</h2>";
                output += "<div id='desc'>click to show reviews<br></div>";
                 
                document.getElementById("output").innerHTML = output;
                var div = document.getElementById("output");
                var img_tag = document.createElement('img');

                img_tag.setAttribute('src','http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png');
                img_tag.setAttribute('width','40px');
                img_tag.setAttribute('id','rev_arrow');
                img_tag.setAttribute('class', 'arrow');

                
                img_tag.addEventListener('click',function(){displayReviews(resp.reviews);}, false);
                div.appendChild(img_tag);

                output = "<div id='output2'></div><div id='desc'>click to show photos<br></div>";
                img_tag.insertAdjacentHTML("afterend",output);

                var img_tag2 = document.createElement('img');

                img_tag2.setAttribute('src','http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png');
                img_tag2.setAttribute('width','40px');
                img_tag2.setAttribute('id','ph_arrow');
                img_tag2.setAttribute('class', 'arrow');

                
                img_tag2.addEventListener('click',function(){displayPhotos(resp.photos);}, false);
                div.appendChild(img_tag2);
                output = "<div id='output3'>";
                img_tag2.insertAdjacentHTML("afterend",output);
            }
        };
        xmlhttp.open("GET", "places.php?place_id="+pid, true);
        xmlhttp.send();
      }

      function displayPhotos(photosObj){
        var img = document.getElementById("ph_arrow");
        if(img.src == 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png')
        {
          img.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png";          
          document.getElementById("rev_arrow").src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
          document.getElementById("output2").innerHTML = "";
      
          var output = "<table class='detailsTable'>";
          if(photosObj.length>0){
            for(p of photosObj)
            {
              output += "<tr><td><a href='"+p+"'><img src='"+p+"' width=100% height=500px class='small_img'></a></td></tr>";
            }
          }
          else{
            output += "<tr><td style='font-weight: bold; text-align: center;'>No Photos Found</td></tr>";
          }
          
          output+="</table>";
          document.getElementById("output3").innerHTML = output;
        }
        else{
          img.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
          document.getElementById("output3").innerHTML = "";
        }
      }

      function displayReviews(reviews){
        var img = document.getElementById("rev_arrow");
        if(img.src == 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png'){
          img.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png";

          document.getElementById("ph_arrow").src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
          document.getElementById("output3").innerHTML = "";
          var output ="<table class='detailsTable'>";
          if(reviews.length>0){
            for(review of reviews){
              output += "<tr><td id='reviewHdr'><img src='"+review.photo_url+"' class='img-circle'>  "+review.author_name+"</td></tr>";
              output += "<tr><td>"+review.text+"</td></tr>";
            }
          }
          else{
            output += "<tr><td style='font-weight: bold; text-align: center;'>No Reviews Found</td></tr>";
          }
          
          output += "</table>";
          document.getElementById("output2").innerHTML = output;
        }
        else{
          img.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
          document.getElementById("output2").innerHTML = "";
        }
        
      }

       function initMap(lat,lng,index) {
        //var uluru = {lat: -25.363, lng: 131.044};
        var id = "map"+index;
        var div = document.getElementById(id);
        
        if(div.style.display=="none"){
          var uluru = {lat: lat, lng: lng};
          var map = new google.maps.Map(document.getElementById(id), {
            zoom: 15,
            center: uluru
            //gestureHandling: 'none',
            //zoomControl: false
          });
          var marker = new google.maps.Marker({
            position: uluru,
            map: map
          });

          div.setAttribute("style", "display: block; width: 500px; height:500px; z-index: 1; overflow: hidden;");
          var dirPanel = "<table class='dirTbl'><tr><td class='dirPanel' onclick='getDir("+lat+","+lng+","+index+",1)'>Walk there</td></tr><tr><td class='dirPanel'";
          dirPanel+="onclick='getDir("+lat+","+lng+","+index+",2)'>Bike there</td></tr><tr><td class='dirPanel' onclick='javascript:getDir("+lat+","+lng+","+index+",3)'>Drive there</td></tr></table>";
          document.getElementById("dir"+index).innerHTML = dirPanel;
        }
        else{
          document.getElementById("dir"+index).innerHTML = "";
          div.innerHTML="";
          div.setAttribute("style", "display: none; width: 0px; height: 0px; z-index: 0;");
        }
        // clicked++;
              
      }

      function getDir(dest_lat,dest_lng,id,mode){
          var src_lat = <?php if(isset($lat))echo $lat ?>;
          var src_lng = <?php if(isset($lng))echo $lng ?>;
      
          var selectedMode;
          if(mode==0){
            selectedMode = "WALKING";
          }
          else if(mode==1){
            selectedMode="BICYCLING";
          }
          else{
            selectedMode="DRIVING";
          }
          
          var directionsDisplay = new google.maps.DirectionsRenderer;
          var directionsService = new google.maps.DirectionsService;
          var map = new google.maps.Map(document.getElementById('map'+id), {
            zoom: 14,
            center: {lat: dest_lat, lng: dest_lng}
          });
          directionsDisplay.setMap(map);

          directionsService.route({
          origin: {lat: src_lat, lng: src_lng},  
          destination: {lat: dest_lat, lng: dest_lng},  
          travelMode: google.maps.TravelMode[selectedMode]
          }, function(response, status) {
          if (status == 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
        }

        function setData(){
          var keyword = "<?php echo $keyword; ?>";
          var type = "<?php echo $type; ?>";
          var radius = "<?php echo $enteredRadius; ?>";
          var startingPoint = "<?php echo $startingPoint; ?>";

          document.getElementById("keyword").value = keyword;
          document.getElementById("type").value = type;
          document.getElementById("dist").value = radius;
          if(startingPoint == "here"){
            document.getElementById("chkHere").checked = true;
            document.getElementById("chkLoc").checked = false;
            document.getElementById("locTxt").disabled = true;
          }
          else{
            document.getElementById("chkLoc").checked = true;
            document.getElementById("chkHere").checked = false;
            document.getElementById("locTxt").disabled = false;
            var location = "<?php echo $location; ?>";

            document.getElementById("locTxt").value = location;
          }
        }

        function clearAll(){
            document.getElementById("type").selectedIndex = 0;
            document.getElementById("chkHere").checked = true;
            document.getElementById("chkLoc").checked = false;            
            document.getElementById("keyword").value = "";
            document.getElementById("locTxt").value = "";
            // document.getElementById("chkLoc").disabled = true;
            document.getElementById("locTxt").disabled = true;
            document.getElementById("dist").value = "";
            document.getElementById("output").innerHTML = "";

        }
        var phpOutput = <?php echo isset($outputSearch) ? json_encode($outputSearch): 'empty'; ?>;
        displayJSON(phpOutput);
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSx87XGNw7CHF__Q0HBjccdN4p4rGzpIg"></script>
  </body>
</html>