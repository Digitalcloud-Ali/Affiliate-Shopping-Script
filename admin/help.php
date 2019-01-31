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
            <td width="1"><img src="../images/help.jpg" width="110" height="83" /></td>
            <td width="700"><p class="style6">Help :</p>
              <p align="justify" class="refineby2">Here you will able to find some quick helps to use the administrator functions and modules.<br />
            Follow the Help tricks if you find any serious matter and is not able to understand any function. If you did not found the help and having the problem still the contact the developer at ask@rayice.com.</p></td>
          </tr>
          <tr>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td bgcolor="#F9FAFA"><p class="refineby2"><strong>• Categories</strong><span class="stylecatestwo"> » </span>In Categories Section you can add main categories under which the products will be listening. You must have to carefull in following things when you are adding any category.<br />
              <br />
                <span class="descriptionlight">1. Use the ( and ) Instead of ( &amp; ) within category name. E.G Art and Antique Not Art &amp; Antique. It will case the problem to open the category.<br />  
                  <br />
                2. <strong>Show Filter :</strong> Will have you to find the category quick by searching its name or starting spell.<br />    
                    <br />
                3. Edit / Del Functions will help you to manage the categories. If you want to manage the category you already created then select it and click edit. If you want to add new category Simply click on ( Add New )</span></p>
              <p class="descriptionlight">4. At Edit Page You will see 4 buttons. ( Insert as new , Update , Delete , Cancel ). If you edit any name and want to update same name then click on ( Update ). If you want to put new category of same name , then change the name and click at ( Insert as New )</p>
              <p class="refineby2"><span class="style23">Important </span>: Donot submit the Same Categories Name. First check the category you are going to add is already in list or not. Double name will case the problem.<br />
              </p>
              <p class="refineby2"><br />
              <strong>• Sub Categories</strong> <span class="stylecatestwo"> » </span> Sub Categories that will be add under any Category you have added in Categories Section. Sub Category will be act as sub section of the main Category Section.</p>
              <p class="descriptionlight">1. You must type the correct and same name of Main Category under which you are going to add the sub category , the ( Section or Main Category ) Should same name of the main category you already added. Otherwise it will cause the problem.</p>
              <p class="descriptionlight">2. Donot submit the duplicate names. First check if the sub category you are going to adding is not inserted already.</p>
              <p class="refineby2"><span class="descriptionlight">Buttons will work same as we already discuss at Categories Help Section.</span></p>
              <p class="refineby2"><br />
                <br />
                <strong>• Products</strong> <span class="stylecatestwo"> » </span> Product is the Important part of website here you have to care full much.</p>
              <p class="descriptionlight">1. When adding product you must have the correct name of ( Main Category &amp; Sub Category ). You can find the Main Categories &amp; Sub Categories ) you already added at Categories and Sub Categories manage area.</p>
              <p class="descriptionlight">2. Product Name Should be around 50 words. Not more then that.</p>
              <p class="refineby2"><span class="stylecatestwo">Important :</span> Look the sample post at &gt;&gt; Manage : Products &gt;&gt; Add New &gt;&gt; Bottom of edit page.</p>
              <p class="refineby2"><br />
                <br />
              <strong>• Shops</strong> <span class="stylecatestwo"> » </span> Here you can add Affiliate Links Direct To Shopping Stores.</p>
              <p class="descriptionlight">1. Shop Name Should Be Less Between 20 to 50 Words.</p>
              <p class="descriptionlight">2. Shop Information : Must be Unique Words.</p>
              <p class="descriptionlight">3. Shop image will be first upload to the FTP /Images/stores/ and then give the correct path to image of shopping E.G ( /images/stores/amazon.gif )</p>
              <p class="descriptionlight">4. Shop Link will be the affiliate link which will be redirect to orignal shopping store.</p>
              <p class="refineby2"><span class="stylecatestwo">Important :</span> Donot Submit the Duplicate Shops Of Same Store With Diffrent Affiliate Links. It will cause the problem.</p>
              <p class="refineby2"><br />
                <br />
              <strong>• Pages</strong> <span class="stylecatestwo"> » </span> You can manage the pages of website here.</p>
              <p class="descriptionlight">1. Terms Of Use : Terms of websites. Donot Change the Title Of Pages. Only Edit the Informations Of that.</p>
              <p class="descriptionlight">2. Support : Here you can put some of support information you want to. Donot change the Title Of this page.</p>
              <p class="descriptionlight">3. Buzz Cloud : Here you will able to write the code to some keywords of buzz cloud. Using which you will able to get some listening to keywords and quick searchs for visitors. Donot Change the Title Of Buzz Cloud. Change the Information Only.</p>
              <p class="refineby2"><span class="stylecatestwo">Important :</span> Donot add any new page there. Only change the current pages information.<br />
                                            </p></td>
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
