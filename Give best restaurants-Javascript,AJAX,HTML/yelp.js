function initialize () {
}

function findRestaurants () {
   document.getElementById("output").innerHTML = "";
   
   
   var city = document.getElementById('city').value;
   var searchTerms =document.getElementById('searchterms').value;
   var level=document.getElementById('level').value;
   //console.log(city,searchTerms,level);
   var xhr = new XMLHttpRequest();
   var qstr = "proxy.php?term="+searchTerms+"&location="+city+"&limit="+level+"&sort_by=best_match";
   var tstr = "proxy.php?term=Indian&location=Arlington&limit=10";
   
   xhr.open("GET", qstr);
   xhr.setRequestHeader("Accept","application/json");
   xhr.onreadystatechange = function () {
       if (this.readyState == 4) {
          var json = JSON.parse(this.responseText);
          var str = JSON.stringify(json,undefined,2);
          //console.log(json);
          //console.log(json['businesses'])
          var businessData=json['businesses'];
          for(var i=0;i<businessData.length;i++)
          {
            var data = businessData[i];
            displayData(data)
          }
      }
   };

   xhr.send(null);
}

function displayData(data){
            
   var imageData =data['image_url'];
   var url = data['url'];
   var nameData = data['name'];
   var priceData = data['price'];
   var ratingData = data['rating'];
   var addressData = data['location']['address1']+" "+data['location']['address2']+", "+data['location']['city']+", "+data['location']['country'];
   var phoneData = data['phone'];

   var catg = data['categories'];
   var finalCatg = ''
   for( var j=0;j< catg.length;j++){
      finalCatg+=catg[j]['title']+', '
   }
  
   //document.getElementById("output").innerHTML = imageData+"<br>"+nameData+"<br>"+titleData+"<br>"
   //+priceData+"<br>"+ratingData+"<br>"+addressData+"<br>"+phoneData+"<br>"+finalCatg+"<br>"+url;
   var outIputd = document.getElementById("output");
   
   //add image
   var imgElem = document.createElement('img');
   imgElem.setAttribute(
      'src',
      imageData
    ); 
   imgElem.setAttribute('height', 200); 
   imgElem.setAttribute('width', 200);

   //add name and URL link
   var createURL = document.createElement('a');
   var URLText = document.createTextNode(nameData);
   createURL.setAttribute('href', url);
   createURL.setAttribute('target', "_blank");//to open in new tab
   createURL.appendChild(URLText);
   var nameElem = document.createElement("div");
   nameElem.appendChild(createURL);

   //create price 
   var priceElem = document.createElement("div");
   var priceDisplay = "Price : ";
   if (priceData!= undefined ){
      priceDisplay +=priceData
   }
   
   var priceContent = document.createTextNode(priceDisplay);
   priceElem.appendChild(priceContent);

   //create rating 
   var rateElem = document.createElement("div");
   var rateContent = document.createTextNode("Rating : "+ratingData);
   rateElem.appendChild(rateContent);

   //create address 
   var addressElem = document.createElement("div");
   var addressContent = document.createTextNode("Address : "+addressData);
   addressElem.appendChild(addressContent);

   //create phone 
   var phoneElem = document.createElement("div");
   var phoneContent = document.createTextNode("Phone : "+phoneData);
   phoneElem.appendChild(phoneContent);

   //create category 
   var catgElem = document.createElement("div");
   var catgContent = document.createTextNode("Category : "+finalCatg);
   catgElem.appendChild(catgContent);


   var parentDiv = document.createElement("div");
   parentDiv.style.cssText += 'width:300px;height:400px;float:left;border: 1px solid black; padding: 5px; margin: 2px;';
   parentDiv.appendChild(imgElem);
   parentDiv.appendChild(nameElem);
   parentDiv.appendChild(priceElem);
   parentDiv.appendChild(rateElem);
   parentDiv.appendChild(addressElem);
   parentDiv.appendChild(phoneElem);
   parentDiv.appendChild(catgElem);
   outIputd.appendChild(parentDiv);

}


