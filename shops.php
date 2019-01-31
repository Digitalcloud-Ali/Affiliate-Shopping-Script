<?php require_once('includes/shops.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

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
if (isset($_GET['productname'])) {
  $colname_visitedsection = $_GET['productname'];
}
mysql_select_db($database_shops, $shops);
$query_visitedsection = sprintf("SELECT * FROM products WHERE productname = %s", GetSQLValueString($colname_visitedsection, "text"));
$visitedsection = mysql_query($query_visitedsection, $shops) or die(mysql_error());
$row_visitedsection = mysql_fetch_assoc($visitedsection);
$totalRows_visitedsection = mysql_num_rows($visitedsection);

$colname_rsproducts1 = "-1";
if (isset($_GET['shopid'])) {
  $colname_rsproducts1 = $_GET['shopid'];
}
mysql_select_db($database_shops, $shops);
$query_rsproducts1 = sprintf("SELECT * FROM shops WHERE shopid = %s", GetSQLValueString($colname_rsproducts1, "int"));
$rsproducts1 = mysql_query($query_rsproducts1, $shops) or die(mysql_error());
$row_rsproducts1 = mysql_fetch_assoc($rsproducts1);
$totalRows_rsproducts1 = mysql_num_rows($rsproducts1);

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
<title><?php echo $domainname ?> - <?php echo $row_rsproducts1['shopname']; ?></title>
<meta name="description" content="<?php echo $row_rsproducts1['shopname']; ?> - <?php echo KT_FormatForList($row_rsproducts1['shopinfo'], 200); ?>" />
<meta name="keywords" content="<?php echo $row_rsproducts1['shopname']; ?> , shopping , store , discount , products" />
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
        <td width="64%" height="1" bgcolor="#EBF0F3"><a href="/" class="refineby">Home</a> <span class="arrows"> ››</span><span class="refineby"> <?php echo $row_rsproducts1['shopname']; ?></span></td>
        </tr>
      
    </table>
      
      
          
</td>
    <td width="24">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="21" height="65" valign="top">&nbsp;</td>
    <td width="300" valign="top"><?php include('includes/cates.php'); ?>
      <?php include('includes/rightmenu.php'); ?>    </td>
    <td width="9" valign="top"></td>
    <td width="650" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#DFE6EA" class="refineby2">
            <tr>
              <td height="1" bgcolor="#EBF0F3" class="style23"><span class="descriptionlight">Shop :</span><span class="style28"> <a href="shop_check.php?shopid=<?php echo $row_rsproducts1['shopid']; ?>&amp;shopname=<?php echo $string = str_replace(' ', '+', $row_rsproducts1['shopname']); ?>" target="_blank" class="stylecates3"><?php echo $row_rsproducts1['shopname']; ?></a></span></td>
              <td bgcolor="#EBF0F3"><div align="right"><span>
                <script type="text/javascript"><!--
if ((navigator.appName == "Microsoft Internet Explorer") && (parseInt(navigator.appVersion) >= 4)) {
var url="<?php echo $linking ?>/stores/<?php echo $row_rsproducts1['shopid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['shopname']); ?>.html";
var title="<?php echo $row_rsproducts1['shopname']; ?> - <?php echo $domainname ?>";
document.write('<A HREF="javascript:window.external.AddFavorite(url,title)');
document.write('"class="refineby">Favorites It</a>');
}
else {
var alt = "<span>Click Here</span><br>";
if(navigator.appName == "Netscape") alt += "Press (Ctrl-D) on your keyboard.";
document.write(alt);
}
// End of favorites code --></script>
              </span></div></td>
            </tr>
            
          </table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E2E0D6">
              <tr>
                <td bgcolor="#FFFFFF"><div>
                    <div>
                      <div>
                        <div>
                          <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#FDE7C8">
                            <tr>
                              <td width="53" bgcolor="#FDE7C8">&nbsp;</td>
                              <td width="491" bgcolor="#FDE7C8"><div align="center"><span class="style6"><a href="/forwading/store/<?php echo $row_rsproducts1['shopid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['shopname']); ?>/" target="_blank" class="pagination"><?php echo $row_rsproducts1['shopname']; ?></a></span></div></td>
                              <td width="73" bgcolor="#FFFFFF"><a href="/forwading/store/<?php echo $row_rsproducts1['shopid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['shopname']); ?>/" target="_blank"><img src="/images/visitnow.gif" alt="<?php echo $row_rsproducts1['shopname']; ?>" width="73" height="26" border="0" /></a></td>
                            </tr>
                          </table>
                        </div>
                        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#DFE6EA">
                          
                          <tbody>
                            <tr>
                              <td valign="top" bgcolor="#FFFFFF" ><br />
                                <table width="1" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#96ACBA"  class="topborder">
                                  <tr>
                                    <td bgcolor="#F1F0E4"><a href="/forwading/store/<?php echo $row_rsproducts1['shopid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['shopname']); ?>/"><img src="<?php echo $row_rsproducts1['shopimage']; ?>" alt="<?php echo $row_rsproducts1['shopname']; ?>" border="0" /></a></td>
                                  </tr>
                                </table>
                                  <div align="center">
                                    <input name="shopid" type="hidden" id="shopid" value="<?php echo $row_rsproducts1['shopid']; ?>" />
                                    <br />
                                  </div>
                                  <div align="center"></div></td>
                              </tr>
</tbody>
                        </table>
                        
                        
                          <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#E9F0F3">
                            <tr>
                              <td height="22" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="5">
                                <tr>
                                  <td bgcolor="#F3F7F8" class="descriptionlight"><strong>Shop Info :</strong> <span class="descriptionlight"><?php echo $row_rsproducts1['shopinfo']; ?></span></td>
                                </tr>
                              </table></td>
                            </tr>
                          </table>
                          <?php if ($totalRows_rsproducts1 == 0) { // Show if recordset empty ?>
                            <table width="100%" border="0" cellpadding="6" cellspacing="1" bgcolor="#DFE6EA">
                                <tr>
                                    <td bgcolor="#EBF0F3"><div align="center"><span class="refineby">Nothing's Found - Please Use Our Search To Find What You Are Looking For</span><span class="searchbottomlinks"></span></div></td>
                                </tr>
                                </table>
                            <?php } // Show if recordset empty ?>
<div align="center"></div>
                        <div class="KT_bottombuttons">
                          <div class="KT_operations"></div></div>
                      </div>
                    </div>
                  </div>                  </td>
              </tr>
            </table></td>
          <td width="9">&nbsp;</td>
        </tr>
      </table></td>
    <td width="23">&nbsp;</td>
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
?>
