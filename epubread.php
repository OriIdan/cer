<?PHP
/*
 | Extract EPUB file
 | It is assumed that $epubfile is set by the calling script.
 |
 | This scripts extracts the EPUB file and parses the package file (.opf file).
 | Parsing the package file creates a bunch of JavaScript variables and objects used later by the reader code.
 |
 | EPUB is parsed into a dirctory under the 'ebub' directory.
 | This is a special directory that is chosen for this session.
 | Each session will have it's own directory so that one file will not overwrite the other.
 | Note that directory epub must be under the current directory and current user must have permissions to write.
 | We first create a temprary directory and store it in the session.
 | It's up to the server to clean old directory, in next version cleaning logic will be added.
 */

function CreateTmpDir($base) {
	if($_SESSION['epubdir']) {
		return $_SESSION['epubdir'];
	}
	$i = 1;
	while(!mkdir("$base/$i", 0777, true))
		$i++;
	$_SESSION['epubdir'] = "$base/$i";
	return "$base/$i";
}

$epubdir = CreateTmpDir('epub');
// print "tmpdir: $epubdir<br />\n";
/* Unzip epub file */
system("unzip -o $epubfile -d $epubdir > /dev/null");
/*
 | Analyze EPUB contents
 */
libxml_use_internal_errors(true);
$xmlstring = file_get_contents("$epubdir/META-INF/container.xml");
$xmlstring = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xmlstring); 
$xml = simplexml_load_string($xmlstring);
if(!$xml) {
	print "<h2>Failed loading container.xml</h2>\n";
	foreach(libxml_get_errors() as $error) {
		echo $error->message;
		print "<br />\n";
	}
	exit;
}
$rootfile = $xml->rootfiles;
$root = $rootfile->rootfile;
foreach($root->attributes() as $k => $v) {
	if($k == 'full-path') {
		$rootfile = "$epubdir/$v";
		break;
	}
}
// print "rootfile: $rootfile<br />\n";
$basedir = dirname($rootfile);
print "var basedir = \"$basedir\";\n";
// print "<br />\n";
/* We now have package file, analyze it */
$xmlstring = file_get_contents($rootfile);
$xmlstring = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xmlstring); 
$xml = simplexml_load_string($xmlstring);
if(!$xml) {
	print "<h2>Failed loading .opf file</h2>\n";
	foreach(libxml_get_errors() as $error) {
		echo $error->message;
		print "<br />\n";
	}
	exit;
}
$meta = $xml->metadata;
$title = htmlspecialchars($meta->dctitle, ENT_QUOTES);
$author = htmlspecialchars($meta->dccreator, ENT_QUOTES);
$publisher = htmlspecialchars($meta->dcpublisher, ENT_QUOTES);
$lang = $meta->dclanguage;
$description = htmlspecialchars($meta->dcdescription, ENT_QUOTES);
$description = preg_replace("/\n/", "<br />", $description);
print "var title = \"$title\";\n";
print "var author = \"$author\";\n";
print "var publisher = \"$publisher\";\n";
print "var lang = \"$lang\";\n";
print "var description = \"$description\";\n";
$coverid = '';
for($i = 0; $i < 20; $i++) {
	if($meta->meta[$i]) {
		$attr = $meta->meta[$i]->attributes();
		$state = 0;
		foreach($attr as $k => $v) {
			if(($k == 'name') && ($v == 'cover')) {
				$state = 1;
			}
			if(($k == 'content') && ($state == 1)) {
				$coverid = $v;
				break;
			}
		}
	}
	else
		break;
	if($coverid)
		break;
}
print "var coverid = \"$coverid\";\n";
$ma = $xml->manifest;
print "var manifest = {";
$i = 0;
foreach($ma->item as $k => $v) {
	$id = $href = NULL;
	foreach($v->attributes() as $k1 => $v1) {
		if($k1 == 'id')
			$id = $v1;
		if($k1 == 'href')
			$href = $v1;
		if($id && $href) {
			if($i)
				print ", ";
			print "\"$id\": \"$basedir/$href\"";
			$i++; 
			$id = NULL;
			$href = NULL;
		}
	}
}
print "};\n";

// print "<br />\n";

print "var spineatrr = {";
$i = 0;
$sp = $xml->spine;
foreach($sp->attributes() as $k => $v) {
	if($i)
		print ", ";
	print "\"$k\": \"$v\"";
	$i++;
}
print "};\n";
// print "<br />\n";
print "var spine = [";
$itemref = $sp->itemref;
$i = 0;
foreach($itemref as $k => $v) {
	foreach($v->attributes() as $v1) {
		if($i)
			print ", ";
		print "\"$v1\"";
		$i++;
	}
}
print "];\n";
print "var chapter = 1;\n";
?>

