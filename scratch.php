<?php

function curlGet($url,$params=null,$userAgent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)",$proxy=null) {
	$options = [
		CURLOPT_RETURNTRANSFER => true, // return web page
		CURLOPT_HEADER => false, // don't return headers
		CURLOPT_FOLLOWLOCATION => true, // follow redirects
		CURLOPT_ENCODING => "", // handle all encodings
		CURLOPT_USERAGENT => $userAgent, // who am i
		CURLOPT_AUTOREFERER => true, // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 10, // timeout on connect
		CURLOPT_TIMEOUT => 5, // timeout on response
		CURLOPT_MAXREDIRS => 2, // stop after 10 redirects
		CURLOPT_SSL_VERIFYPEER => false, // Disabled SSL Cert checks,

		// CURLOPT_NOBODY			=>	true,
		// CURLOPT_PROXYPORT => $proxyport,
		// CURLOPT_PROXYTYPE => $prxTypeMap[$proxyProto], 
	];

	if(!empty($proxy)){
		$options[CURLOPT_PROXY]=$proxy;
	}
	
	if($params){
		$url.='?' . http_build_query($params);
	}
	
	$ch = curl_init($url);
	curl_setopt_array($ch, $options);
	$content = curl_exec($ch);
	$err = curl_errno($ch);
	$errmsg = curl_error($ch);
	$info = curl_getinfo($ch); // Time spent downloading
	curl_close($ch);

	//print_r($info);

	$time = $info['total_time_us'] - $info['namelookup_time_us'];

	$response['time'] = (int)($time / 1000);
	$response['errno'] = $err;
	$response['errmsg'] = $errmsg;
	$response['content'] = $content;

	return $response;
}


function getGoogleFirstResultUrl($q){
	$ie6UserAgent="Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 5.2)";
	
	$q=urlencode($q);
	$url="https://www.google.com/search?q=$q";
	$res=curlGet($url,null,$ie6UserAgent);

	preg_match('/href="\/url\?q=(?<url>[^&]+)&/', $res['content'], $match);

	// Print the entire match result
	return @($match['url']);	
}

function getMalIdByQuery($q)
{
	$q="$q anime site:myanimelist.net";
	$url=(getGoogleFirstResultUrl($q));
	
	preg_match('/myanimelist\.net\/\w+\/(?<id>\d+)\//i', $url, $match);

	// Print the entire match result
	return @($match['id']);
}


var_dump(getMalIdByQuery("enen no shouboutai"));

