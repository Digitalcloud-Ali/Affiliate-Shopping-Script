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

$colname_rsshops1 = "-1";
if (isset($_GET['shopid'])) {
  $colname_rsshops1 = $_GET['shopid'];
}
mysql_select_db($database_shops, $shops);
$query_rsshops1 = sprintf("SELECT * FROM shops WHERE shopid = %s", GetSQLValueString($colname_rsshops1, "int"));
$rsshops1 = mysql_query($query_rsshops1, $shops) or die(mysql_error());
$row_rsshops1 = mysql_fetch_assoc($rsshops1);
$totalRows_rsshops1 = mysql_num_rows($rsshops1);

// Load the common classes
require_once('includes/common/KT_common.php');

// Defining List Recordset variable

$colname_rsproducts1 = "-1";
if (isset($_GET['catename'])) {
  $colname_rsproducts1 = $_GET['catename'];
}

?>
<?php include("includes/config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Redirecting To.... <?php echo $row_rsshops1['shopname']; ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
<!--
.style32 {
	font-size: 14px;
	font-weight: bold;
}
.descriptionlight {color: #737B86; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.stylecates {color: #5C9DBF; font-family: Arial, Helvetica, sans-serif; font-size: 11px; text-decoration:none;}
.stylecates:hover {color: #D99801; font-family: Arial, Helvetica, sans-serif; font-size: 11px;text-decoration:none; }
.refineby2 {color: #564E3A; font-family: Arial, Helvetica, sans-serif; font-size: 11px; text-decoration:none; }
.style33 {
	font-size: 10px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #666666;
}
.style35 {color: #990000}
.style36 {font-size: 14px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; }
-->
</style>
<script language="JavaScript">
<!--Script courtesy of http://www.rayice.com - Design Like You Dream It
var time = null
function move() {
window.location = '<?php echo $row_rsshops1['shoplink']; ?>'
}
//-->
</script>
</head>

<body onload="timer=setTimeout('move()',5000)">
<p align="center" class="style33 style35"><span class="style36"><span class="style32">&nbsp;</span></span><?php echo $row_rsshops1['shopname']; ?> Is Being Open</p>
<p align="center" class="descriptionlight style32"><img src="/images/loading.gif" width="16" height="16" /></p>
<p align="center" class="descriptionlight style32">Thank You For Using <?php echo $domainname ?> . We Provide The Perfect Shopping Solution</p>
<p align="center" class="descriptionlight style32"><?php echo $row_rsshops1['shopname']; ?> Will Be Open Shorly</p>
<p align="center" class="descriptionlight style32">Visit Us Again To Find Some Great Products &amp; Shopping Stores</p>
<div align="center">
  <table height="21" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="1" bgcolor="#CCCCCC"></td>
    </tr>
    <tr>
      <td><span class="refineby2"> Not Redirected Auto? Go Directly To</span> <a href="<?php echo $row_rsproducts1['productlink']; ?>" class="stylecates"><?php echo $row_rsshops1['shopname']; ?></a></td>
    </tr>
  </table>
</div>
</body>
</html>
<?php
mysql_free_result($rsshops1);
?>