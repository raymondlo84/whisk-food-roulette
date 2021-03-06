<!DOCTYPE html>
<?php
#include all environmental variables
include("env.php");

if(empty($API_KEY)){
	echo "Please Request for API Key before start";
	exit();
}

$ingredent_list = array();
$food_image_url_list = array();
function jwt_request_whisk_list($token, $list_id) {
	$ch = curl_init('https://graph.whisk.com/v1/'.$list_id);
	$authorization = "Authorization: Token ".$token; 
	// Inject the token into the header
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization )); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch); // Execute the cURL statement
	curl_close($ch); // Close the cURL connection
	return json_decode($result); // Return the received data
}
$json_decoded = jwt_request_whisk_list($API_KEY, '106b865abf7985e4ead8a3cfd782e876385');
#print_r($json_decoded);

#get a list of ingredient items
$ingredient_items = $json_decoded->items;

?>
<html>
<head>
<meta charset="utf-8">
<title>Whisk Food Roulette</title>
<link rel="stylesheet" href="roulette.js/sample/bootstrap.css"></link>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/ui-lightness/jquery-ui.css">
<link rel="stylesheet" href="roulette.js/sample/bootstrap-responsive.css"></link>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/ui-lightness/jquery-ui.css">
<link rel="stylesheet" href="roulette.js/sample/demo.css"></link>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
<script src="/roulette.js/roulette.js"></script>
<script src="/roulette.js/sample/demo.js"></script>

</head>
<body>
<a href="https://whi.sk/UxAEl">
<img src="whisk.png" height=30px /> 
</a>
<?php
echo "<table><tr>";
for ($i=0; $i < 3; $i++){
	echo "\n<td width=320px><div class=\"roulette\" style=\"display:none;\">\n";
	$ingredient_items_randomized = $ingredient_items;
	shuffle($ingredient_items_randomized);
	$k = 0; 
	foreach($ingredient_items_randomized as $ingredient){
		$food_image_url = $ingredient->imageUrl;
		$food_id = $ingredient->id;
		$food_name = $ingredient->analysis->canonicalName;
		echo "<img class=\"food_items\" src=\"".$food_image_url."\" width=300px alt=$k id=\"".$food_id."\" name=\"".$food_name."\">\n";
		$k=$k+1;
	}
	echo "\n</div></td>\n";
}
echo "</tr></table>";
?>

<script>
function onClickUberEats(){
  run_roulette("ubereats");
}
function onClickGrubHub(){
  run_roulette("grubhub");
}
function run_roulette(engine) {
  document.getElementById("ubereats_button").disabled = true;
  document.getElementById("grubhub_button").disabled = true;

  const x = document.getElementsByClassName("food_items");
  console.log(x.length);
  var i;
  for(i=0; i<x.length; i++){
	console.log(i+" "+x[i].name);
  }
  var num_items = x.length/3-1;
  var select_id = Math.floor(Math.random() * num_items); 
  console.log("Selecting" + select_id);
  var p = {
		startCallback : function() {
		},
		slowDownCallback : function() {
		},
		stopCallback : function($stopElm) {
        }
  }
  p['speed'] = 50;
  p['duration'] = 1;
  p['stopImageNumber'] = select_id;
  $('div.roulette').roulette('option', p);
  $('div.roulette').roulette('start');
  var i;
  var ingredients="";
  for(i=0; i<x.length; i++){
	//console.log(x[i].alt);
	if(x[i].alt == select_id){
		console.log(x[i].name);
		ingredients = ingredients + x[i].name + " ";
	}
  }
  var duration = 5000 + num_items*200;
  console.log('timeout:'+duration);
  setTimeout(function(){
    if(engine == "ubereats"){
    	window.open('https://www.ubereats.com/search?q='+ingredients, '_self');
    }else if(engine == "grubhub"){
    	window.open('https://www.grubhub.com/search?orderMethod=delivery&locationMode=DELIVERY&facetSet=umamiV2&pageSize=20&hideHateos=true&searchMetrics=true&preciseLocation=true&facet=open_now%3Atrue&queryText='+ingredients, '_self');
    }
  }, duration);
}
</script>

<input type="image" onclick="onClickUberEats()" id="ubereats_button" src="https://www.greenbaglunch2go.com/wp-content/uploads/2019/06/button-ubereats.png" height=120px/>
<input type="image" onclick="onClickGrubHub()" id="grubhub_button" src="http://pages.r.grubhub.com/rs/927-DYO-696/images/GHORDERLINKBUTTON.png" height=120px/>
<span>Powered by <a href="http://www.whisk.com">Whisk.com</a></span>
</body>
</html>
