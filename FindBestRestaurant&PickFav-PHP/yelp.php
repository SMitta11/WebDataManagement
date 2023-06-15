<?php
session_start();
$city=$_GET["city"];
$searchterms = $_GET["searchterms"];
$storeid=$_GET["store"];

//put values of city and searchterms in session variable 
if (isset($city)){
  $_SESSION["city"]=$city;
}
if (isset($searchterms)){
  $_SESSION['searchvalue'] = $searchterms;
}

if($_SERVER['REQUEST_METHOD'] == "GET" and array_key_exists('Reset', $_GET)) {
  session_destroy();
  $_SESSION["city"] = "";
  $_SESSION['favorites']= array();
  $_SESSION["currentsearch"] = array();
  $_SESSION["search"] = array();
}
?>
<!DOCTYPE html>


<html>
    <head>
<body> 

    <form  method="GET"> 
 
    <label>City: <input type="text" name="city" required value="<?php if(isset($_SESSION["city"])){echo $_SESSION["city"];} ?>"></input></label><br/>
    <label>SearchTerms: <input type="text" name="searchterms"/></label><br/>

       <input type="submit" name="Find" value="Find"/>
       <input type="submit" name="Reset"  value="Reset"/>
</form>
       
   
</body>   
</head>      
</html>

<?php


//session_destroy();



//echo $_SESSION["city"];
//echo $storeid;
if (!isset($_SESSION['favorites'])) {
  $_SESSION['favorites']=array();
}
if (isset($storeid) && !in_array($storeid, $_SESSION['favorites'])){
  array_push($_SESSION['favorites'],$storeid);
}

//print_r($_SESSION['favorites']);

//print_r($_SESSION["city"]);

if($_SERVER['REQUEST_METHOD'] == "GET" and array_key_exists('Find', $_GET)) {
  find();
}



function find()
{
  $API_KEY = 'DmwziXQVNb3J5R12P7v3oo-x5XUpshybYANewkeRGVh_o4-BJO00wPZpSLVEnq3t1pdbDuISa3QH0E09DPquyR5LRD2F6AKkcnTrHgV9XiHV764iqF115v1Cz5g2Y3Yx';
  $API_HOST = "https://api.yelp.com";
  $SEARCH_PATH = "/v3/businesses/search";
  $BUSINESS_PATH = "/v3/businesses/";
  $curl = curl_init();
  if (FALSE === $curl)
     throw new Exception('Failed to initialize');
  $url = $API_HOST . $SEARCH_PATH . "?" . "location=" . $_SESSION["city"] ."&term=" .$GLOBALS['searchterms']."&limit=10";
  //print $url;
  curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $API_KEY,
                "cache-control: no-cache",
            ),
        ));
  $response = curl_exec($curl);
  curl_close($curl);

$json = json_decode($response, true);
//store current session from search data
$_SESSION["currentsearch"] = array();

//store 
foreach($json['businesses'] as $item){
  $id = $item['id'];
  $name = $item['name'];
  $image_url = $item['image_url'];
  $price = $item['price'];
  $rating = $item['rating'];
  $address = $item['location']['address1']." ".$item['location']['address2']." ".$item['location']['city']." ".$item['location']['country'];
  $phone = $item['phone'];
  $category = "";
  foreach($item['categories'] as $itemcategory)
  {
    $category = $$category. $itemcategory['title'].', ';
  }
  //store current session from search data
  $_SESSION["currentsearch"][$id] = array("name" => $name, "image_url" => $image_url, "price"=> $price, "rating" => $rating, "address" => $address, "phone" => $phone, "category" => $category);

  //store all data in session 
  if (!isset($_SESSION["search"])) {
    $_SESSION["search"][$id] = array("name" => $name, "image_url" => $image_url, "price"=> $price, "rating" => $rating, "address" => $address, "phone" => $phone, "category" => $category);
  }
  else{
    //store all data in session
    if(!in_array($id, $_SESSION['search'])){
      $_SESSION["search"][$id] = array("name" => $name, "image_url" => $image_url, "price"=> $price, "rating" => $rating, "address" => $address, "phone" => $phone, "category" => $category);
    }
  }
}
}

?>
<style type="text/css">
.flex-container {
    display: flex;
}
.flex-child {
    flex: 1;
}
.flex-child:first-child {
    border-right: 1px solid ;
    margin-right: 20px;
}
</style>
<div class="flex-container">
<div class="flex-child ">
  <h3>Search Results</h3>
<?php
foreach ($_SESSION["currentsearch"] as $id=>$value)
{
?>
<div style="padding-top:10px;border-top:1px solid;"><a href="yelp.php?store=<?=$id?>"> 
  <img src="<?=$value['image_url']?>" alt="" width="200" height="200">
</a></div>
<div><?=$value['name']?></div>

<div>Price: <?=$value['price']?></div>
<div>Raing: <?=$value['rating']?></div>
<div>Category: <?=$value['category']?></div> 
<div>Address: <?=$value['address']?></div> 
<div>Phone: <?=$value['phone']?></div>
<?php
}
?>
</div>
<div class="flex-child ">
<h3>Favorites</h3>
<?php
foreach ($_SESSION["favorites"] as $key) 
{
  foreach ($_SESSION["search"] as $id=>$value) 
  {
    if($key == $id){
      ?>
      <div style="padding-top:10px;border-top:1px solid;"><img src="<?=$value['image_url']?>" alt="" width="200" height="200"></div>
      <div><?=$value['name']?></div>
      
      <div>Price: <?=$value['price']?></div>
      <div>Raing: <?=$value['rating']?></div>
      <div>Category: <?=$value['category']?></div> 
      <div>Address: <?=$value['address']?></div> 
      <div>Phone: <?=$value['phone']?></div>

      <?php
    }
  }
}
?>
</div>
</div>