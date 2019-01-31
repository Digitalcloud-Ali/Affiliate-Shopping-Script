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
$formValidation->addField("section", true, "text", "", "1", "50", "");
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
$ins_cates = new tNG_multipleInsert($conn_shops);
$tNGs->addTransaction($ins_cates);
// Register triggers
$ins_cates->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_cates->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_cates->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$ins_cates->setTable("cates");
$ins_cates->addColumn("section", "STRING_TYPE", "POST", "section");
$ins_cates->setPrimaryKey("cateid", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_cates = new tNG_multipleUpdate($conn_shops);
$tNGs->addTransaction($upd_cates);
// Register triggers
$upd_cates->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_cates->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_cates->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$upd_cates->setTable("cates");
$upd_cates->addColumn("section", "STRING_TYPE", "POST", "section");
$upd_cates->setPrimaryKey("cateid", "NUMERIC_TYPE", "GET", "cateid");

// Make an instance of the transaction object
$del_cates = new tNG_multipleDelete($conn_shops);
$tNGs->addTransaction($del_cates);
// Register triggers
$del_cates->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Delete1");
$del_cates->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$del_cates->setTable("cates");
$del_cates->setPrimaryKey("cateid", "NUMERIC_TYPE", "GET", "cateid");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);

// Get the transaction recordset
$rscates = $tNGs->getRecordset("cates");
$row_rscates = mysql_fetch_assoc($rscates);
$totalRows_rscates = mysql_num_rows($rscates);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Categories</title>
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
if (@$_GET['cateid'] == "") {
?>
              <?php echo NXT_getResource("Insert_FH"); ?>
              <?php 
// else Conditional region1
} else { ?>
              <?php echo NXT_getResource("Update_FH"); ?>
              <?php } 
// endif Conditional region1
?>
Cates </p>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F9FAFA">&nbsp;</td>
            <td height="11" bgcolor="#F9FAFA"><div class="KT_tng">
              <div class="KT_tngform">
                  <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
                    <?php $cnt1 = 0; ?>
                    <?php do { ?>
                      <?php $cnt1++; ?>
                      <?php 
// Show IF Conditional region1 
if (@$totalRows_rscates > 1) {
?>
                        <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
                        <?php } 
// endif Conditional region1
?>
                      <table cellpadding="2" cellspacing="0" class="KT_tngtable">
                        <tr>
                          <td class="KT_th"><label for="section_<?php echo $cnt1; ?>" class="style6">Section:</label></td>
                          <td><input name="section_<?php echo $cnt1; ?>" type="text" class="refineby2" id="section_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rscates['section']); ?>" size="32" maxlength="100" />
                              <?php echo $tNGs->displayFieldHint("section");?> <?php echo $tNGs->displayFieldError("cates", "section", $cnt1); ?> </td>
                        </tr>
                      </table>
                      <input type="hidden" name="kt_pk_cates_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rscates['kt_pk_cates']); ?>" />
                      <?php } while ($row_rscates = mysql_fetch_assoc($rscates)); ?>
                    <div class="KT_bottombuttons">
                      <div>
                        <?php 
      // Show IF Conditional region1
      if (@$_GET['cateid'] == "") {
      ?>
                          <input name="KT_Insert1" type="submit" class="button" id="KT_Insert1" value="<?php echo NXT_getResource("Insert_FB"); ?>" />
                          <?php 
      // else Conditional region1
      } else { ?>
                          <div class="KT_operations">
                            <input name="KT_Insert1" type="submit" class="button" onclick="nxt_form_insertasnew(this, 'cateid')" value="<?php echo NXT_getResource("Insert as new_FB"); ?>" />
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
