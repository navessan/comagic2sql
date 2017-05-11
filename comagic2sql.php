<?php

/* Default database settings*/
$database_type = "sqlsrv";
$database_default = "medialog";
$database_hostname = "localhost";
$database_username = "sa";
$database_password = "password";
$database_port = "";

$debug=0;
/* display ALL errors */
error_reporting(E_ALL);

/* Include configuration */
include("config.php");

include("client_comagic.php");

if (isset($_REQUEST['phpinfo']))
{
	phpinfo();
	die( "exit!" );
}
if (isset($_REQUEST['debug']))
{
	$debug=1;
}
//----------------------------
//����������� � Comagic
$stage="Auth";

$api=client_api_init($api_url);
$session_key=client_comagic_login($api,$api_username,$api_password);

if( strlen($session_key)==0)
{
	die("session_key null length!\n");
}else
	echo $stage." OK\n";

//------------------
//����������� � SQL-����
$stage="DB connect";

if($database_type=="sqlsrv")
	$dsn = "$database_type:server=$database_hostname;database=$database_default";
else 	
	$dsn = "$database_type:host=$database_hostname;dbname=$database_default;charset=$database_charset";

$opt = array(
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
	$conn = new PDO($dsn, $database_username, $database_password, $opt);
}
catch(PDOException $e) {
	die($e->getMessage());
}
echo $stage." OK\n";

//----------------------------
//�������� �� ���������� �������
$stage="Calls DB exist check:";

//$date=date("Y-m-d");

$date = new DateTime();
$date->modify('-1 day');
$date=$date->format('Y-m-d');

//echo $date."\n";

$date_from=$date.' 00:00:00';
$date_till=$date.' 23:59:59';

//$date_from='2017-03-01 00:00:00';
//$date_till='2017-03-01 00:00:00';
//------------------
echo $stage." period $date_from - $date_till\n";

$sql="select count(*) as cnt
 from US_WEB_COMAGIC_CALLS 
 where 
 convert(datetime,call_date,120)> convert(datetime, :date_from, 120) and 
 convert(datetime,call_date,120)< convert(datetime, :date_till, 120) ";
 
$r=array('date_from' =>$date_from
		,'date_till' =>$date_till); 
 
$st = $conn->prepare($sql);
$st -> execute($r);
$data=$st->fetchAll();
//print_r($data);

if(count($data)>0 && $data[0]['cnt']>0)
{
	echo $stage." Calls already imported for period $date_from - $date_till.\n";
	echo $stage." Count=".$data[0]['cnt']."\n";
	die( $stage." Exit\n");
}

echo $stage." OK\n";

//-------------------
//��������� �������
$stage="Calls client:";

$data=client_comagic_calls($api,$session_key,$date_from,$date_till);
//print_r($data);

echo ($stage." Data from client count=".count($data)."\n");
if(count($data)==0)
	die($stage." No Data, exiting\n");

//-----------------------------------------------
//����������� ������� ��� �������	
$fields_array=array(
	'id'=>array("sql"=>"int")						// id ���������
    ,'call_date'=>array("sql"=>"varchar")			// ����� ������
    ,'session_start'=>array("sql"=>"varchar")		// ����� ������ ������, ��������� �� �������
    ,'communication_type'=>array("sql"=>"varchar") // ��� ������: call - ������� ������, sitephone - ������ � ��������
    ,'status'=>array("sql"=>"varchar")				// ������ ������ (��������(normal)/�����������(lost))
    ,'numa'=>array("sql"=>"varchar")				// � ������ ������ ������� (���)
    ,'numb'=>array("sql"=>"varchar")				// �� ����� ����� ������� (����� ������)
    ,'wait_time'=>array("sql"=>"int")				// ����� ��������
    ,'duration'=>array("sql"=>"int")				// ����������������� ���������
    ,'file_link'=>array("sql"=>"text","type"=>"array")	// ������ �� ������ ���������,
    ,'operator_name'=>array("sql"=>"varchar")		// ��������
    ,'coach_name'=>array("sql"=>"varchar")			// ������
    ,'scenario_name'=>array("sql"=>"varchar")		// �������� �������� 
    ,'is_transfer'=>array("sql"=>"varchar")			// ��������
    ,'tags'=>array("sql"=>"text","type"=>"array")	// ������ id ������������� �����
    ,'communication_number'=>array("sql"=>"int")	// ����� ��������� (�������������� � �������� ������� �� ���� ����� ������������)
    ,'site_id'=>array("sql"=>"int")					// id �����
    ,'ac_id'=>array("sql"=>"int")					// id ��������� ��������
    ,'visitor_id'=>array("sql"=>"int")				// id ����������
    ,'visitor_type'=>array("sql"=>"varchar")		// ��� ����������
    ,'visits_count'=>array("sql"=>"int")			// ����� ���������� ��������� ����������
    ,'other_adv_contacts'=>array("sql"=>"varchar") // ���� ���������� ������� �� ���������� (� �������� �������) �� ������ ��������� ���������
    ,'country'=>array("sql"=>"varchar")				// ������
    ,'region'=>array("sql"=>"varchar")				// ������
    ,'city'=>array("sql"=>"varchar")				// �����
    ,'visitor_first_ac'=>array("sql"=>"int")		// id ������ ��������� ��������
    ,'search_engine'=>array("sql"=>"varchar")		// ��������� �������
    ,'search_query'=>array("sql"=>"text")			// ��������� ������
    ,'page_url'=>array("sql"=>"text")				// ����� ���������� ��������
    ,'referrer_domain'=>array("sql"=>"text")		// �����, � �������� ��� ������ �������
    ,'referrer'=>array("sql"=>"text")				// ����� ��������, � ������� ��� ������ �������
    ,'ua_client_id'=>array("sql"=>"text")			// User ID Universal Analytics
    ,'utm_campaign'=>array("sql"=>"text")			// �������� ����� utm
    ,'utm_content'=>array("sql"=>"text")
    ,'utm_medium'=>array("sql"=>"text")
    ,'utm_source'=>array("sql"=>"text")
    ,'utm_term'=>array("sql"=>"text")
    ,'os_ad_id'=>array("sql"=>"text")				// �������� ����� OpenStat
    ,'os_campaign_id'=>array("sql"=>"text")
    ,'os_service_name'=>array("sql"=>"text")
    ,'os_source_id'=>array("sql"=>"text")
    ,'gclid'=>array("sql"=>"text")					// �������� ����� gclid
    ,'yclid'=>array("sql"=>"text")					// �������� ����� yclid
    ,'ef_id'=>array("sql"=>"text")					// �������� ����� ef_id
    ,'session_id'=>array("sql"=>"int")				// id ������
    ,'sale_date'=>array("sql"=>"varchar")			// ���� ������
    ,'sale_cost'=>array("sql"=>"varchar")			// ����� ������
    ,'direction'=>array("sql"=>"varchar")			// ����������� ������. in - ��������, out - ���������
    ,'last_query'=>array("sql"=>"text")			// ���������� ��������� ������
    ,'is_visitor_by_numa'=>array("sql"=>"varchar") // ���� ���������� ��������� ������ ��� ���: true - ������ ��������� � ����������� ���������, false - ������ �� ���� ���������				
);

$table_name="US_WEB_COMAGIC_CALLS";	
$count=db_insert($conn,$data,$fields_array,$table_name);

echo $stage." inserted to ".$table_name." ".$count." rows.\n";

//-----------------------------------------------
//��������� ����� ������� � SQL
$stage="Calls SQL Update MEDIALOG_CALL_ID";

$sql="update [US_WEB_COMAGIC_calls] set MEDIALOG_CALL_ID=CALLS.CALLS_ID
FROM US_WEB_COMAGIC_calls web
join calls on calls.phone=web.numa and abs(datediff(MINUTE,convert(datetime,web.call_date,120),CALLS.CALL_DATETIME))<2
where 
 web.MEDIALOG_CALL_ID is null and 
 convert(datetime,call_date,120)> convert(datetime, :date_from, 120) and 
 convert(datetime,call_date,120)< convert(datetime, :date_till, 120) ";

$r=array('date_from' =>$date_from
		,'date_till' =>$date_till); 
 
$st = $conn->prepare($sql);
$st -> execute($r);
$count = $st->rowCount();
echo ($stage." count=".$count."\n");

//-----------------------------------------------
$stage="Calls SQL Update file_link";

$sql="update [US_WEB_COMAGIC_calls] set file_link='http:'+cast(file_link as varchar(max))
FROM US_WEB_COMAGIC_calls web
where
 datalength(file_link)>0 and
 file_link not like 'http%' and 
 convert(datetime,call_date,120)> convert(datetime, :date_from, 120) and 
 convert(datetime,call_date,120)< convert(datetime, :date_till, 120) ";

$r=array('date_from' =>$date_from
		,'date_till' =>$date_till); 
 
$st = $conn->prepare($sql);
$st -> execute($r);
$count = $st->rowCount();
echo ($stage." count=".$count."\n");

//-----------------------------------------------
//��������� ������ ��������� �������� 
$stage="AC get";

$data=client_comagic_ac($api,$session_key);
//print_r($data);	

echo ($stage." Data from client count=".count($data)."\n");
if(count($data)==0)
	die($stage." No Data, exiting\n");

//-----------------------------------------------
//������� ��������� �������
$stage="delete from US_WEB_COMAGIC_AC_TMP";

$sql="delete from US_WEB_COMAGIC_AC_TMP";

$st = $conn->prepare($sql);
$st -> execute();
$count = $st->rowCount();
echo ($stage." count=".$count."\n");

//-----------------------------------------------
//����������� ������� ��� �������	
$stage="AC TMP insert";

$fields_array=array(
	'id'=>array("sql"=>"int")				// id ��������� ��������
    ,'name'=>array("sql"=>"varchar") 			//�������� ��������� ��������
 );
	
$table_name="US_WEB_COMAGIC_AC_TMP";
$count=db_insert($conn,$data,$fields_array,$table_name);

echo $stage." inserted to ".$table_name." ".$count." rows.\n";
//-----------------------------------------------
//��������� ��������
//������� ������������� ������� 
$stage="AC insert from TMP";

$sql="insert into US_WEB_COMAGIC_AC
	(id,NAME)
select
id,NAME
from US_WEB_COMAGIC_AC_TMP
where id not in(
	select id
	from US_WEB_COMAGIC_AC
)";

$st = $conn->prepare($sql);
$st -> execute();
$count = $st->rowCount();
echo ($stage." count=".$count."\n");

//-----------------------------------------------
//���������� ��������
$stage="AC Update from TMP";

$sql="update old set NAME=tmp.NAME
from US_WEB_COMAGIC_AC old
join US_WEB_COMAGIC_AC_TMP tmp on old.ID=tmp.id
where old.NAME<>tmp.NAME";

$st = $conn->prepare($sql);
$st -> execute();
$count = $st->rowCount();
echo ($stage." count=".$count."\n");

//-----------------------------------------------
//��������� ������ ������ �������� ������������ ������� site. 
/*
$stage="Site get";

$data=client_comagic_site($api,$session_key);
//print_r($data);	

echo ($stage." Data from client count=".count($data)."\n");
if(count($data)==0)
	die($stage." No Data, exiting\n");

//-----------------------------------------------	
$stage="Site insert";

$fields_array=array(
	'id'=>array("sql"=>"int")				// id ��������� ��������
    ,'domain'=>array("sql"=>"varchar") 			//�������� ��������� ��������
 );
	
$table_name="US_WEB_COMAGIC_SITE_TMP";
$count=db_insert($conn,$data,$fields_array,$table_name);

echo $stage." inserted to ".$table_name." ".$count." rows.\n";
*/
//-----------------------------------------------
//close connection
comagic_api_logout($api,$session_key);

//-----------------------------------------------	
function db_insert($conn,$data,$fields_array,$table_name)	
{
	if( $conn==null || 
		$data==null ||
		$fields_array==null ||
		count($data)==0 ||
		count($fields_array)==0 ||
		strlen($table_name)==0
		)
		return false;
	
	//���������� �������� � SQL-�������
	
	$sql_columns="";
	$sql_vals="";
		
	foreach ($fields_array as $key => $value)
	{
		//print "$key :\n";
		//print_r($value);
		if (strlen($value['sql'])>0)
		{
			if (strlen($sql_columns)>0)
				$sql_columns.= "\t,";
			else 
				$sql_columns.= "\t";
			
			$sql_columns.= $key."\n";
			
			if (strlen($sql_vals)>0)
				$sql_vals.= "\t,";
			else $sql_vals.= "\t";
			
			$sql_vals.= ":".$key."\n";
		}
	}
	
	$sql="INSERT INTO [".$table_name."] (\n"
		.$sql_columns
		.") VALUES ("
		.$sql_vals
		.")";	

	//echo "sql=".$sql;
		
	$st = $conn->prepare($sql);
	
	$i=0;
	
    // loop through the array
    foreach ($data as $row) {
		//print_r($row);
		foreach ($fields_array as $key => $value)
		{
			if (strlen($value['sql'])>0)		
			{
				if(isset($row[$key]))
				{
					if(isset($value['type']))
					{
						if($value['type']=='json')
							$r[$key]=json_encode($row[$key]);
						else
							$r[$key]=implode(" ",$row[$key]);
					}
					else
						$r[$key]=$row[$key];
				}
				else
					$r[$key]=null;
			}
		}
		
		//print_r($r);
		$st->execute($r);
		$i=$i+$st->rowCount();
		//$i++;
    }
	return $i;
}

function validate_json($str=NULL) {
    if (is_string($str)) {
        @json_decode($str);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    return false;
}
	
/* sanitize_search_string - cleans up a search string submitted by the user to be passed
     to the database. NOTE: some of the code for this function came from the phpBB project.
   @arg $string - the original raw search string
   @returns - the sanitized search string */
function sanitize_search_string($string) {
	static $drop_char_match =   array('^', '$', '<', '>', '`', '\'', '"', '|', ',', '?', '~', '+', '[', ']', '{', '}', '#', ';', '!', '=');
	static $drop_char_replace = array(' ', ' ', ' ', ' ',  '',   '', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');

	/* Replace line endings by a space */
	$string = preg_replace('/[\n\r]/is', ' ', $string);
	/* HTML entities like &nbsp; */
	$string = preg_replace('/\b&[a-z]+;\b/', ' ', $string);
	/* Remove URL's */
	$string = preg_replace('/\b[a-z0-9]+:\/\/[a-z0-9\.\-]+(\/[a-z0-9\?\.%_\-\+=&\/]+)?/', ' ', $string);

	/* Filter out strange characters like ^, $, &, change "it's" to "its" */
	for($i = 0; $i < count($drop_char_match); $i++) {
		$string =  str_replace($drop_char_match[$i], $drop_char_replace[$i], $string);
	}

	$string = str_replace('*', ' ', $string);

	return $string;
}	
	
?>