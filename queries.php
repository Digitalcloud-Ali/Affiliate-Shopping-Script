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

mysql_select_db($database_shops, $shops);
$query_rsproducts1 = "SELECT * FROM products ORDER BY productid DESC";
$rsproducts1 = mysql_query($query_rsproducts1, $shops) or die(mysql_error());
$row_rsproducts1 = mysql_fetch_assoc($rsproducts1);
$totalRows_rsproducts1 = mysql_num_rows($rsproducts1);
?>
<?php require_once('includes/shops.php'); ?>
<?php
// Defining List Recordset variable

//End NeXTenesio3 Special List Recordset
?>
<?php include("includes/config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $domainname ?> - Search Queries</title>
<meta name="description" content="<?php echo $domainname ?> - Queries" />
<meta name="keywords" content="Shortcuts , store , products , shops , quick stores , quick shops" />
<link href="/includes/style.css" rel="stylesheet" type="text/css">
</head>

<body><table width="1003" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="659" valign="top"><p class="pagestitle"><span class="pagestitle">Queries : <br />
      </span>
      </p>
      <table width="310" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="10">&nbsp;</td>
          <td width="300"><?php do { ?>
              <span class="stylecates"><span class="arrows">›</span> </span><span class="stylecates3"><a href="/products/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>.html" class="stylecates3"><?php echo $row_rsproducts1['productname']; ?></a></span><br />
              <?php } while ($row_rsproducts1 = mysql_fetch_assoc($rsproducts1)); ?></td>
        </tr>
      </table>      </td>
    </tr>
</table>
    </td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsproducts1);
?>