<table width="300" border="0" cellpadding="0" cellspacing="0" bgcolor="#F9F9F2" class="topborder">
      <tr>
        <td width="300" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="5" height="19">&nbsp;</td>
            <td width="737" height="1"><span class="style9 style33">Categories :</span></td>
            <td width="14" rowspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="5">&nbsp;</td>
            <td valign="top"><span class="style23 style31">/</span>
              <?php do { ?>
            <span><a href="/explore/<?php echo $row_sections['cateid']; ?>/<?php echo $string = str_replace(' ', '+', $row_sections['section']); ?>/" class="stylecates15"><?php echo $row_sections['section']; ?></a></span>
                <span class="style23 style32">/ </span>               
                <?php } while ($row_sections = mysql_fetch_assoc($sections)); ?></td>
            </tr>
        </table></td>
      </tr>
    </table>