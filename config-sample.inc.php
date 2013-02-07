<?PHP
/*
 | Database configuration
 | To create this file, rename the file config-sample.inc.php and modify $user, $pswd and $database
 */
$host='localhost';
$user = '';
$pswd = '';
$database='';

$sql_link = mysql_connect($host, $user, $pswd) or die("Could not connect to host $host");
mysql_select_db($database) or die("Could not select database: $database");
mysql_query ("set character_set_client='utf8'");
mysql_query ("set character_set_results='utf8'");
mysql_query ("set collation_connection='utf8_general_ci'");

