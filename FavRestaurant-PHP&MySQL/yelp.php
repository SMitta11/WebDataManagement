<?php
//$testStoreId =  "11121";
$dbh = new PDO(
  "mysql:host=localhost:3306;dbname=yelp",
  "root",
  "",
  array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);


session_start();
$city = $_GET["city"];
$searchterms = $_GET["searchterms"];
$storeid = $_GET["store"];

//put values of city and searchterms in session variable 
if (isset($city)) {
  $_SESSION["city"] = $city;
}
if (isset($searchterms)) {
  $_SESSION['searchvalue'] = $searchterms;
}

if ($_SERVER['REQUEST_METHOD'] == "GET" and array_key_exists('Reset', $_GET)) {
  session_destroy();

  $_SESSION["city"] = "";

  $_SESSION["currentsearch"] = array();
 
}
?>
<!DOCTYPE html>
<html>
<head>
<body>
  <form method="GET">

    <label>City: <input type="text" name="city" required value="<?php if (isset($_SESSION["city"])) {
                                                                  echo $_SESSION["city"];
                                                                } ?>"></input></label><br />
    <label>SearchTerms: <input type="text" name="searchterms" /></label><br />
    <input type="submit" name="Find" value="Find" />
    <input type="submit" name="Reset" value="Reset" />
  </form>
</body>
</head>
</html>

<?php


if ($_SERVER['REQUEST_METHOD'] == "GET" and array_key_exists('Find', $_GET)) {
  find();
}

//save favourites in database
if (isset($storeid)) {
  saveFavorites();
}

function saveFavorites()
{
  //check if store id already exists
  $dbh = $GLOBALS['dbh'];
  $dbh->beginTransaction();
  $stmt = $dbh->prepare('select * from favorites where id=?');
  $storeid = $GLOBALS['storeid'];
  $stmt->execute([$storeid]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    //echo "not found";
     //if store id not found insert into db
    foreach($_SESSION["currentsearch"] as $key=>$value){
      if ($storeid == $key)
      {
        //echo $value['name'];
        
        
        $stmt = $dbh->prepare("INSERT INTO favorites (id,name,image_url,yelp_page_url,categories,price,rating,address,phone) VALUES (:id,:name,:image_url,
        :yelp_page_url,:category,:price,:rating,:address,:phone)");
    
        $stmt->bindParam(':id', $key);
        $stmt->bindParam(':name',$value['name']);
        $stmt->bindParam(':image_url',$value['image_url']);
        $stmt->bindParam(':yelp_page_url',$value['yelp_page_url']);
        $stmt->bindParam(':category',$value['category']);
        $stmt->bindParam(':price',$value['price']);
        $stmt->bindParam(':rating',$value['rating']);
        $stmt->bindParam(':address',$value['address']);
        $stmt->bindParam(':phone',$value['phone']);
    
        $stmt->execute();
        $dbh->commit();
        
      }
      

    //
  } 
}
  //else {
    //echo 'found';
  //}

   

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
  $url = $API_HOST . $SEARCH_PATH . "?" . "location=" . $_SESSION["city"] . "&term=" . $GLOBALS['searchterms'] . "&limit=10";
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
  foreach ($json['businesses'] as $item) {
    $id = $item['id'];
    $name = $item['name'];
    $image_url = $item['image_url'];
    $yelp_page_url = $item['url'];
    $price = $item['price'];
    $rating = $item['rating'];
    $address = $item['location']['address1'] . " " . $item['location']['address2'] . " " . $item['location']['city'] . " " . $item['location']['country'];
    $phone = $item['phone'];
    $category = "";
    foreach ($item['categories'] as $itemcategory) {
      $category = $$category . $itemcategory['title'] . ', ';
    }
    //store current session from search data
    $_SESSION["currentsearch"][$id] = array("name" => $name, "image_url" => $image_url,"yelp_page_url"=>$yelp_page_url ,"price" => $price, "rating" => $rating, "address" => $address, "phone" => $phone, "category" => $category);

    
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
    border-right: 1px solid;
    margin-right: 20px;
  }
</style>
<div class="flex-container">
  <div class="flex-child ">
    <h3>Search Results</h3>
    <?php
    foreach ($_SESSION["currentsearch"] as $id => $value) {
    ?>
      <div style="padding-top:10px;border-top:1px solid;"><a href="yelp.php?store=<?= $id ?>">
          <img src="<?= $value['image_url'] ?>" alt="" width="200" height="200">
        </a></div>
      <div><?= $value['name'] ?></div>

      <div>Price: <?= $value['price'] ?></div>
      <div>Raing: <?= $value['rating'] ?></div>
      <div>Category: <?= $value['category'] ?></div>
      <div>Address: <?= $value['address'] ?></div>
      <div>Phone: <?= $value['phone'] ?></div>
    <?php
    }
    ?>
  </div>
  <div class="flex-child ">

    <h3>Favorites</h3>
    <?php
    //display favorites from db
    $stmt = $dbh->prepare("SELECT * FROM favorites");
    $stmt->execute();
    
    $rows = $stmt->fetchAll();
    //print_r($rows);
    foreach($rows as $row)
    {
    
    ?>
        <div style="padding-top:10px;border-top:1px solid;">
          <a href="<?=$row['yelp_page_url']?>">
          <img src="<?= $row['image_url'] ?>" alt="" width="200" height="200">
          </a>
        </div>
          <div><?= $row['name'] ?></div>
          <div>Price: <?= $row['price'] ?></div>
          <div>Raing: <?= $row['rating'] ?></div>
          <div>Category: <?= $row['categories'] ?></div>
          <div>Address: <?= $row['address'] ?></div>
          <div>Phone: <?= $row['phone'] ?></div>

    <?php
        
    }
    
    ?>
  </div>
</div>