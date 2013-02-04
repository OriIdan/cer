<?PHP
/*
 | CER - Cloud Ebook Reading application
 | Written by: Ori Idan <ori@heliconbooks.com>
 | Originally written in order to add book preview to Helicon books store (http://store.heliconbooks.com)
 |
 | Theory of operation:
 | The main file (index.php) reades the template (default: reader.html) and displays it.
 | Before the closing tag </body> it reads the EPUB file, parses the package file (.opf file).
 | The package file parsing creates a bunch of javaScript variables and objects.
 | These JavaScript variables and objects are used later by the reader JavaScript.
 | The actual reader is operated by JavaScript and AJAX (used for reading the HTML files of the book).
 */
session_start();

$template = "reader.html";

$fname = $fname = isset($_GET['fname']) ? $_GET['fname'] : '';

$epubfile = "../files/$fname";	/* This is according to Helicon books store file structure */

$endbody = <<<EOE
<script src="reader.js"></script>
<script type="text/javascript">
ShowTitle();
ShowInfo();
ShowPage();
</script>
EOE;


$file = fopen($template, "rt");
if(!$file) {
	print "<h1>Error opening $template</h1>\n";
	exit;
}
while(!feof($file)) {
	$str = fgets($file, 1024);
	if(preg_match("/<\/body>/", $str)) {
		print "<script type=\"text/javascript\">\n";
		/*
		 | Call epubread.php to read EPUB file.
		 | EPUB file name is specified in $epubfile (set beforehand)
		 */
		require('epubread.php');
		print "</script>\n";
		print $endbody;
	}
	print "$str";
}

?>

