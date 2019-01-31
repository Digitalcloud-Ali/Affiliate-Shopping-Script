<?php require_once('../includes/shops.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_shops = new KT_connection($shops, $database_shops);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_shops, "../");
//Grand Levels: Any
$restrict->Execute();
//End Restrict Access To Page

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome To Administrator Area</title>
<link href="../includes/style.css" rel="stylesheet" type="text/css" />
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script src="../includes/skins/style.js" type="text/javascript"></script>
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
            <td width="1"><img src="../images/shoppingbag.jpg" width="110" height="110" /></td>
            <td width="700"><p class="style6">Welcome To Administrator Area</p>
              <p align="justify" class="refineby2">Here you can control all the affiliate products and the categories and shopping stores. Browse through the menu to
                To visit the section you want to manage or for any furthur support go to help section to find quick helps and supports
                To manage the admin area and to change staffs. There are some important things we have to keep in mind during adding
                products and shops. All important things you will find bottom of each manage page. Follow those instruction when adding
                any product or shop. In this way you will able to insert those things correctly if there is any mistak during adding staff the product
            or shop will not appear correct in there place or the link will not redirect to actual affiliate page. So please follow the instructions.</p></td>
          </tr>
          <tr>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td bgcolor="#F9FAFA"><p class="refineby2"><strong>• Categories</strong><span class="stylecatestwo"> » </span> Manage all category names<br />
              <br />
                <strong>• Sub Categories</strong> <span class="stylecatestwo"> » </span> Manage Sub Categories of Main Category Names<br />
                <br />
                <strong>• Products</strong> <span class="stylecatestwo"> » </span> Manage The Products Listening Under The Categories Or Sub Categories<br />
                <br />
                <strong>• Shops</strong> <span class="stylecatestwo"> » </span> Manage the Shopping stores listening in the website<br />
                <br />
                <strong>• Pages</strong> <span class="stylecatestwo"> » </span> Manage the pages of websites ( e.g. Support / Terms )<br />
            </p>
            </td>
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
