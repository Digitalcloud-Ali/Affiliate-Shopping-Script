<table width="100%" border="0" cellpadding="0" cellspacing="0">

  <tr>

    <td width="21" height="50">&nbsp;</td>

    <td width="1"><a href="/"><img src="/images/logo.gif" alt="Home! - Uk Online Discount Products &amp; Discount Shops/Stores" width="267" height="62" border="0" /></a></td>

    <td valign="top"><table width="698" border="0" cellspacing="0" cellpadding="0">

      <tr>

        <td width="70%">&nbsp;</td>

        <td width="30%"><table border="0" align="right" cellpadding="2" cellspacing="1">

          <tr>

            <td><div align="center" class="stylecates"><a class="stylecatestwo"

      onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?php echo $linking ?>');"> Set as home page</a></div></td>

            <td><div align="center"><a href="#" class="stylecatestwo"> <span class="style29">|</span></a></div></td>

            <td><a

      onclick="window.external.AddFavorite('<?php echo $linking ?>','<?php echo $slogan ?> - <?php echo $simpleurl ?>');" class="stylecatestwo">Bookmark us</a></td>

          </tr>

        </table></td>

      </tr>

    </table>

      <table width="517" border="0" align="right" cellpadding="0" cellspacing="0">

      <tr>

        <td width="499"><div align="right"></div></td>

        <td width="18">&nbsp;</td>

      </tr>

      <tr>

        <td><div align="right"><span class="style2"><a href="/categories.php" class="stylecates">Browse All</a> <span class="style3">|</span> <a href="/search.php" class="stylecates">Quick Products</a> <span class="style3">| </span><a href="/pages/buzz+cloud.html" class="stylecates">Tags</a></span></div></td>

        <td>&nbsp;</td>

      </tr>

    </table></td>

  </tr>

</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E2E0D6">

  <tr>

    <td width="21" bgcolor="#FFFFFF">&nbsp;</td>

    <td width="954" bgcolor="#F1F0E4"><table width="100%" border="0" cellspacing="0" cellpadding="0">

      <tr>

        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">

          <tr>

            <td height="5"></td>

          </tr>

        </table>

        <table width="100%" border="0" cellpadding="0" cellspacing="0">

          <tr>

            <td width="5">&nbsp;</td>

            <td width="468" height="60" bgcolor="#FEFBE7"><div align="center"><a href="http://www.wwwfull.com"><img src="http://www.wwwfull.com/image.gif" width="468" height="60" border="0" /></a><div></td>

            <td valign="top"><form id="form1" name="form1" method="post" action="/search.php?" style="margin:0px;">

              <table width="1" border="0" align="center" cellpadding="0" cellspacing="0">

                <tr>

                  <td height="33" class="style30">Products </td>

                  <td width="1"><span class="KT_row_filter">

                    <input name="tfi_listproducts3_productname" type="text" class="form" id="tfi_listproducts3_productname" size="62" />

                  </span></td>

                  <td width="1"><span class="KT_row_filter">

                    <input name="tfi_listproducts3" type="submit" class="button" value="Search" />

                  </span></td>

                </tr>

                <tr>

                  <td height="26" colspan="3"><div align="left">

                      <div align="right"><span class="style28"><a href="advance.php" class="style28"></a><span class="KT_row_filter"> </span><input name="tfi_listproducts" type="hidden" id="tfi_listproducts3" /> 

                      <a href="/advance.php" class="style28"> Advanced Search</a></span> <span class="style23">| </span><span class="searchbottomlinks"><a href="/sitemap.php" class="style28">Shortcuts</a></span></div>

                  </div></td>

                </tr>

              </table>

                        </form>

            </td>

          </tr>

        </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <td height="5"></td>

  </tr>

</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">

          <tr>

            <td width="5" height="19">&nbsp;</td>

            <td width="737" height="1"><span class="style9 style33">Discount Stores :</span></td>

            <td width="14" rowspan="2">&nbsp;</td>

          </tr>

          <tr>

            <td width="5">&nbsp;</td>

            <td valign="top"><span class="style23">/</span>

              <?php do { ?>

            <span class="stylecates"><a href="/stores/<?php echo $string = str_replace(' ', '+', $row_stores['shopid']); ?>/<?php echo $string = str_replace(' ', '+', $row_stores['shopname']); ?>.html" class="stylecates"><?php echo $row_stores['shopname']; ?></a></span>

                <span class="style23">/ </span>               

                <?php } while ($row_stores = mysql_fetch_assoc($stores)); ?></td>

            </tr>

        </table>

          <table width="100%" border="0" cellspacing="0" cellpadding="0">

            <tr>

              <td height="5"></td>

            </tr>

          </table></td>

      </tr>

    </table></td>

    <td width="21" bgcolor="#FFFFFF">&nbsp;</td>

  </tr>

</table>

<table width="1003" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <td width="22">&nbsp;</td>

    <td width="957">&nbsp;</td>

    <td width="24">&nbsp;</td>

  </tr>

</table>