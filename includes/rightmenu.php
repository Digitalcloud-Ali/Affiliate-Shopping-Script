<table width="100" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="cateheading">&nbsp;</td>
        </tr>
      </table>
      <table width="300" border="0" cellpadding="0" cellspacing="0" bgcolor="#E2E0D6" class="topborder">
      <tr>
        <td width="300" height="250" valign="top" bgcolor="#F1F0E4"><script type="text/javascript"><!--
google_ad_client = "pub-2890343354362681";
/* 300x250, New created 19/09/08 */
google_ad_slot = "7222790903";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></td>
      </tr>
    </table>
      <table width="100" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="cateheading">&nbsp;</td>
        </tr>
        <tr>
          <td class="style9 style33">New Shops</td>
        </tr>
      </table>
      <table width="300" border="0" cellpadding="5" cellspacing="0" bgcolor="#E2E0D6" class="topborder">
        <tr>
          <td width="300" valign="top" bgcolor="#F1F0E4"><?php do { ?>
                <span class="arrows">›</span> <a href="/stores/<?php echo $string = str_replace(' ', '+', $row_newstores['shopname']); ?>.html" class="stylecates"><?php echo $row_newstores['shopname']; ?></a><br />
                <?php } while ($row_newstores = mysql_fetch_assoc($newstores)); ?></td>
        </tr>
      </table>
      <table width="100" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="cateheading">&nbsp;</td>
        </tr>
        <tr>
          <td class="style9 style33">New Products</td>
        </tr>
      </table>
      <table width="300" border="0" cellpadding="5" cellspacing="0" bgcolor="#E2E0D6" class="topborder">
        <tr>
          <td width="300" valign="top" bgcolor="#F1F0E4">
            <?php do { ?><a href="/products/<?php echo $row_popularproducts['productid']; ?>/<?php echo $string = str_replace(' ', '+', $row_popularproducts['catename']); ?>/<?php echo $string = str_replace(' ', '+', $row_popularproducts['section']); ?>/<?php echo $string = str_replace(' ', '+', $row_popularproducts['productname']); ?>.html" class="stylecates">
              ■ <?php echo KT_FormatForList($row_popularproducts['productname'], 50); ?></br></a>
              <?php } while ($row_popularproducts = mysql_fetch_assoc($popularproducts)); ?></td>
        </tr>
      </table>