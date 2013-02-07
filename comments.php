<?PHP
/*
 | Comments add and display
 | This is the only part based on a database in this software.
 |
 | TODO: Base it on an XML file
 |
 | Database structure is one table with the following scheme:
 |	* article VARCHAR(60)
 |  * name VARCHAR(80)
 |  * time DATETIME
 |  * comment TEXT
 */
include('config.inc.php');	/* Database connection */

$lang = isset($_GET['lang']) ? $_GET['lang'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$article = isset($_GET['article']) ? $_GET['article'] : '';

function DoQuery($query, $debugstr) {
	global $sql_link;

	if($sql_link)
		$result = mysql_query($query, $sql_link);
	else
		$result = mysql_query($query);
	if(!$result) {
		print "$debugstr Query: $query<br>\n";
		echo mysql_error();
		exit;
	}
	return $result;
}

if($action == 'doaddcomment') {
	$article = htmlspecialchars($_POST['article'], ENT_QUOTES);
	$comment = htmlspecialchars($_POST['comment'], ENT_QUOTES);
	$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
	
	$query = "INSERT INTO newsys_comments (article, name, time, comment) ";
	$query .= "VALUES('$article', '$name', NOW(), '$comment')";
	DoQuery($query, __LINE__);
}

if($action == 'addcomment') {
	print "<script src=\"ajaxsubmit.js\"></script>\n";
	$article = htmlspecialchars($_GET['article'], ENT_QUOTES);
	$l = _("Add comment");
	print "<div id=\"addcommentstr\" dir=\"ltr\">\n";
	print "<img src=\"images/comment_add.png\" alt=\"\">&nbsp;$l<br />\n";
	print "</div>\n";
	print "<form name=\"commentform\" method=\"post\" onsubmit=\"xmlhttpPost('comments.php?action=doaddcomment', 'commentform', 'comments'); return false;\">\n";
	print "<textarea name=\"comment\" id=\"comment\" style=\"width:100%;height:4em\"></textarea>\n";
	print "<input type=\"hidden\" id=\"article\" name=\"article\" value=\"$article\">\n";
	print "<input type=\"hidden\" id=\"lang\" name=\"lang\" value=\"$lang\">\n";
	$l = _("Name");
	print "<input type=\"text\" id=\"name\" placeholder=\"$l\" name=\"name\" style=\"width:100%\"/>\n";
	$l = _("Sumbit");
	print "<div style=\"width:100%;text-align:center\"><input type=\"submit\" id=\"submit\" class=\"btn\" value=\"$l\" /></div>\n";
	print "</form>\n";
	return;
}

$query = "SELECT * FROM newsys_comments WHERE article='$article' ORDER BY `time` DESC";
$result = DoQuery($query, __LINE__);
while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$name = $line['name'];
	$t = $line['time'];
	$comment = nl2br($line['comment']);
	print "<div>$comment\n";
	print "<div style=\"color:gray;margin-top:10px\">$name &nbsp;&nbsp;$t</div>\n";
	print "</div>\n";
}

