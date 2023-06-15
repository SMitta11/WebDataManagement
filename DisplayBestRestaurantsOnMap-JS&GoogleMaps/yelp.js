var map;
var radius;
var lat;
var lng;
var allMarkers = [];
var prev_infowindow =false; 
function initialize() {
}

function findRestaurants() {
   document.getElementById("output").innerHTML = "";
   removeAllMarkers();
   
   var searchTerms = document.getElementById('searchterms').value;
   var xhr = new XMLHttpRequest();
   
   var qstr = "proxy.php?term="+searchTerms+"&latitude="+lat+"&longitude="+lng+"&radius="+radius+"&limit=10";
   //console.log(lat,lng,radius)
   xhr.open("GET", qstr);
   xhr.setRequestHeader("Accept", "application/json");
   xhr.onreadystatechange = function () {
      if (this.readyState == 4) {
         var json = JSON.parse(this.responseText);
         var businessData = json['businesses'];
         for (var i = 0; i < businessData.length; i++) {
            var data = businessData[i];
            displayData(data,i)
         }
      }
   };
   xhr.send(null);
}

function removeAllMarkers(){
   for (var i = 0; i < allMarkers.length; i++ ) {
      allMarkers[i].setMap(null);
    }
    allMarkers.length = 0;
}

function addNewMarker(location,titleData){
   var marker = new google.maps.Marker({
      position: location,
      map,
      title: titleData,
      label:titleData,
    });
    allMarkers.push(marker);
    return marker;
}

function displayData(data,i) {
   var imageData = data['image_url'];
   var nameData = data['name'];
   var ratingData = data['rating'];
   
   var latData = data['coordinates']['latitude'];
   var lngData = data['coordinates']['longitude'];
   var latLong = {lat: latData,lng: lngData}
   var titleData = (i+1).toString();
   var marker = addNewMarker(latLong,titleData);

   const contentString =
   '<div id="content">' +
   '<div id="siteNotice">' +
   "</div>" +
   '<h1 id="firstHeading" class="firstHeading">'+nameData+'</h1>' +
   '<div id="bodyContent">' +
   '<img src="'+imageData+'" height="200" width="200">' +
   '<p>Rating '+ratingData+'</p>'
   "</div>" +
   "</div>";   
   const infoWindow = new google.maps.InfoWindow();

   marker.addListener("click", () => {
      if( prev_infowindow ) {
         prev_infowindow.close();
      }
      prev_infowindow = infoWindow;
      infoWindow.close();
      infoWindow.setContent(contentString);
      infoWindow.open(marker.getMap(), marker);
    });   
   //console.log(latData,lngData)
   var catg = data['categories'];
   var finalCatg = ''
   for (var j = 0; j < catg.length; j++) {
      finalCatg += catg[j]['title'] + ', '
   }
}

function initMap() {
   var myLatLang = { lat: 32.75, lng: -97.13 };
   map = new google.maps.Map(document.getElementById("map"), {
      center: myLatLang,
      zoom: 16,
   });

   google.maps.event.addListener(map, 'idle', function () {
      var bounds = map.getBounds();
      //console.log(bounds)
      lat = bounds.getCenter().lat();
      lng = bounds.getCenter().lng();
      var tempRadius = calculateRadius();
      radius = tempRadius<40000?tempRadius:40000;
   });

}

function calculateRadius() {
   var bounds = map.getBounds();
   var radius1 = google.maps.geometry.spherical.computeDistanceBetween(
      bounds.getCenter(),
      new google.maps.LatLng(bounds.getNorthEast().lat(), bounds.getCenter().lng())
   );
   var radius2 = google.maps.geometry.spherical.computeDistanceBetween(
      bounds.getCenter(),
      new google.maps.LatLng(bounds.getCenter().lat(), bounds.getNorthEast().lng())
   );

   var radius = radius1 <= radius2 ? radius1 : radius2;
   return parseInt(Number((radius).toFixed(2)));
}

window.initMap = initMap;
