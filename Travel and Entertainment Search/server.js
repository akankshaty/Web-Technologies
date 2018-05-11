//import the express module
const express = require('express');
const bodyParser = require('body-parser');

const googleMapsClient = require('@google/maps').createClient({
  key: 'AIzaSyDyU6dPrphd_olQg5LEg0JCLN7DfZ6-fPM',
    Promise: Promise
});

//store the express in a variable 
const app = express();
var path = require("path");
var request = require('request');
//var empty = require('is-empty');
const port = 8080;
//
const yelp = require('yelp-fusion');
const yelpKey = "2C1OT4Yeir0gHzpO8AiuarFlTqJaNwY7GdkX_RzZGjsjPlP9p_9EJ_A8GzZWgUmjdQRRi04qF9-DQXoRCBZ1Rvy_n0fA7w2vW1XW2YRI8VKplSgRxUgvGjR-zVvEWnYx";
const client = yelp.client(yelpKey);

app.use(bodyParser.urlencoded({extended:true}));
app.use(bodyParser.json());
app.use(express.static(__dirname + '/public'));

app.get('/', function(request, response){
    response.sendFile(path.join(__dirname+'/public/index.html'));
});

    
app.post('/yelp_reviews', function (req, res) {
    var queryParams = req.body;
    res.setHeader('Content-Type', 'application/json');

    
    const query = {
        name : queryParams['name'],
        address1 : queryParams['address1'],
        city : queryParams['city'],
        state : queryParams['state'],
        country : queryParams['country'],
    }

     console.log(query);

  client.businessMatch('best',queryParams).then(response => {
      
      if(response.jsonBody.businessess.length > 0){
        var id = response.jsonBody.businesses[0].id;}
        client.reviews(id).then(response => {
            res.send(response.jsonBody.reviews);
        }).catch(e => {
            console.log(e);
        });
    }).catch(e => {
      res.send("Error");
        console.log(e);
    });

});

//calling google client api
app.get('/view1',function(req,res){
res.setHeader('Content-Type','application/json');

var location = "";
var radius = req.query.radius;
var type = req.query.type;
var keyword = req.query.keyword;  


//if user enters a location, call google geocode api
if(req.query.userLocation && req.query.from == 2){
        var tmp_locTxt = req.query.userLocation;
        var locTxt = tmp_locTxt.split(' ').join('+');

        googleMapsClient.geocode({address: locTxt}).asPromise()
            .then((response) => {
            var results = response.json.results[0].geometry.location;
            lat = results.lat;
            lng = results.lng;
            

            googleMapsClient.placesNearby({
              location: lat + ',' + lng,
              type: type,
              radius: parseFloat(radius),
              keyword: keyword,
              }).asPromise()
                  .then((response) => {
                  res.send(response.json);
              })
              .catch((err) => {
                  console.log(err);
              });
        })
        .catch((err) => {
            console.log(err);
        });

   } //end of if , when user enters location   
else{
location = req.query.latitude + ', ' + req.query.longitude;

var paramsGoogle = {
            location: location,
            radius : parseFloat(radius),
            type : type,
            keyword : encodeURI(keyword)
};

googleMapsClient.placesNearby(paramsGoogle).asPromise()
.then((response) => {
    res.send(response.json);
  })
  .catch((err) => {
    console.log(err);
  });
}
});

app.get('/favicon.ico', function(req, res) {
    res.status(204);
});

app.listen(port, function(){
        console.log("Travel app listening at http://%s", port);
    });


