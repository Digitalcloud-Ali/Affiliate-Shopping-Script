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
<title>Search - <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productname']); ?> | <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_programname']); ?> | <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productdescription']); ?></title>
<meta name="description" content="<?php echo $domainname ?> - Explore Product <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productname']); ?> | <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_programname']); ?> | <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productdescription']); ?>" />
<meta name="keywords" content="<?php echo $domainname ?>, <?php echo $domainnamespace ?> , product , shop , <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productname']); ?> , <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_programname']); ?> , <?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productdescription']); ?>" />
<link href="/includes/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/includes/preload.js"></script>
</head>

<body onload="MM_preloadImages('/images/downproduct.gif','/images/downshops.gif')"><table width="1003" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
<?php include("includes/header.php"); ?>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="22" valign="top">&nbsp;</td>
    <td width="250" valign="top"><?php include('includes/cates.php'); ?>
      <?php include('includes/rightmenu.php'); ?>    </td>
    <td width="5" valign="top"></td>
    <td width="659" valign="top"><form action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#DFE6EA">
            <tr>
              <td width="64%" height="0" bgcolor="#EBF0F3"><span class="style6">Sort By :</span> <span> <a href="<?php echo $tso_listproducts3->getSortLink('products.productname'); ?>" class="refineby">Name</a> <span class="style23">/</span> <span><span><span> <a href="<?php echo $tso_listproducts3->getSortLink('products.programname'); ?>" class="refineby">Program Name </a> <span class="style23">/</span> <span> <a href="<?php echo $tso_listproducts3->getSortLink('products.currency'); ?>" class="refineby">Currency</a> <span class="style23">/</span> <span><span><span><span><span> <a href="<?php echo $tso_listproducts3->getSortLink('products.productbrand'); ?>" class="refineby">Productbrand</a> <span class="style23">/</span> <span> <a href="<?php echo $tso_listproducts3->getSortLink('products.productprice'); ?>" class="refineby">Productprice</a> </span></span></span></span></span></span></span></span></span></span></span></td>
              <td width="30%" bgcolor="#EBF0F3" class="descriptionlight"><div align="center">&lt;&lt; Again Click : Will Be ASC / DESC </div></td>
            </tr>
            <tr>
              <td height="0" colspan="2" bgcolor="#EBF0F3"><table border="0" align="center" cellpadding="4" cellspacing="0">
                <tr>
                  <td class="style6">Browse :</td>
                  <td class="KT_row_filter"><input name="tfi_listproducts3_productname" type="text" class="form" id="tfi_listproducts3_productname" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productname']); ?>" size="24" maxlength="100" /></td>
                  <td class="KT_row_filter"><input name="tfi_listproducts3_programname" type="text" class="form" id="tfi_listproducts3_programname" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_programname']); ?>" size="24" maxlength="100" /></td>
                  <td class="KT_row_filter"><input name="tfi_listproducts3_productdescription" type="text" class="form" id="tfi_listproducts3_productdescription" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts3_productdescription']); ?>" size="24" maxlength="100" /></td>
                  <td class="KT_row_filter"><input name="tfi_listproducts3" type="submit" class="button" value="<?php echo NXT_getResource("Filter"); ?>" /></td>
                  </tr>
                <tr>
                  <td height="1" class="KT_row_filter"></td>
                  <td height="1" class="refineby1">Product :</td>
                  <td class="refineby1">Program :</td>
                  <td class="refineby1">Within Detail :</td>
                  <td class="KT_row_filter"></td>
                </tr>
              </table></td>
              </tr>
            <tr>
              <td height="1" bgcolor="#EBF0F3" class="style23"> Products
                <?php
  $nav_listproducts3->Prepare();
  require("includes/nav/NAV_Text_Statistics.inc.php");
?></td>
              <td bgcolor="#EBF0F3"><div align="right"><a href="<?php echo $nav_listproducts3->getShowAllLink(); ?>" class="refineby"><?php echo NXT_getResource("Show"); ?>
                      <?php 
  // Show IF Conditional region1
  if (@$_GET['show_all_nav_listproducts3'] == 1) {
?>
                      <?php echo $_SESSION['default_max_rows_nav_listproducts3']; ?>
                      <?php 
  // else Conditional region1
  } else { ?>
                      <?php echo NXT_getResource("all"); ?>
                      <?php } 
  // endif Conditional region1
?>
                      <?php echo NXT_getResource("records"); ?></a> &nbsp;
                &nbsp; </div></td>
            </tr>
            
          </table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E2E0D6">
              <tr>
                <td bgcolor="#FFFFFF"><div>
                    <div>
                      <div>
                        <div></div>
                        <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DFE6EA">
                          
                          <tbody>

<?php if ($totalRows_rsproducts1 > 0) { // Show if recordset not empty ?>
                              <?php do { ?>
                                <tr class="<?php echo @$cnt1++%2==0 ? "" : "KT_even"; ?>">
                                  <td width="1" valign="top" bgcolor="#FFFFFF" ><table width="61" border="0" cellpadding="0" cellspacing="0" bgcolor="#96ACBA"  class="topborder">
                                    <tr>
                                      <td bgcolor="#F1F0E4"><a href="/products/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>.html"><img src="<?php echo $row_rsproducts1['productimage']; ?>" alt="<?php echo KT_FormatForList($row_rsproducts1['productname'], 100); ?>" width="67" height="67" border="0" /></a></td>
                                    </tr>
                                  </table>
                                    <input type="hidden" name="kt_pk_products" class="id_checkbox" value="<?php echo $row_rsproducts1['productid']; ?>" />
                                      <input type="hidden" name="productid" class="id_field" value="<?php echo $row_rsproducts1['productid']; ?>" /></td>
                                  <td width="380" bgcolor="#FFFFFF"><span class="style9"><a href="/products/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>.html" target="_blank" class="cateheading"><?php echo KT_FormatForList($row_rsproducts1['productname'], 100); ?></a></span><br />
                                    <span class="descriptionlight"><?php echo KT_FormatForList($row_rsproducts1['productdescription'], 150); ?> <?php echo KT_FormatForList($row_rsproducts1['productinfo'], 500); ?></span> 
                                    <div class="descriptionlight"></div>
                                    <div></div>
                                    <div></div></td>
                                  <td width="48" bgcolor="#FFFFFF"><div align="center"><img src="<?php echo $row_rsproducts1['programlogo']; ?>" alt="<?php echo KT_FormatForList($row_rsproducts1['programname'], 100); ?>" border="0" /></div>
                                    <div class="style23"></div></td>
                                  <td width="100" bgcolor="#FFFFFF"><table width="84" height="23" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td><div align="center" class="style2"><span class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['productprice'], 100); ?></span> <span class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['currency'], 100); ?></span></div> 
                                      <div class="style2"></div></td>
                                    </tr>
                                  </table>
                                    <table width="84" height="31" border="0" align="center" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td valign="top"><div align="center" class="style9"><a href="/products/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>.html"><img src="/images/moreinfo.gif" alt="<?php echo KT_FormatForList($row_rsproducts1['productname'], 100); ?>" width="73" height="26" border="0" /></a></div></td>
                                      </tr>
                                    </table>
                                    <div align="center" class="stylecates3"><a href="/process/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/" target="_blank" class="stylecates3">Purchase Now</a></div></td>
                                </tr>
                                <?php } while ($row_rsproducts1 = mysql_fetch_assoc($rsproducts1)); ?>
                              <?php } // Show if recordset not empty ?>
                          </tbody>
                        </table>
                        
                        <?php if ($totalRows_rsproducts1 == 0) { // Show if recordset empty ?>
                          <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DFE6EA">
                              <tr>
                                  <td height="22" bgcolor="#FFFFFF"><div align="center" class="style28">No Product Listed Under This Category - Please Check Back Soon</div></td>
                              </tr>
                                  </table>
                          <?php } // Show if recordset empty ?>
<div align="center">
  <table width="100%" height="24" border="0" cellpadding="0" cellspacing="1" bgcolor="#DFE6EA">
  <tr>
    <td valign="top"><div align="center">
      <?php
            $nav_listproducts3->Prepare();
            require("includes/nav/NAV_Text_Navigation.inc.php");
          ?>
    </div></td>
  </tr>
</table>
                              </div>
                      </div>
                    </div>
                    </div>                  </td>
              </tr>
            </table></td>
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
