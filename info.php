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
if (isset($_GET['productid'])) {
  $colname_rsproducts1 = $_GET['productid'];
}
mysql_select_db($database_shops, $shops);
$query_rsproducts1 = sprintf("SELECT * FROM products WHERE productid = %s", GetSQLValueString($colname_rsproducts1, "int"));
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
<title><?php echo $domainname ?> - <?php echo $row_visitedsection['productname']; ?> | <?php echo $row_visitedsection['catename']; ?> | <?php echo $row_visitedsection['section']; ?></title>
<meta name="description" content="<?php echo $row_visitedsection['catename']; ?> - Purchase <?php echo $row_visitedsection['productname']; ?> Price : <?php echo $row_rsproducts1['productprice']; ?> Currency : <?php echo $row_rsproducts1['currency']; ?>" />
<meta name="keywords" content="<?php echo $row_rsproducts1['productname']; ?> , <?php echo $string = str_replace(' ', ',', $row_rsproducts1['productname']); ?>, <?php echo $row_rsproducts1['programname']; ?> , <?php echo $string = str_replace(' ', ',', $row_visitedsection['section']); ?> , <?php echo $string = str_replace(' ', ',', $row_visitedsection['catename']); ?> , <?php echo $string = str_replace(' ', ',', $row_rsproducts1['Genres']); ?> , <?php echo $row_rsproducts1['currency']; ?> , <?php echo $row_rsproducts1['productprice']; ?>" />
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
        <td width="64%" height="1" bgcolor="#EBF0F3"><a href="/" class="refineby">Home</a> <span class="arrows"> ›› </span><span class="refineby"><?php echo $row_visitedsection['section']; ?>  </span><span class="arrows"> ›› </span><span class="refineby"><?php echo $row_visitedsection['catename']; ?> </span><span class="arrows"> ››</span> <span class="refineby"><?php echo $row_visitedsection['productname']; ?></span></td>
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
              <td height="1" bgcolor="#EBF0F3" class="style23"><span class="descriptionlight">Currency :</span><span class="style2"><?php echo $row_rsproducts1['currency']; ?></span>/<span class="descriptionlight"> Price :</span><span class="style2"><?php echo $row_rsproducts1['productprice']; ?></span></td>
              <td bgcolor="#EBF0F3"><div align="right"><span>
                <script type="text/javascript"><!--
if ((navigator.appName == "Microsoft Internet Explorer") && (parseInt(navigator.appVersion) >= 4)) {
var url="<?php echo $linking ?>/products/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>.html";
var title="<?php echo $row_rsproducts1['productname']; ?> - <?php echo $domainname ?>";
document.write('<A HREF="javascript:window.external.AddFavorite(url,title)');
document.write('"class="refineby">Favorites It</a>');
}
else {
var alt = "<span>Click Here</span><br>";
if(navigator.appName == "Netscape") alt += "Press (Ctrl-D) on your keyboard.";
document.write(alt);
}
// End of favorites code --></script>
                </span> - <a href="javascript:void(0)"
onclick="window.open('/broken.php?productid=<?php echo $row_rsproducts1['productid']; ?>&section=<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>&catename=<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>',
'welcome','width=300,height=200,menubar=no,status=no,location=no,toolbar=no,scrollbars=no')" class="refineby">
                  Broken ?</a></div></td>
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
                              <td bgcolor="#FDE7C8"><div align="center"><span class="style6"><?php echo $row_rsproducts1['productname']; ?></span></div></td>
                              <td width="1" bgcolor="#FFFFFF"><a href="/process/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/" target="_blank"><img src="/images/visitnow.gif" width="73" height="26" border="0" /></a></td>
                            </tr>
                          </table>
                        </div>
                        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#DFE6EA">
                          
                          <tbody>
                            <tr>
                              <td valign="top" bgcolor="#FFFFFF" ><br />
                                <table width="1" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#96ACBA"  class="topborder">
                                  <tr>
                                    <td bgcolor="#F1F0E4"><a href="/process/<?php echo $row_rsproducts1['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['productname']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_rsproducts1['catename']); ?>/"><img src="<?php echo $row_rsproducts1['productimage']; ?>" alt="<?php echo $row_rsproducts1['productname']; ?>" border="0" /></a></td>
                                  </tr>
                                </table>
                                  <div align="center">
                                    <input type="hidden" name="kt_pk_products" class="id_checkbox" value="<?php echo $row_rsproducts1['productid']; ?>" />
                                        <input type="hidden" name="productid" class="id_field" value="<?php echo $row_rsproducts1['productid']; ?>" />
<br />
                                  </div>                                  </td>
                              </tr>
</tbody>
                        </table>
                        
                        
                          <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DFE6EA">
                            <tr>
                              <td height="22" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="5">
                                <tr>
                                  <td>
                                    <?php
                              if($row_rsproducts1['productinfo'])
							  {
							  ?><span class="descriptionlight"><strong>Short Info : </strong></span><span class="descriptionlight"><?php echo $row_rsproducts1['productinfo']; ?></span><br />
                                      <?php
                              }
							  else
							  {
							  }
							  ?>
                              <?php
                              if($row_rsproducts1['productdescription'])
							  {
							  ?><span class="descriptionlight"><strong>Product Detail : </strong></span><span class="descriptionlight"><?php echo $row_rsproducts1['productdescription']; ?></span> <br />
                                      <?php
                              }
							  else
							  {
							  }
							  ?>                              </td>
                                </tr>
                              </table></td>
                            </tr>
                          </table>
                          <table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#DFE6EA">
                            <tr>
                              <td width="1" height="1" bgcolor="#FFFFFF" class="refineby">
                                <table width="1" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#96ACBA">
                                  <tr>
                                    <td bgcolor="#F1F0E4"><a href="<?php echo $row_rsproducts1['productlink']; ?>" target="_blank"><img src="<?php echo $row_rsproducts1['programlogo']; ?>" alt="<?php echo $row_rsproducts1['programname']; ?>" border="0" /></a></td>
                                  </tr>
                                </table></td>
                              <td bgcolor="#FFFFFF" class="style23"><span class="style2"><?php
                              if($row_rsproducts1['programname'])
							  {
							  ?>
                              <span class="descriptionlight">Program Name : </span><?php echo $row_rsproducts1['programname']; ?>
							  <?php
                              }
							  else
							  {
							  }
							  ?>                             
                              
                              
                              
                              
                              <?php
                              if($row_rsproducts1['productbrand'])
							  {
							  ?>
                              <span class="descriptionlight"> Brand </span><span class="refineby">: </span><?php echo $row_rsproducts1['productbrand']; ?></span>
							  <?php
                              }
							  else
							  {
							  }
							  ?></td>
                              </tr>
                          </table>
                          <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DFE6EA">
                            <tr>
                              <td bgcolor="#EBF0F3" class="style23"><span class="refineby">Genres : </span><span class="searchbottomlinks"><?php echo $row_rsproducts1['Genres']; ?></span></td>
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
