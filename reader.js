/*
 | JavaScript functions for CER
 | Written by: Ori Idan <ori@heliconbooks.com>
 */
function loadAjax(url,mydiv) {
	var xmlhttp;
	
	document.getElementById(mydiv).innerHTML = '<div style="text-align:center"><img src="images/ajax-loader.gif" /></div>';
	
	if(window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function() {
		if((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			document.getElementById(mydiv).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}

function ShowTitle() {	
	if(lang == 'he') {
		document.getElementById('title').innerHTML = '<div dir="rtl">' + title + '</div>';
	}
	else {
		document.getElementById('title').innerHTML = '<div dir="ltr">' + title + '</div>';
	}
	document.title = title;
}

function ShowInfo() {
	var t;

	if(lang == 'he') {
		t = '<div dir="rtl">';
	}
	else {
		t = '<div dir="ltr">';
	}
	t = t + '<img src="' + manifest[coverid] + '" alt="cover image" style="width:80%" >';
	t = t + '<h3>' + publisher + '</h3><h1>' + title + '</h1><h2>' + author + '</h2>';
	t = t + '<div>' + description + '</div>';
	t = t + '</div>';
	document.getElementById('info').innerHTML = t;
}

function ShowComments() {
	var article = fname + '_' + chapter;
	
	loadAjax('comments.php?article=' + article + '&lang=' + lang, 'comments');
	document.getElementById('article').value = article;
	if(lang == 'he') {
		document.getElementById('addcommentstr').innerHTML = '<img src="images/comment_add.png">&nbsp;הוספת תגובה<br />';
		document.getElementById('addcommentstr').dir = "rtl";
		document.getElementById('name').placeholder = "שם";
		document.getElementById('name').dir = "rtl";
		document.getElementById('submit').value = "שליחה";
		document.getElementById('comment').dir = "rtl";
		document.getElementById('comments').dir = "rtl";
	}
}

function ShowPage() {
	var id = spine[chapter];
	var filename = manifest[id];
	
	width = $('#page').width();
	height = $('#page').height();
	loadAjax('ajax-backend.php?id=page&file=' + filename + '&width=' + width + '&height=' + height + '&bookname=' + fname + '&chapter=' + chapter, 'page');
	ShowComments();
}

function next_page(id, opid) {
	if(chapter >= spine.length)
		return;
	chapter = chapter + 1;
	if((chapter + 1) >= spine.length) {
		document.getElementById(id).style.display = 'none';
	}
	else {
		document.getElementById(id).style.display = 'block';
	}
	document.getElementById(opid).style.display = 'block';
	ShowPage();
}

function prev_page(id, opid) {
	 if(chapter > 0) {
	 	chapter = chapter - 1;
	 	if(chapter == 0) {
	 		document.getElementById(id).style.display = 'none';
		}
		else {
			document.getElementById(id).style.display = 'block';
		}	
 		document.getElementById(opid).style.display = 'block';
	 	ShowPage();
	 }
}

function left() {
	if(lang == 'he')
		next_page('leftbt', 'rightbt');
	else
		prev_page('leftbt', 'rightbt');
}

function right() {
	if(lang == 'he')
		prev_page('rightbt', 'leftbt');
	else
		next_page('rightbt', 'leftbt');
}

if(lang == 'he')
	document.getElementById('rightbt').style.display = 'none';
else
	document.getElementById('leftbt').style.display = 'none';
