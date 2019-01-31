<?php require_once('includes/shops.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_report = "-1";
if (isset($_GET['productid'])) {
  $colname_report = $_GET['productid'];
}
mysql_select_db($database_shops, $shops);
$query_report = sprintf("SELECT * FROM products WHERE productid = %s", GetSQLValueString($colname_report, "int"));
$report = mysql_query($query_report, $shops) or die(mysql_error());
$row_report = mysql_fetch_assoc($report);
$totalRows_report = mysql_num_rows($report);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $row_report['productname']; ?></title>
<link href="styles.css" rel="stylesheet" type="text/css" />
<link href="includes/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
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
	<?php
if($_GET['productid'] > '0' && $_GET['section'] == $row_report['section'] )
{

?>
	<form id="form1" name="form1" method="post" action="includes/broken_pro.php">
	  <table width="100%" border="0" cellpadding="0" cellspacing="3" bgcolor="#F3F3F3">
        <tr>
          <td height="58" valign="top" bgcolor="#F3F3F3" class="refineby2"><span class="stylesmalldark"><strong> Title :</strong> <?php echo $row_report['productname']; ?></span> <br />
              <span class="stylesmalldark"><strong>Section :</strong></span><span class="stylesmallorgane"> <?php echo $row_report['section']; ?></span> <br />
              <span class="stylesmalldark"><strong>Category :</strong></span><span class="stylesmallorgane"> <?php echo $row_report['catename']; ?></span></td>
        </tr>
      </table>
	  <p align="center" class="descriptionlight"> <span class="stylesmalldark">You are about to reporting the following content ( <?php echo $row_report['productname']; ?> ) is broken or not working ?</span><span class="stylesmall"><br />
            <br />
            <input type="submit" name="button" id="button" value="report" />
        </span><strong>
        <input name="productid" type="hidden" id="productid" value="<?php echo $row_report['productid']; ?>" />
        </strong>
        <input name="title" type="hidden" id="title" value="<?php echo $row_report['productname']; ?>" />
        <input name="section" type="hidden" id="section" value="<?php echo $row_report['section']; ?>" />
        <input name="cates" type="hidden" id="cates" value="<?php echo $row_report['catename']; ?>" />
      </p>
</form>
<?php
}
else
{
}
if($_GET['productid'] == '0' && $_GET['section'] == '' )
{
?>
<div align="center"><span class="refineby1">Nothing is here</span></div>
<?php
}
else
{
}
?>
<?php
if($_GET['status'] == 'success')
{
	$i = 0;
?>
<div align="center" class="descriptionlight"><span class="stylesmalldark">Success - Thank you for taking time to report us broken contents</span><br />
<a href="javascript:window.close();" class="listlinks1">Close Window</a></div>
<?php
}
else
{
}
?>
</body>
</html>
<?php
mysql_free_result($report);
?>
