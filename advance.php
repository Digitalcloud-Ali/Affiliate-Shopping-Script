<?php require_once('includes/shops.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the required classes
require_once('includes/tfi/TFI.php');
require_once('includes/tso/TSO.php');
require_once('includes/nav/NAV.php');

// Make unified connection variable
$conn_shops = new KT_connection($shops, $database_shops);

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

// Filter
$tfi_listproducts3 = new TFI_TableFilter($conn_shops, "tfi_listproducts3");
$tfi_listproducts3->addColumn("products.productname", "STRING_TYPE", "productname", "%");
$tfi_listproducts3->addColumn("products.productid", "NUMERIC_TYPE", "productid", "=");
$tfi_listproducts3->addColumn("products.programlogo", "STRING_TYPE", "programlogo", "%");
$tfi_listproducts3->addColumn("products.programname", "STRING_TYPE", "programname", "%");
$tfi_listproducts3->addColumn("products.currency", "STRING_TYPE", "currency", "%");
$tfi_listproducts3->addColumn("products.productinfo", "STRING_TYPE", "productinfo", "%");
$tfi_listproducts3->addColumn("products.productdescription", "STRING_TYPE", "productdescription", "%");
$tfi_listproducts3->addColumn("products.productlink", "STRING_TYPE", "productlink", "%");
$tfi_listproducts3->addColumn("products.productimage", "STRING_TYPE", "productimage", "%");
$tfi_listproducts3->addColumn("products.productbrand", "STRING_TYPE", "productbrand", "%");
$tfi_listproducts3->addColumn("products.productprice", "STRING_TYPE", "productprice", "%");
$tfi_listproducts3->Execute();

// Sorter
$tso_listproducts3 = new TSO_TableSorter("rsproducts1", "tso_listproducts3");
$tso_listproducts3->addColumn("products.productname");
$tso_listproducts3->addColumn("products.productid");
$tso_listproducts3->addColumn("products.programlogo");
$tso_listproducts3->addColumn("products.programname");
$tso_listproducts3->addColumn("products.currency");
$tso_listproducts3->addColumn("products.productinfo");
$tso_listproducts3->addColumn("products.productdescription");
$tso_listproducts3->addColumn("products.productlink");
$tso_listproducts3->addColumn("products.productimage");
$tso_listproducts3->addColumn("products.productbrand");
$tso_listproducts3->addColumn("products.productprice");
$tso_listproducts3->setDefault("products.productname");
$tso_listproducts3->Execute();

// Navigation
$nav_listproducts3 = new NAV_Regular("nav_listproducts3", "rsproducts1", "", $_SERVER['PHP_SELF'], 10);

mysql_select_db($database_shops, $shops);
$query_sections = "SELECT * FROM cates ORDER BY `section` ASC";
$sections = mysql_query($query_sections, $shops) or die(mysql_error());
$row_sections = mysql_fetch_assoc($sections);
$totalRows_sections = mysql_num_rows($sections);

// Defining List Recordset variable

mysql_select_db($database_shops, $shops);
$query_stores = "SELECT * FROM shops ORDER BY shopname ASC";
$stores = mysql_query($query_stores, $shops) or die(mysql_error());
$row_stores = mysql_fetch_assoc($stores);
$totalRows_stores = mysql_num_rows($stores);

$maxRows_newstores = 10;
$pageNum_newstores = 0;
if (isset($_GET['pageNum_newstores'])) {
  $pageNum_newstores = $_GET['pageNum_newstores'];
}
$startRow_newstores = $pageNum_newstores * $maxRows_newstores;

mysql_select_db($database_shops, $shops);
$query_newstores = "SELECT * FROM shops ORDER BY shopid DESC";
$query_limit_newstores = sprintf("%s LIMIT %d, %d", $query_newstores, $startRow_newstores, $maxRows_newstores);
$newstores = mysql_query($query_limit_newstores, $shops) or die(mysql_error());
$row_newstores = mysql_fetch_assoc($newstores);

if (isset($_GET['totalRows_newstores'])) {
  $totalRows_newstores = $_GET['totalRows_newstores'];
} else {
  $all_newstores = mysql_query($query_newstores);
  $totalRows_newstores = mysql_num_rows($all_newstores);
}
$totalPages_newstores = ceil($totalRows_newstores/$maxRows_newstores)-1;

$maxRows_popularproducts = 10;
$pageNum_popularproducts = 0;
if (isset($_GET['pageNum_popularproducts'])) {
  $pageNum_popularproducts = $_GET['pageNum_popularproducts'];
}
$startRow_popularproducts = $pageNum_popularproducts * $maxRows_popularproducts;

mysql_select_db($database_shops, $shops);
$query_popularproducts = "SELECT * FROM products ORDER BY productprice DESC";
$query_limit_popularproducts = sprintf("%s LIMIT %d, %d", $query_popularproducts, $startRow_popularproducts, $maxRows_popularproducts);
$popularproducts = mysql_query($query_limit_popularproducts, $shops) or die(mysql_error());
$row_popularproducts = mysql_fetch_assoc($popularproducts);

if (isset($_GET['totalRows_popularproducts'])) {
  $totalRows_popularproducts = $_GET['totalRows_popularproducts'];
} else {
  $all_popularproducts = mysql_query($query_popularproducts);
  $totalRows_popularproducts = mysql_num_rows($all_popularproducts);
}
$totalPages_popularproducts = ceil($totalRows_popularproducts/$maxRows_popularproducts)-1;

//NeXTenesio3 Special List Recordset
$maxRows_rsproducts1 = $_SESSION['max_rows_nav_listproducts3'];
$pageNum_rsproducts1 = 0;
if (isset($_GET['pageNum_rsproducts1'])) {
  $pageNum_rsproducts1 = $_GET['pageNum_rsproducts1'];
}
$startRow_rsproducts1 = $pageNum_rsproducts1 * $maxRows_rsproducts1;

// Defining List Recordset variable
$NXTFilter_rsproducts1 = "1=1";
if (isset($_SESSION['filter_tfi_listproducts3'])) {
  $NXTFilter_rsproducts1 = $_SESSION['filter_tfi_listproducts3'];
}
// Defining List Recordset variable
$NXTSort_rsproducts1 = "products.productname";
if (isset($_SESSION['sorter_tso_listproducts3'])) {
  $NXTSort_rsproducts1 = $_SESSION['sorter_tso_listproducts3'];
}
mysql_select_db($database_shops, $shops);

$query_rsproducts1 = "SELECT * FROM products WHERE {$NXTFilter_rsproducts1} ORDER BY {$NXTSort_rsproducts1}";
$query_limit_rsproducts1 = sprintf("%s LIMIT %d, %d", $query_rsproducts1, $startRow_rsproducts1, $maxRows_rsproducts1);
$rsproducts1 = mysql_query($query_limit_rsproducts1, $shops) or die(mysql_error());
$row_rsproducts1 = mysql_fetch_assoc($rsproducts1);

if (isset($_GET['totalRows_rsproducts1'])) {
  $totalRows_rsproducts1 = $_GET['totalRows_rsproducts1'];
} else {
  $all_rsproducts1 = mysql_query($query_rsproducts1);
  $totalRows_rsproducts1 = mysql_num_rows($all_rsproducts1);
}
$totalPages_rsproducts1 = ceil($totalRows_rsproducts1/$maxRows_rsproducts1)-1;
//End NeXTenesio3 Special List Recordset

$nav_listproducts3->checkBoundries();

//End NeXTenesio3 Special List Recordset
?>
<?php include("includes/config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $domainname ?> - Advanced Search Engine</title>
<meta name="description" content="<?php echo $domainname ?> - Search The Premium Products Using Advance Search Engine." />
<meta name="keywords" content="advance , search , product , products , By Product , By Program Name , By Currency , With In Description , By Brand , By Price" />
<link href="/includes/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/includes/preload.js"></script>
</head>

<body onload="MM_preloadImages('/images/downproduct.gif','/images/downshops.gif')"><table width="1003" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
<?php include("includes/header.php"); ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="20" height="416" valign="top">&nbsp;</td>
    <td width="300" valign="top"><?php include('includes/cates.php'); ?>
      <?php include('includes/rightmenu.php'); ?>      </td>
    <td width="7" valign="top"></td>
    <td width="655" valign="top"><form id="form1" name="form1" method="post" action="/search.php?">
        <table width="203%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="style9">Advance Search :</td>
          </tr>
        </table>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="topborder">
          <tr>
            <td width="9" height="111">&nbsp;</td>
            <td><br />
              <table width="621" border="0" cellspacing="0" cellpadding="5">
                <tr>
                  <td width="120"><div align="right"><strong><span class="refineby2">By Product : </span></strong></div></td>
                  <td width="481"><input name="tfi_listproducts3_productname" type="text" class="form" id="tfi_listproducts3_productname2" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                  <td><div align="right"><strong><span class="refineby2">By Program Name : </span></strong></div></td>
                  <td><input name="tfi_listproducts3_programname" type="text" class="form" id="tfi_listproducts3_programname" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                  <td><div align="right"><strong><span class="refineby2">By Currency : </span></strong></div></td>
                  <td><input name="tfi_listproducts3_currency" type="text" class="form" id="tfi_listproducts3_currency" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                  <td><div align="right"><strong><span class="refineby2">With In Description : </span></strong></div></td>
                  <td><input name="tfi_listproducts3_productdescription" type="text" class="form" id="tfi_listproducts3_productdescription" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                  <td><div align="right"><strong><span class="refineby2">By Brand : </span></strong></div></td>
                  <td><input name="tfi_listproducts3_productbrand" type="text" class="form" id="tfi_listproducts3_productbrand" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                  <td><div align="right"><strong><span class="refineby2">By Price : </span></strong></div></td>
                  <td><input name="tfi_listproducts3_productprice" type="text" class="form" id="tfi_listproducts3_productprice" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><input name="tfi_listproducts" type="submit" class="button" value="Search" />&nbsp;<input name="tfi_listproducts3" type="hidden" id="tfi_listproducts3" value="<?php echo NXT_getResource("Filter"); ?>" /></td>
                </tr>
              </table>
                <br /></td>
            </tr>
        </table>
          </form></td>
    <td width="21">&nbsp;</td>
  </tr>
</table>
<?php include("includes/footer.php"); ?></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsproducts1);

mysql_free_result($sections);

mysql_free_result($stores);

mysql_free_result($newstores);

mysql_free_result($popularproducts);
?>
