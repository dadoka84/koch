<?
/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

  if($fmSent) {
    $ok = false;
    if(get_magic_quotes_gpc()) $fmText = stripslashes($fmText);

    if($ftp) {
      $tmp = str_replace('\\', '/', dirname(__FILE__)) . '/tmp';
      if($fp = @fopen("$tmp/$fmEdit", 'w')) {
        @fwrite($fp, $fmText);
        @fclose($fp);
        $ok = fm_upload("$tmp/$fmEdit", "$fmCurDir/$fmEdit");
        @unlink("$tmp/$fmEdit");
      }
    }
    else if($fp = @fopen("$fmCurDir/$fmEdit", 'w')) {
      $ok = @fwrite($fp, $fmText);
      @fclose($fp);
    }

    if(!$ok) $fmError = $msg['errSave'] . ": $fmEdit";

    include('listing.inc.php');
  }
  else if($file = fm_get("$fmCurDir/$fmEdit")) {
?>
    <form name="fEdit" class="fmForm" action="<? echo $PHP_SELF; ?>" method="post">
    <input type="hidden" name="fmSent" value="1">
    <input type="hidden" name="fmEdit" value="<? echo $fmEdit; ?>">
    <input type="hidden" name="fmCurDir" value="<? echo $fmCurDir; ?>">
    <input type="hidden" name="fmSortField" value="<? echo $fmSortField; ?>">
    <input type="hidden" name="fmSortOrder" value="<? echo $fmSortOrder; ?>">
    <table border="0" cellspacing="0" cellpadding="4"><tr>
    <td class="fmTH1" align="left"><? echo $msg['cmdEdit'] . ": $fmEdit"; ?></td>
    <td class="fmTH1" align="right" nowrap>
    <a href="<? echo $manager; ?>"
     title="<? echo $msg['cmdViewList']; ?>"
     onMouseOver="window.status='<? echo $msg['cmdViewList']; ?>'; return true"
     onMouseOut="window.status=''">
    <img src="<? echo $fmWebPath; ?>/icons/list.gif" border="0" width="14" height="14" alt="<? echo $msg['cmdViewList']; ?>"></a>
    &nbsp;
    <a href="javascript:document.fEdit.reset()"
     title="<? echo $msg['cmdReset']; ?>"
     onMouseOver="window.status='<? echo $msg['cmdReset']; ?>'; return true"
     onMouseOut="window.status=''">
    <img src="<? echo $fmWebPath; ?>/icons/reset.gif" border="0" width="14" height="14" alt="<? echo $msg['cmdReset']; ?>"></a>
    <a href="javascript:fmGoToOK('<? echo $msg['msgSaveFile']; ?>', 'javascript:document.fEdit.submit()')"
     title="<? echo $msg['cmdSave']; ?>"
     onMouseOver="window.status='<? echo $msg['cmdSave']; ?>'; return true"
     onMouseOut="window.status=''">
    <img src="<? echo $fmWebPath; ?>/icons/save.gif" border="0" width="14" height="14" alt="<? echo $msg['cmdSave']; ?>"></a>
    </td>
    </tr><tr>
    <td class="fmTH3" colspan="2" align="center">
    <?
      if($fp = @fopen($file, 'r')) {
        $content = @fread($fp, filesize($file));
        @fclose($fp);
      }
    ?>
    <textarea name="fmText" style="width:<? echo $fmWidth - 20; ?>px; height:<? echo $maskHeight - 80; ?>px" wrap="off" class="fmField"><? echo htmlspecialchars($content); ?></textarea>
    </td>
    </tr></table>
    </form>
<?
  }
  else {
    $fmError = $msg['errOpen'] . ": $fmEdit";
    include('listing.inc.php');
  }
?>
