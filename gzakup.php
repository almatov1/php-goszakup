<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>gosZakup Parser</title>
	<script src="https://kit.fontawesome.com/2d27021a71.js" crossorigin="anonymous"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<script type="text/javascript">
	function makeMoney(n) {
    return parseFloat(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1 ");
	}	
</script>

<div class="container" style="margin-top: 25px;margin-bottom: 25px;">
	<div class="row">
	    <div class="col text-center">
	      <h1><i class="fa-solid fa-copyright" style="color: #0277BD;" id="titleIcon"></i> Parser </h1>
	    </div>
	  </div>
	  <br>
	  <hr>
	  <br>
<?php

$contract_id = ['190240037042/210136/02',
	'190240037042/210106/02',
	'190240037042/210124/02',
	'190240037042/210308/02',
	'190240037042/210237/02',
	'180540031270/210057/01',
	'180540031270/210056/01',
	'190240037042/210269/02',
	'190240037042/210242/02',
	'190240037042/210272/02',
	'190240037042/210253/02',
	'190240037042/210252/02',
	'180540031082/210107/01',
	'180540031082/210120/02'];

function getPost($query) {
	$headers = array(
			  "Content-Type: application/json",
			  "Authorization: Bearer",
	);
	$endpoint = "https://ows.goszakup.gov.kz/v3/graphql";

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $endpoint);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

	foreach($contract_id as $item) {
		$json = getPost('{"query": "query ($filter: ContractFiltersInput) {  Contract(filter: $filter) { id Customer {      nameRu    }    Supplier {      nameRu    }    contractSum    rootId  }}",
  "variables": {
    "filter": {
      "contractNumberSys": "'.$item.'"
    }
}}');
		$res = json_decode($json, true);
		$mainId = $res['data']['Contract'][0]['id'];

		$tblCNameRu = $res['data']['Contract'][0]['Customer']['nameRu'];
		$tblSNameRu = $res['data']['Contract'][0]['Supplier']['nameRu'];
		$tblSum = $res['data']['Contract'][0]['contractSum'];

?>
<table class="table table-dark">
  <thead>
    <tr>
      <th scope="col">Заказщик</th>
      <th scope="col">Поставщик</th>
      <th scope="col">Сумма</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $tblCNameRu ?></td>
      <td><?php echo $tblSNameRu ?></td>
      <td><script type="text/javascript">document.write(makeMoney(<?php echo $tblSum ?>));</script></td>
    </tr>
  </tbody>
</table>

<table class="table">
  <thead>
    <tr>
      <th scope="col">Дата</th>
      <th scope="col">Сумма</th>
      <th scope="col">Ссылка</th>
    </tr>
  </thead>
  <tbody>

<?php

		$rootId = $res['data']['Contract'][0]['rootId'];
				$actJson = getPost('{"query": "query ($filter: ContractActFiltersInput) {  Acts(filter: $filter) {aktDate  statusNameRu  id    Place {      amount    }  }}",
  "variables": {
    "filter": {
      "contractRootId": '.$rootId.'
    }
  }}');
				$actRes = json_decode($actJson, true);

				foreach($actRes['data']['Acts'] as $item) {
	   				$secondId = $item['id'];
?>
		<tr>
      <td><?php echo $item['aktDate'] ?></td>
      <td><script type="text/javascript">document.write(makeMoney(<?php echo $item['Place'][0]['amount'] ?>));</script></td>
      <td><a href="https://www.goszakup.gov.kz/ru/egzcontract/cpublic/akts/<?php echo $mainId ?>/akt/<?php echo $secondId ?>"><?php echo $item['statusNameRu'] ?></td>
    </tr>
<?php

				}   

				?>
  </tbody>
</table>
				<?php

		echo '<br>';
		echo '<br>';
	}
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>
</html>