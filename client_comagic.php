<?php

include_once("restclient.php");

function client_comagic($api_url,$api_username,$api_password,$date_from,$date_till)
{
	$api = new RestClient([
		'base_url' => $api_url
		,'headers' =>['Accept'=>"application/json"]
		//,'curl_options'=>array(CURLOPT_FOLLOWLOCATION=>true)
		//,'format' => "json"
	]);

	//allows you to receive decoded JSON data as an array.
	$api->register_decoder('json', function($data){
		return json_decode($data, TRUE);
	});

	/*
	Для авторизации в системе используется функция login.
	http://api.comagic.ru/api/login/?login=login&password=password
	*/

	$result = $api->get("login/", ['login' => $api_username,'password'=>$api_password]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		die( "Authorization Error\n" );
	}

	//var_dump($result);
	//foreach($result as $key => $value)
	//    var_dump($value);

	$res=$result->decode_response();
	//var_dump($res);
		
	if(!$res['success'])
	{
		echo 'message=;'.$res['message']."\n";
		die("Authorization failed\n");
	}

	$session_key=$res['data']['session_key'];

	echo "session_key=".$session_key."\n";

	if( strlen($session_key)==0)
	{
		die("session_key null length!\n");
	}

	/*
	Функция call возвращает информацию по звонкам.
	http://api.comagic.ru/api/v1/call/?session_key=2271a5c46bfe6cdadfd1a6daebdd3b9e&date_from=2017-02-27%2000:00:00&date_till=2017-02-28%2000:00:00
	*/
	$date=date("Ymd");

	$result = $api->get("v1/call/", ['session_key' => $session_key
									,'date_from'=>$date_from
									,'date_till'=>$date_till]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		die( "API Call Error\n" );
	}

	$res=$result->decode_response();
	//var_dump($res);
		
	if(!$res['success'])
	{
		echo 'message=;'.$res['message']."\n";
		echo "API Call failed\n";
	}
	else
	{
		//echo $result->response;
		//print_r($res['data']);
		//var_dump($res->data);
		$data=$res['data'];
		//echo "count=".count($data)."\n";
		//print_r(array_keys($data[0]));
	}

	/*
	Для завершения сессии используется функция logout.
	http://api.comagic.ru/api/logout/?session_key=05e765dc1ac3901fe1b57b865924271d
	*/

	$result = $api->get("logout/", ['session_key' => $session_key]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		echo( "Logout Error\n" );
	}
	else
		echo "Logout success ".($result->decode_response()['success'] ? "true":"false"."\n");

	return $data;
}

function client_api_init($api_url)
{
	$api = new RestClient([
		'base_url' => $api_url
		,'headers' =>['Accept'=>"application/json"]
		//,'curl_options'=>array(CURLOPT_FOLLOWLOCATION=>true)
		//,'format' => "json"
	]);

	//allows you to receive decoded JSON data as an array.
	$api->register_decoder('json', function($data){
		return json_decode($data, TRUE);
	});
	
	return $api;
}

function client_comagic_login($api,$api_username,$api_password)
{
	if($api==null)
		return false;
	
	/*
	Для авторизации в системе используется функция login.
	http://api.comagic.ru/api/login/?login=login&password=password
	*/

	$result = $api->get("login/", ['login' => $api_username,'password'=>$api_password]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		die( "Authorization Error\n" );
	}

	$res=$result->decode_response();
		
	if(!$res['success'])
	{
		echo 'message=;'.$res['message']."\n";
		die("Authorization failed\n");
	}

	$session_key=$res['data']['session_key'];
	
	return $session_key;
}

//================================================
function comagic_api_logout($api,$session_key)
{
	if($api==null)
		return false;
	
	/*
	Для завершения сессии используется функция logout.
	http://api.comagic.ru/api/logout/?session_key=05e765dc1ac3901fe1b57b865924271d
	*/

	$result = $api->get("logout/", ['session_key' => $session_key]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		echo( "Logout Error\n" );
		return false;
	}
	else
		echo "Logout success ".($result->decode_response()['success'] ? "true":"false"."\n");

	return true;
}

function client_comagic_calls($api,$session_key,$date_from,$date_till,$site_id=null)
{
	if($api==null)
		return null;
	
	/*
	Функция call возвращает информацию по звонкам.
	http://api.comagic.ru/api/v1/call/?session_key=05e765dc1ac3901fe1b57b865924271d&date_from=2014-12-10%2017:10:00&date_till=2014-12-10%2017:20:00
	*/

	$result = $api->get("v1/call/", ['session_key' => $session_key
									,'date_from'=>$date_from
									,'date_till'=>$date_till]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		die( "API Call Error\n" );
	}

	$res=$result->decode_response();
		
	if(!$res['success'])
	{
		echo 'message=;'.$res['message']."\n";
		echo "API Call failed\n";
	}
	else
	{
		$data=$res['data'];
	}

	return $data;
}

function client_comagic_ac($api,$session_key,$site_id=null,$customer_id=null)
{
	if($api==null)
		return null;
	
	/*
	Для получения списка рекламных кампаний используется функция ac.
	http://api.comagic.ru/api/v1/ac/?session_key=05e765dc1ac3901fe1b57b865924271d
	*/

	$result = $api->get("v1/ac/", ['session_key' => $session_key
									/*,'site_id'=>$site_id
									,'customer_id'=>$customer_id*/]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		die( "API ac Error\n" );
	}

	$res=$result->decode_response();
		
	if(!$res['success'])
	{
		echo 'message=;'.$res['message']."\n";
		echo "API Ac failed\n";
	}
	else
	{
		$data=$res['data'];
	}

	return $data;
}

function client_comagic_site($api,$session_key,$customer_id=null)
{
	if($api==null)
		return null;
	
	/*
	Для получения списка сайтов аккаунта используется функция site.
	http://api.comagic.ru/api/v1/site/?session_key=05e765dc1ac3901fe1b57b865924271d
	*/

	$result = $api->get("v1/site/", ['session_key' => $session_key
									/*,'customer_id'=>$customer_id*/]);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		die( "API site Error\n" );
	}

	$res=$result->decode_response();
		
	if(!$res['success'])
	{
		echo 'message=;'.$res['message']."\n";
		echo "API site failed\n";
	}
	else
	{
		$data=$res['data'];
	}

	return $data;
}

?>