<?php
	
define('THUMBNAILS_DIR',__DIR__."/thumbnails");
define('DEFAULT_PROXY', "socks5://192.168.1.2:9050");

include  __DIR__."/NotORM/NotORM.php";
/* 
CREATE TABLE "animelist" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
	"title"	TEXT UNIQUE,
	"sec_title"	NUMERIC,
	"year"	INTEGER,
	"score"	REAL,
	"es_score"	REAL,
	"real_id"	INTEGER,
	"url"	TEXT,
	"path"	TEXT,
	"popularity"	INTEGER,
	"members"	INTEGER,
	"rank"	INTEGER,
	"favorites"	INTEGER,
	"synopsis"	TEXT,
	"added_time"	TEXT,
	"genres"	TEXT
)
*/
$pdo = new PDO("sqlite:data.db");
$db = new NotORM($pdo);

if(!function_exists('dd')){
	function dd(){
		$call=( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS)[0]);
		if (php_sapi_name() != "cli") {
			echo "<pre>";
		}
		echo "\n### {$call['file']}:{$call['line']} ###\n";
		call_user_func_array("var_dump",func_get_args()) ;die;
	}
}	


function get($url,$params=null) {
	$options = [
		CURLOPT_RETURNTRANSFER => true, // return web page
		CURLOPT_HEADER => false, // don't return headers
		CURLOPT_FOLLOWLOCATION => true, // follow redirects
		CURLOPT_ENCODING => "", // handle all encodings
		CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)", // who am i
		CURLOPT_AUTOREFERER => true, // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 10, // timeout on connect
		CURLOPT_TIMEOUT => 5, // timeout on response
		CURLOPT_MAXREDIRS => 2, // stop after 10 redirects
		CURLOPT_SSL_VERIFYPEER => false, // Disabled SSL Cert checks,

		// CURLOPT_NOBODY			=>	true,

		CURLOPT_PROXY => DEFAULT_PROXY,
		// CURLOPT_PROXYPORT => $proxyport,
		// CURLOPT_PROXYTYPE => $prxTypeMap[$proxyProto], 
	];

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

function getRelatedData($keyword){
	/* output :
	array(8) {
	  ["id"]=>
	  int(10213)
	  ["type"]=>
	  string(5) "anime"
	  ["name"]=>
	  string(32) "Maji de Watashi ni Koi Shinasai!"
	  ["url"]=>
	  string(67) "https://myanimelist.net/anime/10213/Maji_de_Watashi_ni_Koi_Shinasai"
	  ["image_url"]=>
	  string(97) "https://cdn.myanimelist.net/r/116x180/images/anime/4/32541.jpg?s=0f4f3eb4d58fda49b094c86083ac6dd2"
	  ["thumbnail_url"]=>
	  string(96) "https://cdn.myanimelist.net/r/116x76/images/anime/4/32541.jpg?s=cdb1230636d44be2c1f4d9e63419da29"
	  ["payload"]=>
	  array(5) {
		["media_type"]=>
		string(2) "TV"
		["start_year"]=>
		int(2011)
		["aired"]=>
		string(27) "Oct 2, 2011 to Dec 18, 2011"
		["score"]=>
		string(4) "6.86"
		["status"]=>
		string(15) "Finished Airing"
	  }
	  ["es_score"]=> float(3.794895)
	} 
	*/
		
	
	$response= get("https://myanimelist.net/search/prefix.json",[
			'type'=>'anime',
			'keyword'=>$keyword,
			'v'=>1
		]
	);
	if($response['errno']!=0){
		echo "fetch failed! error: $response[errmsg]\n";
		return null;
	}
	$data=json_decode($response['content'],true);
	$data= ($data['categories'][0]['items'][0]);
	
	$jikanLink="http://api.jikan.moe/v3/anime/{$data["id"]}";
	//echo "$jikanLink\n";
	$jikanRes=get($jikanLink);
	$jikanData=@json_decode($jikanRes['content'],true);
	if($jikanData['status']==404){
		
		echo "jikan api fetch failed! error: {$jikanData['message']}\n";
		return null;
	}
	//print_r($jikanData);
	$data['jikanData']=$jikanData;
	
	
	return $data;
}

function fetchByNameQuery($name,$path){
	Global $db;
	echo str_pad (  "\t {$name} " , 50  ,".") ;
			$data=getRelatedData($name);
			
			//dd(implode(' / ',$data['jikanData']['title_synonyms']));
			// echo $data["id"]."\n";
			if(!@$data["id"]){
			   echo "fetch failed!\n";
			   return;
			}
		   
			$tumbPath=THUMBNAILS_DIR."/{$data["id"]}.jpg";
			if(!(file_exists($tumbPath) && filesize($tumbPath)>0)){
				$imageUrl=str_replace('/r/116x180','',$data["image_url"]);
				$imageData=get($imageUrl)['content'];
				file_put_contents($tumbPath,$imageData);
			}
			
			
			if($db->animelist("real_id = ?", $data["id"])->fetch()){
				echo " already exists!\n";
			}else{
				$insertData=[
					'real_id'=>$data['id'],
					'title'=>$data['name'],
					'url'=>$data['url'],
					'year'=>$data['payload']['start_year'],
					'score'=>$data['payload']['score'],
					'es_score'=>$data['es_score'],
					'path'=>$path,
					
					'sec_title'=>$data['jikanData']['title_english'],
					'popularity'=>$data['jikanData']['popularity'],
					'members'=>$data['jikanData']['members'],
					'rank'=>$data['jikanData']['rank'],
					'favorites'=>$data['jikanData']['favorites'],
					'synopsis'=>$data['jikanData']['synopsis'],
					'added_time'=>date("Y-m-d H:i:s",filemtime($path)),
					'genres'=>implode(', ',array_column($data['jikanData']['genres'],'name')),
				];
				
				
				$r=$db->animelist()->insert($insertData);
				// dd($insertData);
				echo " was insterted!\n";
				//print_r($insertData);
				
			}
}

