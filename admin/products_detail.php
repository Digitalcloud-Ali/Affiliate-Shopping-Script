<?php require_once('../includes/shops.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Load the KT_back class
require_once('../includes/nxt/KT_back.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_shops = new KT_connection($shops, $database_shops);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_shops, "../");
//Grand Levels: Any
$restrict->Execute();
//End Restrict Access To Page

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("productname", true, "text", "", "", "100", "");
$formValidation->addField("productinfo", true, "text", "", "", "", "");
$formValidation->addField("productdescription", true, "text", "", "", "", "");
$formValidation->addField("productlink", true, "text", "", "", "500", "");
$formValidation->addField("productimage", true, "text", "", "", "500", "");
$formValidation->addField("productprice", true, "text", "", "", "100", "");
$formValidation->addField("programlogo", false, "text", "", "", "500", "");
$formValidation->addField("programname", false, "text", "", "", "100", "");
$formValidation->addField("catename", true, "text", "", "", "50", "");
$formValidation->addField("section", true, "text", "", "", "50", "");
$formValidation->addField("currency", true, "text", "", "", "10", "");
$tNGs->prepareValidation($formValidation);
// End trigger

// Make a logout transaction instance
$logoutTransaction = new tNG_logoutTransaction($conn_shops);
$tNGs->addTransaction($logoutTransaction);
// Register triggers
$logoutTransaction->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "KT_logout_now");
$logoutTransaction->registerTrigger("END", "Trigger_Default_Redirect", 99, "login.php");
// Add columns
// End of logout transaction instance

// Make an insert transaction instance
$ins_products = new tNG_multipleInsert($conn_shops);
$tNGs->addTransaction($ins_products);
// Register triggers
$ins_products->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_products->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_products->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$ins_products->setTable("products");
$ins_products->addColumn("productname", "STRING_TYPE", "POST", "productname");
$ins_products->addColumn("productinfo", "STRING_TYPE", "POST", "productinfo");
$ins_products->addColumn("productdescription", "STRING_TYPE", "POST", "productdescription");
$ins_products->addColumn("productlink", "STRING_TYPE", "POST", "productlink");
$ins_products->addColumn("productimage", "STRING_TYPE", "POST", "productimage");
$ins_products->addColumn("productbrand", "STRING_TYPE", "POST", "productbrand");
$ins_products->addColumn("productprice", "STRING_TYPE", "POST", "productprice");
$ins_products->addColumn("programlogo", "STRING_TYPE", "POST", "programlogo");
$ins_products->addColumn("programname", "STRING_TYPE", "POST", "programname");
$ins_products->addColumn("catename", "STRING_TYPE", "POST", "catename");
$ins_products->addColumn("section", "STRING_TYPE", "POST", "section");
$ins_products->addColumn("currency", "STRING_TYPE", "POST", "currency");
$ins_products->addColumn("Genres", "STRING_TYPE", "POST", "Genres");
$ins_products->setPrimaryKey("productid", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_products = new tNG_multipleUpdate($conn_shops);
$tNGs->addTransaction($upd_products);
// Register triggers
$upd_products->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_products->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_products->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$upd_products->setTable("products");
$upd_products->addColumn("productname", "STRING_TYPE", "POST", "productname");
$upd_products->addColumn("productinfo", "STRING_TYPE", "POST", "productinfo");
$upd_products->addColumn("productdescription", "STRING_TYPE", "POST", "productdescription");
$upd_products->addColumn("productlink", "STRING_TYPE", "POST", "productlink");
$upd_products->addColumn("productimage", "STRING_TYPE", "POST", "productimage");
$upd_products->addColumn("productbrand", "STRING_TYPE", "POST", "productbrand");
$upd_products->addColumn("productprice", "STRING_TYPE", "POST", "productprice");
$upd_products->addColumn("programlogo", "STRING_TYPE", "POST", "programlogo");
$upd_products->addColumn("programname", "STRING_TYPE", "POST", "programname");
$upd_products->addColumn("catename", "STRING_TYPE", "POST", "catename");
$upd_products->addColumn("section", "STRING_TYPE", "POST", "section");
$upd_products->addColumn("currency", "STRING_TYPE", "POST", "currency");
$upd_products->addColumn("Genres", "STRING_TYPE", "POST", "Genres");
$upd_products->setPrimaryKey("productid", "NUMERIC_TYPE", "GET", "productid");

// Make an instance of the transaction object
$del_products = new tNG_multipleDelete($conn_shops);
$tNGs->addTransaction($del_products);
// Register triggers
$del_products->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Delete1");
$del_products->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$del_products->setTable("products");
$del_products->setPrimaryKey("productid", "NUMERIC_TYPE", "GET", "productid");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);

// Get the transaction recordset
$rsproducts = $tNGs->getRecordset("products");
$row_rsproducts = mysql_fetch_assoc($rsproducts);
$totalRows_rsproducts = mysql_num_rows($rsproducts);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Products</title>
<link href="../includes/style.css" rel="stylesheet" type="text/css" />
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script src="../includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>
<script src="../includes/nxt/scripts/form.js" type="text/javascript"></script>
<script src="../includes/nxt/scripts/form.js.php" type="text/javascript"></script>
<script type="text/javascript">
$NXT_FORM_SETTINGS = {
  duplicate_buttons: false,
  show_as_grid: true,
  merge_down_value: true
}
</script>
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
    <td>
      <div align="left">
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
            <td width="1">&nbsp;</td>
            <td width="700"><p class="style6">
              <?php 
// Show IF Conditional region1 
if (@$_GET['productid'] == "") {
?>
              <?php echo NXT_getResource("Insert_FH"); ?>
              <?php 
// else Conditional region1
} else { ?>
              <?php echo NXT_getResource("Update_FH"); ?>
              <?php } 
// endif Conditional region1
?>
Products </p>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td bgcolor="#F9FAFA"><div class="KT_tng">
              <div class="KT_tngform">
                  <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
                    <?php $cnt1 = 0; ?>
                    <?php do { ?>
                      <?php $cnt1++; ?>
                      <?php 
// Show IF Conditional region1 
if (@$totalRows_rsproducts > 1) {
?>
                        <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
                        <?php } 
// endif Conditional region1
?>
                      <table cellpadding="2" cellspacing="0" class="KT_tngtable">
                        <tr>
                          <td class="KT_th"><label for="productname_<?php echo $cnt1; ?>">Productname:</label></td>
                          <td><input type="text" name="productname_<?php echo $cnt1; ?>" id="productname_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['productname']); ?>" size="32" maxlength="100" />
                              <?php echo $tNGs->displayFieldHint("productname");?> <?php echo $tNGs->displayFieldError("products", "productname", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="productinfo_<?php echo $cnt1; ?>">Productinfo:</label></td>
                          <td><textarea name="productinfo_<?php echo $cnt1; ?>" id="productinfo_<?php echo $cnt1; ?>" cols="50" rows="5"><?php echo KT_escapeAttribute($row_rsproducts['productinfo']); ?></textarea>
                              <?php echo $tNGs->displayFieldHint("productinfo");?> <?php echo $tNGs->displayFieldError("products", "productinfo", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="productdescription_<?php echo $cnt1; ?>">Productdescription:</label></td>
                          <td><textarea name="productdescription_<?php echo $cnt1; ?>" id="productdescription_<?php echo $cnt1; ?>" cols="50" rows="5"><?php echo KT_escapeAttribute($row_rsproducts['productdescription']); ?></textarea>
                              <?php echo $tNGs->displayFieldHint("productdescription");?> <?php echo $tNGs->displayFieldError("products", "productdescription", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="productlink_<?php echo $cnt1; ?>">Productlink:</label></td>
                          <td><input type="text" name="productlink_<?php echo $cnt1; ?>" id="productlink_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['productlink']); ?>" size="32" />
                              <?php echo $tNGs->displayFieldHint("productlink");?> <?php echo $tNGs->displayFieldError("products", "productlink", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="productimage_<?php echo $cnt1; ?>">Productimage:</label></td>
                          <td><input type="text" name="productimage_<?php echo $cnt1; ?>" id="productimage_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['productimage']); ?>" size="32" />
                              <?php echo $tNGs->displayFieldHint("productimage");?> <?php echo $tNGs->displayFieldError("products", "productimage", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="productbrand_<?php echo $cnt1; ?>">Productbrand:</label></td>
                          <td><input type="text" name="productbrand_<?php echo $cnt1; ?>" id="productbrand_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['productbrand']); ?>" size="32" maxlength="100" />
                              <?php echo $tNGs->displayFieldHint("productbrand");?> <?php echo $tNGs->displayFieldError("products", "productbrand", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="productprice_<?php echo $cnt1; ?>">Productprice:</label></td>
                          <td><input type="text" name="productprice_<?php echo $cnt1; ?>" id="productprice_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['productprice']); ?>" size="32" maxlength="100" />
                              <?php echo $tNGs->displayFieldHint("productprice");?> <?php echo $tNGs->displayFieldError("products", "productprice", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="programlogo_<?php echo $cnt1; ?>">Programlogo:</label></td>
                          <td><input type="text" name="programlogo_<?php echo $cnt1; ?>" id="programlogo_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['programlogo']); ?>" size="32" />
                              <?php echo $tNGs->displayFieldHint("programlogo");?> <?php echo $tNGs->displayFieldError("products", "programlogo", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="programname_<?php echo $cnt1; ?>">Programname:</label></td>
                          <td><input type="text" name="programname_<?php echo $cnt1; ?>" id="programname_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['programname']); ?>" size="32" maxlength="100" />
                              <?php echo $tNGs->displayFieldHint("programname");?> <?php echo $tNGs->displayFieldError("products", "programname", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="catename_<?php echo $cnt1; ?>">Sub Category:</label></td>
                          <td><input type="text" name="catename_<?php echo $cnt1; ?>" id="catename_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['catename']); ?>" size="32" maxlength="50" />
                              <?php echo $tNGs->displayFieldHint("catename");?> <?php echo $tNGs->displayFieldError("products", "catename", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="section_<?php echo $cnt1; ?>">Main Category:</label></td>
                          <td><input type="text" name="section_<?php echo $cnt1; ?>" id="section_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['section']); ?>" size="32" maxlength="50" />
                              <?php echo $tNGs->displayFieldHint("section");?> <?php echo $tNGs->displayFieldError("products", "section", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="currency_<?php echo $cnt1; ?>">Currency:</label></td>
                          <td><input type="text" name="currency_<?php echo $cnt1; ?>" id="currency_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['currency']); ?>" size="32" maxlength="10" />
                              <?php echo $tNGs->displayFieldHint("currency");?> <?php echo $tNGs->displayFieldError("products", "currency", $cnt1); ?> </td>
                        </tr>
                        <tr>
                          <td class="KT_th"><label for="Genres_<?php echo $cnt1; ?>">Genres:</label></td>
                          <td><input type="text" name="Genres_<?php echo $cnt1; ?>" id="Genres_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsproducts['Genres']); ?>" size="32" />
                              <?php echo $tNGs->displayFieldHint("Genres");?> <?php echo $tNGs->displayFieldError("products", "Genres", $cnt1); ?> </td>
                        </tr>
                      </table>
                      <input type="hidden" name="kt_pk_products_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rsproducts['kt_pk_products']); ?>" />
                      <?php } while ($row_rsproducts = mysql_fetch_assoc($rsproducts)); ?>
                    <div class="KT_bottombuttons">
                      <div>
                        <?php 
      // Show IF Conditional region1
      if (@$_GET['productid'] == "") {
      ?>
                          <input name="KT_Insert1" type="submit" class="button" id="KT_Insert1" value="<?php echo NXT_getResource("Insert_FB"); ?>" />
                          <?php 
      // else Conditional region1
      } else { ?>
                          <div class="KT_operations">
                            <input name="KT_Insert1" type="submit" class="button" onclick="nxt_form_insertasnew(this, 'productid')" value="<?php echo NXT_getResource("Insert as new_FB"); ?>" />
                          </div>
                          <input name="KT_Update1" type="submit" class="button" value="<?php echo NXT_getResource("Update_FB"); ?>" />
                          <input name="KT_Delete1" type="submit" class="button" onclick="return confirm('<?php echo NXT_getResource("Are you sure?"); ?>');" value="<?php echo NXT_getResource("Delete_FB"); ?>" />
                          <?php }
      // endif Conditional region1
      ?>
                        <input name="KT_Cancel1" type="button" class="button" onclick="return UNI_navigateCancel(event, '../includes/nxt/back.php')" value="<?php echo NXT_getResource("Cancel_FB"); ?>" />
                      </div>
                    </div>
                  </form>
                </div>
                </p>
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
