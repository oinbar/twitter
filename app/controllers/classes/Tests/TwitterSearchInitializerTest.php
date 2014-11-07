<?php















include __DIR__.'/../TwitterSearchInitializer.php';

$transformations_params = array('sutime' => 'param1', 'calais' => 'param2');
$feedId = '6';
$access_token = '2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC';
$access_token_secret = 'sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk';
$consumer_key = 'x393VwuVLnnixX6Ld7panxSp8';
$consumer_secret = 'qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u';
$criteria = urlencode('"have lice" OR "son has lice" OR "daughter has lice"');

//$transformations_params = array();
//$feedId = 6;
//$access_token = '2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC';
//$access_token_secret = 'sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk';
//$consumer_key = 'x393VwuVLnnixX6Ld7panxSp8';
//$consumer_secret = 'qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u';
//$criteria = 'lice';



//$tsi = new TwitterSearchInitializer($transformations_params, $feedId, $access_token, $access_token_secret, $consumer_key, $consumer_secret, $criteria);
//
//$doc = json_decode($tsi->run(), true);
//
//echo print_r($doc);
//echo print_r(array_keys($doc));
//
//assert(in_array('transformations', array_keys($doc)));
//assert(in_array('statuses', array_keys($doc)));
//assert($tsi->getNextTransformation() == 'sutime');
//
//
//
$transformations_params = array('sutime' => 'param1');
reset($transformations_params);
$first_key = key($transformations_params);
unset($transformations_params[$first_key]);


$tsi = new TwitterSearchInitializer($transformations_params, $feedId, $access_token, $access_token_secret, $consumer_key, $consumer_secret, $criteria);

$doc = json_decode($tsi->run(), true);

echo $tsi->getNextTransformation();