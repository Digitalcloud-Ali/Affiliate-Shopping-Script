<?php require_once('../includes/shops.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Load the required classes
require_once('../includes/tfi/TFI.php');
require_once('../includes/tso/TSO.php');
require_once('../includes/nav/NAV.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_shops = new KT_connection($shops, $database_shops);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_shops, "../");
//Grand Levels: Any
$restrict->Execute();
//End Restrict Access To Page

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
$tfi_listproducts5 = new TFI_TableFilter($conn_shops, "tfi_listproducts5");
$tfi_listproducts5->addColumn("products.productname", "STRING_TYPE", "productname", "%");
$tfi_listproducts5->addColumn("products.programname", "STRING_TYPE", "programname", "%");
$tfi_listproducts5->addColumn("products.currency", "STRING_TYPE", "currency", "%");
$tfi_listproducts5->addColumn("products.productdescription", "STRING_TYPE", "productdescription", "%");
$tfi_listproducts5->addColumn("products.productprice", "STRING_TYPE", "productprice", "%");
$tfi_listproducts5->Execute();

// Sorter
$tso_listproducts5 = new TSO_TableSorter("rsproducts1", "tso_listproducts5");
$tso_listproducts5->addColumn("products.productname");
$tso_listproducts5->addColumn("products.programname");
$tso_listproducts5->addColumn("products.currency");
$tso_listproducts5->addColumn("products.productdescription");
$tso_listproducts5->addColumn("products.productprice");
$tso_listproducts5->setDefault("products.productname");
$tso_listproducts5->Execute();

// Navigation
$nav_listproducts5 = new NAV_Regular("nav_listproducts5", "rsproducts1", "../", $_SERVER['PHP_SELF'], 10);

//NeXTenesio3 Special List Recordset
$maxRows_rsproducts1 = $_SESSION['max_rows_nav_listproducts5'];
$pageNum_rsproducts1 = 0;
if (isset($_GET['pageNum_rsproducts1'])) {
  $pageNum_rsproducts1 = $_GET['pageNum_rsproducts1'];
}
$startRow_rsproducts1 = $pageNum_rsproducts1 * $maxRows_rsproducts1;

// Defining List Recordset variable
$NXTFilter_rsproducts1 = "1=1";
if (isset($_SESSION['filter_tfi_listproducts5'])) {
  $NXTFilter_rsproducts1 = $_SESSION['filter_tfi_listproducts5'];
}
// Defining List Recordset variable
$NXTSort_rsproducts1 = "products.productname";
if (isset($_SESSION['sorter_tso_listproducts5'])) {
  $NXTSort_rsproducts1 = $_SESSION['sorter_tso_listproducts5'];
}
mysql_select_db($database_shops, $shops);

$query_rsproducts1 = "SELECT products.productname, products.programname, products.currency, products.productdescription, products.productprice, products.productid FROM products WHERE {$NXTFilter_rsproducts1} ORDER BY {$NXTSort_rsproducts1}";
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

// Make a logout transaction instance
$logoutTransaction = new tNG_logoutTransaction($conn_shops);
$tNGs->addTransaction($logoutTransaction);
// Register triggers
$logoutTransaction->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "KT_logout_now");
$logoutTransaction->registerTrigger("END", "Trigger_Default_Redirect", 99, "login.php");
// Add columns
// End of logout transaction instance

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);

$nav_listproducts5->checkBoundries();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Manage Products</title>
<link href="../includes/style.css" rel="stylesheet" type="text/css" />
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script src="../includes/skins/style.js" type="text/javascript"></script>
<script src="../includes/nxt/scripts/list.js" type="text/javascript"></script>
<script src="../includes/nxt/scripts/list.js.php" type="text/javascript"></script>
<script type="text/javascript">
$NXT_LIST_SETTINGS = {
  duplicate_buttons: false,
  duplicate_navigation: false,
  row_effects: false,
  show_as_buttons: false,
  record_counter: false
}
</script>
<style type="text/css">
  /* Dynamic List row settings */
  .KT_col_productname {width:140px; overflow:hidden;}
  .KT_col_programname {width:140px; overflow:hidden;}
  .KT_col_currency {width:140px; overflow:hidden;}
  .KT_col_productdescription {width:140px; overflow:hidden;}
  .KT_col_productprice {width:140px; overflow:hidden;}
</style>
</head>

<body>
<table width="100%" height="63" border="0" cellpadding="0" cellspacing="0" class="headback">
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
        <td><a href="main.php"><img src="../images/logo.gif" width="267" height="62" border="0" /></a></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#D5E1E6">
  <tr>
    <td height="32" bgcolor="#E7EEF1"><table border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td>&nbsp;</td>
        <td class="style6">Manage : </td>
        <td><a href="cates.php" class="refineby">Categories</a>&nbsp; <span class="style23">/</span>&nbsp; <a href="subcates.php" class="refineby">Sub Categories&nbsp;</a> <span class="style23">/</span>&nbsp; <a href="products.php" class="refineby">Products</a>&nbsp; <span class="style23">/</span>&nbsp; <a href="shops.php" class="refineby">Shops&nbsp;</a> <span class="style23">/&nbsp;</span> <a href="pages.php" class="refineby">Pages</a></td>
      </tr>
    </table></td>
    <td width="9%" bgcolor="#E7EEF1"><div align="center" class="pagination"><a href="help.php" class="refineby">Help</a></div></td>
    <td width="9%" bgcolor="#E7EEF1"><div align="center">
      <?php
	echo $tNGs->getErrorMsg();
?>
      <a href="<?php echo $logoutTransaction->getLogoutLink(); ?>" class="stylecatestwo">Logout</a></div></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="83">
      <div align="left">
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
            <td width="1">&nbsp;</td>
            <td width="700"><p align="justify" class="style6"> Products
                <?php
  $nav_listproducts5->Prepare();
  require("../includes/nav/NAV_Text_Statistics.inc.php");
?>
</p></td>
          </tr>
          <tr>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td bgcolor="#F9FAFA"><div class="KT_tng" id="listproducts5">
              <div class="KT_tnglist">
                  <form action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" method="post" id="form1">
                    <div class="KT_options"> <a href="<?php echo $nav_listproducts5->getShowAllLink(); ?>"><?php echo NXT_getResource("Show"); ?>
                      <?php 
  // Show IF Conditional region1
  if (@$_GET['show_all_nav_listproducts5'] == 1) {
?>
                        <?php echo $_SESSION['default_max_rows_nav_listproducts5']; ?>
                        <?php 
  // else Conditional region1
  } else { ?>
                        <?php echo NXT_getResource("all"); ?>
                        <?php } 
  // endif Conditional region1
?>
                          <?php echo NXT_getResource("records"); ?></a> &nbsp;
                      &nbsp;
                <?php 
  // Show IF Conditional region2
  if (@$_SESSION['has_filter_tfi_listproducts5'] == 1) {
?>
                  <a href="<?php echo $tfi_listproducts5->getResetFilterLink(); ?>" class="stylecates3"><?php echo NXT_getResource("Reset filter"); ?></a>
                  <?php 
  // else Conditional region2
  } else { ?>
                  <a href="<?php echo $tfi_listproducts5->getShowFilterLink(); ?>" class="stylecates3"><?php echo NXT_getResource("Show filter"); ?></a>
                  <?php } 
  // endif Conditional region2
?>
                    </div>
                    <table border="0" cellpadding="2" cellspacing="1" class="refineby2">
                      <thead>
                        <tr class="KT_row_order">
                          <th bgcolor="#E7EEF1"> <input type="checkbox" name="KT_selAll" id="KT_selAll"/>                          </th>
                          <th bgcolor="#E7EEF1" class="KT_sorter KT_col_productname <?php echo $tso_listproducts5->getSortIcon('products.productname'); ?>" id="productname"> <a href="<?php echo $tso_listproducts5->getSortLink('products.productname'); ?>" class="cateheading">Productname</a> </th>
                          <th bgcolor="#E7EEF1" class="KT_sorter KT_col_programname <?php echo $tso_listproducts5->getSortIcon('products.programname'); ?>" id="programname"> <a href="<?php echo $tso_listproducts5->getSortLink('products.programname'); ?>" class="cateheading">Programname</a> </th>
                          <th bgcolor="#E7EEF1" class="KT_sorter KT_col_currency <?php echo $tso_listproducts5->getSortIcon('products.currency'); ?>" id="currency"> <a href="<?php echo $tso_listproducts5->getSortLink('products.currency'); ?>" class="cateheading">Currency</a> </th>
                          <th bgcolor="#E7EEF1" class="KT_sorter KT_col_productdescription <?php echo $tso_listproducts5->getSortIcon('products.productdescription'); ?>" id="productdescription"> <a href="<?php echo $tso_listproducts5->getSortLink('products.productdescription'); ?>" class="cateheading">Productdescription</a> </th>
                          <th bgcolor="#E7EEF1" class="KT_sorter KT_col_productprice <?php echo $tso_listproducts5->getSortIcon('products.productprice'); ?>" id="productprice"> <a href="<?php echo $tso_listproducts5->getSortLink('products.productprice'); ?>" class="cateheading">Productprice</a> </th>
                          <th bgcolor="#E7EEF1">&nbsp;</th>
                        </tr>
                        <?php 
  // Show IF Conditional region3
  if (@$_SESSION['has_filter_tfi_listproducts5'] == 1) {
?>
                          <tr class="KT_row_filter">
                            <td>&nbsp;</td>
                            <td><input name="tfi_listproducts5_productname" type="text" class="form" id="tfi_listproducts5_productname" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts5_productname']); ?>" size="20" maxlength="100" /></td>
                            <td><input name="tfi_listproducts5_programname" type="text" class="form" id="tfi_listproducts5_programname" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts5_programname']); ?>" size="20" maxlength="100" /></td>
                            <td><input name="tfi_listproducts5_currency" type="text" class="form" id="tfi_listproducts5_currency" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts5_currency']); ?>" size="20" maxlength="100" /></td>
                            <td><input name="tfi_listproducts5_productdescription" type="text" class="form" id="tfi_listproducts5_productdescription" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts5_productdescription']); ?>" size="20" maxlength="100" /></td>
                            <td><input name="tfi_listproducts5_productprice" type="text" class="form" id="tfi_listproducts5_productprice" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listproducts5_productprice']); ?>" size="20" maxlength="100" /></td>
                            <td><div align="center">
                              <input name="tfi_listproducts5" type="submit" class="button" value="<?php echo NXT_getResource("Filter"); ?>" />
                            </div></td>
                          </tr>
                          <?php } 
  // endif Conditional region3
?>
                      </thead>
                      <tbody>
                        <?php if ($totalRows_rsproducts1 == 0) { // Show if recordset empty ?>
                          <tr>
                            <td colspan="7" class="refineby2"><?php echo NXT_getResource("The table is empty or the filter you've selected is too restrictive."); ?></td>
                          </tr>
                          <?php } // Show if recordset empty ?>
                        <?php if ($totalRows_rsproducts1 > 0) { // Show if recordset not empty ?>
                          <?php do { ?>
                            <tr class="<?php echo @$cnt1++%2==0 ? "" : "KT_even"; ?>">
                              <td><input type="checkbox" name="kt_pk_products" class="id_checkbox" value="<?php echo $row_rsproducts1['productid']; ?>" />
                                  <input type="hidden" name="productid" class="id_field" value="<?php echo $row_rsproducts1['productid']; ?>" />                              </td>
                              <td><div class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['productname'], 20); ?></div></td>
                              <td><div class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['programname'], 20); ?></div></td>
                              <td><div class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['currency'], 20); ?></div></td>
                              <td><div class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['productdescription'], 20); ?></div></td>
                              <td><div class="refineby2"><?php echo KT_FormatForList($row_rsproducts1['productprice'], 20); ?></div></td>
                              <td><a class="KT_edit_link stylecates" href="products_detail.php?productid=<?php echo $row_rsproducts1['productid']; ?>&amp;KT_back=1"><?php echo NXT_getResource("edit_one"); ?></a> <a class="KT_delete_link stylecates" href="#delete"><?php echo NXT_getResource("delete_one"); ?></a> </td>
                            </tr>
                            <?php } while ($row_rsproducts1 = mysql_fetch_assoc($rsproducts1)); ?>
                          <?php } // Show if recordset not empty ?>
                      </tbody>
                    </table>
                    <div class="KT_bottomnav">
                      <div>
                        <?php
            $nav_listproducts5->Prepare();
            require("../includes/nav/NAV_Text_Navigation.inc.php");
          ?>
                      </div>
                    </div>
                    <div class="KT_bottombuttons">
                      <div class="KT_operations"> <a class="stylecates" href="#" onclick="nxt_list_edit_link_form(this); return false;"><?php echo NXT_getResource("edit_all"); ?></a> <a class="stylecates" href="#" onclick="nxt_list_delete_link_form(this); return false;"><?php echo NXT_getResource("delete_all"); ?></a> </div>
<span>&nbsp;</span>
                      <select name="no_new" id="no_new">
                        <option value="1">1</option>
                        <option value="3">3</option>
                        <option value="6">6</option>
                      </select>
                      <a class="KT_additem_op_link stylecates" href="products_detail.php?KT_back=1" onclick="return nxt_list_additem(this)"><?php echo NXT_getResource("add new"); ?></a> </div>
                  </form>
                </div>
                <br class="clearfixplain" />
              </div>
            </td>
          </tr>
          <tr>
            <td height="11" bgcolor="#F9FAFA"></td>
            <td bgcolor="#F9FAFA"></td>
          </tr>
              </table>
    </div></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#E7EEF1">
  <tr>
    <td height="83" bgcolor="#F4F7F7"><div align="center" class="refineby2">Copyright © Fleadiscount, Inc. All rights reserved.<br />
    Website Designed &amp; Scripted By Rayice.Com <br />
    This is restrict admin area. Please if you are not the admin the leave this place soon. We record the IP/Adress for security reasons.</div></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsproducts1);
?>
