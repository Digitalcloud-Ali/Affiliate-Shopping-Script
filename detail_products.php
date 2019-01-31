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
$tfi_listproducts16 = new TFI_TableFilter($conn_shops, "tfi_listproducts16");
$tfi_listproducts16->addColumn("products.productname", "STRING_TYPE", "productname", "%");
$tfi_listproducts16->addColumn("products.productid", "NUMERIC_TYPE", "productid", "=");
$tfi_listproducts16->addColumn("products.programlogo", "STRING_TYPE", "programlogo", "%");
$tfi_listproducts16->addColumn("products.programname", "STRING_TYPE", "programname", "%");
$tfi_listproducts16->addColumn("products.currency", "STRING_TYPE", "currency", "%");
$tfi_listproducts16->addColumn("products.productinfo", "STRING_TYPE", "productinfo", "%");
$tfi_listproducts16->addColumn("products.productdescription", "STRING_TYPE", "productdescription", "%");
$tfi_listproducts16->addColumn("products.productlink", "STRING_TYPE", "productlink", "%");
$tfi_listproducts16->addColumn("products.productimage", "STRING_TYPE", "productimage", "%");
$tfi_listproducts16->addColumn("products.productbrand", "STRING_TYPE", "productbrand", "%");
$tfi_listproducts16->addColumn("products.productprice", "STRING_TYPE", "productprice", "%");
$tfi_listproducts16->Execute();

// Sorter
$tso_listproducts16 = new TSO_TableSorter("rsproducts1", "tso_listproducts16");
$tso_listproducts16->addColumn("products.productname");
$tso_listproducts16->addColumn("products.productid");
$tso_listproducts16->addColumn("products.programlogo");
$tso_listproducts16->addColumn("products.programname");
$tso_listproducts16->addColumn("products.currency");
$tso_listproducts16->addColumn("products.productinfo");
$tso_listproducts16->addColumn("products.productdescription");
$tso_listproducts16->addColumn("products.productlink");
$tso_listproducts16->addColumn("products.productimage");
$tso_listproducts16->addColumn("products.productbrand");
$tso_listproducts16->addColumn("products.productprice");
$tso_listproducts16->setDefault("products.productname");
$tso_listproducts16->Execute();

// Navigation
$nav_listproducts16 = new NAV_Regular("nav_listproducts16", "rsproducts1", "", $_SERVER['PHP_SELF'], 10);

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

$colname_subcates = "-1";
if (isset($_GET['section'])) {
  $colname_subcates = $_GET['section'];
}
mysql_select_db($database_shops, $shops);
$query_subcates = sprintf("SELECT * FROM subcates WHERE `section` = %s ORDER BY catename ASC", GetSQLValueString($colname_subcates, "text"));
$subcates = mysql_query($query_subcates, $shops) or die(mysql_error());
$row_subcates = mysql_fetch_assoc($subcates);
$totalRows_subcates = mysql_num_rows($subcates);

$colname_visitedsection = "-1";
if (isset($_GET['section'])) {
  $colname_visitedsection = $_GET['section'];
}
mysql_select_db($database_shops, $shops);
$query_visitedsection = sprintf("SELECT * FROM cates WHERE `section` = %s", GetSQLValueString($colname_visitedsection, "text"));
$visitedsection = mysql_query($query_visitedsection, $shops) or die(mysql_error());
$row_visitedsection = mysql_fetch_assoc($visitedsection);
$totalRows_visitedsection = mysql_num_rows($visitedsection);

// Defining List Recordset variable
$NXTFilter_rsproducts1 = "1=1";
if (isset($_SESSION['filter_tfi_listproducts16'])) {
  $NXTFilter_rsproducts1 = $_SESSION['filter_tfi_listproducts16'];
}
// Defining List Recordset variable
$maxRows_rsproducts1 = $_SESSION['max_rows_nav_listproducts16'];
$pageNum_rsproducts1 = 0;
if (isset($_GET['pageNum_rsproducts1'])) {
  $pageNum_rsproducts1 = $_GET['pageNum_rsproducts1'];
}
$startRow_rsproducts1 = $pageNum_rsproducts1 * $maxRows_rsproducts1;

$NXTSort_rsproducts1 = "products.productname";
if (isset($_SESSION['sorter_tso_listproducts16'])) {
  $NXTSort_rsproducts1 = $_SESSION['sorter_tso_listproducts16'];
}
$colname_rsproducts1 = "-1";
if (isset($_GET['section'])) {
  $colname_rsproducts1 = $_GET['section'];
}
mysql_select_db($database_shops, $shops);
$query_rsproducts1 = sprintf("SELECT * FROM products WHERE `section` = %s ORDER BY {$NXTSort_rsproducts1}", GetSQLValueString($colname_rsproducts1, "text"));
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

mysql_select_db($database_shops, $shops);
$query_totalrecords = "SELECT * FROM products";
$totalrecords = mysql_query($query_totalrecords, $shops) or die(mysql_error());
$row_totalrecords = mysql_fetch_assoc($totalrecords);
$totalRows_totalrecords = mysql_num_rows($totalrecords);
//End NeXTenesio3 Special List Recordset
$nav_listproducts16->checkBoundries();
?>
<?php include("includes/config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $domainname ?> - <?php echo $row_visitedsection['section']; ?></title>
<meta name="description" content="<?php echo $domainname ?> - Explore All Discount Products Under <?php echo $row_visitedsection['section']; ?>" />
<meta name="keywords" content="<?php echo $string = str_replace(' ', ',', $row_visitedsection['section']); ?> , <?php echo $domainname ?>" />
<link href="/includes/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/includes/preload.js"></script>
</head>

<body onload="MM_preloadImages('/images/downproduct.gif','/images/downshops.gif')"><table width="1003" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php include("includes/header.php"); ?>
<table width="1003" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="22">&nbsp;</td>
    <td width="957"><table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#DFE6EA">
      <tr>
        <td width="64%" height="1" bgcolor="#EBF0F3"><a href="/" class="refineby">Home</a> <span class="arrows"> ››</span><span class="refineby"> <?php echo $row_visitedsection['section']; ?> </span></td>
        </tr>
      
    </table>
      
      
<?php if ($totalRows_rsproducts1 > 0) { // Show if recordset not empty ?>
    <?php if ($totalRows_subcates > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#DFE6EA">
        <tr>
          <td width="64%" height="1" bgcolor="#EBF0F3"><span class="descriptionlight">Sub Categories :</span> <span class="style23">/
            
            </span>
            <?php do { ?>
              <span class="stylecates"><a href="/explore/<?php echo $string = str_replace(' ', '+', $row_subcates['catename']); ?>/<?php echo $row_subcates['subid']; ?>/<?php echo $string = str_replace(' ', '+', $row_subcates['section']); ?>/" class="stylecates"><?php echo $row_subcates['catename']; ?></a></span><span class="style23"> /</span>
              <?php } while ($row_subcates = mysql_fetch_assoc($subcates)); ?></td>
        </tr>
      </table>
      <?php } // Show if recordset not empty ?>
    <?php } // Show if recordset not empty ?>
</td>
    <td width="24">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="22" height="416" valign="top">&nbsp;</td>
    <td width="300" valign="top"><?php include('includes/cates.php'); ?>
      <?php include('includes/rightmenu.php'); ?>    </td>
    <td width="10" valign="top"></td>
    <td width="650" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#DFE6EA">
            <tr>
              <td height="1" bgcolor="#EBF0F3"><table width="100%" border="0" cellpadding="1" cellspacing="3">
                  <tr>
                    <td width="27%"><span class="style6">Sort By :</span></td>
                    <td width="14%"><div align="center"><span class="refineby2">Name</span></div></td>
                    <td width="15%"><div align="center"><span class="refineby2">Program</span></div></td>
                    <td width="15%"><div align="center"><span class="refineby2">Currency</span></div></td>
                    <td width="15%"><div align="center"><span class="refineby2">Brand</span></div></td>
                    <td width="14%"><div align="center"><span class="refineby2">Price</span></div></td>
                  </tr>
                  <tr>
                    <td><span class="searchbottomlinks">ASCENDING ›</span> </td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.productname"><img src="/images/asc.gif" alt="ASC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.programname"><img src="/images/asc.gif" alt="ASC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.currency"><img src="/images/asc.gif" alt="ASC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.productbrand"><img src="/images/asc.gif" alt="ASC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.productprice"><img src="/images/asc.gif" alt="ASC" width="15" height="16" border="0" /></a></div></td>
                  </tr>
                  <tr>
                    <td><span class="searchbottomlinks">DESCENDING›</span></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.productname DESC"><img src="/images/desc.gif" alt="DESC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.programname DESC"><img src="/images/desc.gif" alt="DESC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.currency DESC"><img src="/images/desc.gif" alt="DESC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.productbrand DESC"><img src="/images/desc.gif" alt="DESC" width="15" height="16" border="0" /></a></div></td>
                    <td><div align="center"><a href="/detail_products.php?cateid=<?php echo $row_visitedsection['cateid']; ?>&amp;section=<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>&amp;sorter_tso_listproducts16=products.productprice DESC"><img src="/images/desc.gif" alt="DESC" width="15" height="16" border="0" /></a></div></td>
                  </tr>
                </table>                </td>
              <td height="1" valign="top" bgcolor="#EBF0F3"><table width="100%" border="0" cellspacing="3" cellpadding="1">
                  <tr>
                    <td><div align="right"><span class="refineby2">Overall Products : <span class="style28"><?php echo $totalRows_totalrecords ?></span></span><span class="style9"><span class="style28"></span> </span> </div>                      </td>
                  </tr>
                  <tr>
                    <td><div align="right"><span class="refineby2">Products Here  : </span><span class="style9"><span class="style28"><?php echo $totalRows_rsproducts1 ?></span> </span> </div></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td width="64%" height="1" bgcolor="#EBF0F3" class="style23"> Products
                <?php
  $nav_listproducts16->Prepare();
  require("includes/nav/NAV_Text_Statistics.inc.php");
?></td>
              <td width="30%" bgcolor="#EBF0F3"><div align="right">
<?php
if($_GET['show_all_nav_listproducts16'] == 1)
{
?>
<a href="/explore/<?php echo $row_visitedsection['cateid']; ?>/<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>/" class="refineby">Show 10 Records</a>
<?php
}
else
{
?>
<a href="/expall/<?php echo $row_visitedsection['cateid']; ?>/<?php echo $string = str_replace(' ', '+', $row_visitedsection['section']); ?>/1/" class="refineby">Show All Records</a>
<?php
}
?>
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
                                      <td bgcolor="#F1F0E4"><a href="/process/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/" target="_blank"><img src="<?php echo $row_rsproducts1['productimage']; ?>" alt="<?php echo KT_FormatForList($row_rsproducts1['productname'], 100); ?>" width="67" height="67" border="0" /></a></td>
                                    </tr>
                                  </table>
                                    <input type="hidden" name="kt_pk_products" class="id_checkbox" value="<?php echo $row_rsproducts1['productid']; ?>" />
                                      <input type="hidden" name="productid" class="id_field" value="<?php echo $row_rsproducts1['productid']; ?>" /></td>
                                  <td width="380" bgcolor="#FFFFFF"><span class="style9"><a href="/process/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/" target="_blank" class="cateheading"><?php echo KT_FormatForList($row_rsproducts1['productname'], 100); ?></a></span><br />
                                    <span class="descriptionlight"><?php echo KT_FormatForList($row_rsproducts1['productdescription'], 150); ?> <?php echo KT_FormatForList($row_rsproducts1['productinfo'], 500); ?></span> 
                                    <div class="descriptionlight"></div>
                                    <div></div>
                                    <div></div></td>
                                  <td width="48" bgcolor="#FFFFFF"><div align="center"><img src="<?php echo $row_rsproducts1['programlogo']; ?>" alt="<?php echo KT_FormatForList($row_rsproducts1['programname'], 100); ?>" /></div></td>
                                  <td width="100" bgcolor="#FFFFFF"><table width="84" height="23" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td><div align="center" class="style2"><span class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['productprice'], 100); ?></span> <span class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['currency'], 100); ?></span></div> 
                                      <div class="style2"></div></td>
                                    </tr>
                                  </table>
                                    <table width="1" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td width="1" valign="top"><a href="/process/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/" target="_blank"><img src="/images/visitnow.gif" alt="<?php echo KT_FormatForList($row_rsproducts1['productname'], 100); ?>" width="73" height="26" border="0" /></a></td>
                                      </tr>
                                    </table>
                                    <div align="center" class="stylecates"><a href="/products/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>.html" class="stylecates3">MORE INFO</a></div></td>
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
                              <?php
            $nav_listproducts16->Prepare();
            require("includes/nav/NAV_Text_Navigation.inc.php");
          ?>
                              </div>
                        <div class="KT_bottombuttons">
                          <div class="KT_operations"></div></div>
                      </div>
                    </div>
                  </div>                  </td>
              </tr>
            </table></td>
          </tr>
      </table></td>
    <td width="21">&nbsp;</td>
  </tr>
</table>
<?php include("includes/footer.php"); ?></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($sections);

mysql_free_result($stores);

mysql_free_result($newstores);

mysql_free_result($popularproducts);

mysql_free_result($subcates);

mysql_free_result($visitedsection);

mysql_free_result($rsproducts1);

mysql_free_result($totalrecords);
?>
