<?
/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

  clearstatcache();
  $info = array();

  if($ftp) {
    $systype = @ftp_systype($ftp);
    if(stristr($systype, 'winnt') || stristr($systype, 'windows')) $systype = 'Windows';
    else $systype = 'UNIX';
    $list = @ftp_rawlist($ftp, $fmCurDir);

    if(is_array($list)) foreach($list as $row) {
      $arr = fm_get_info($row, $systype);
      if($arr) $info[] = $arr;
    }
  }
  else if($dp = @opendir($fmCurDir)) {
    $os = function_exists('php_uname') ? php_uname() : PHP_OS;
    if(stristr($os, 'winnt') || stristr($os, 'windows')) $systype = 'Windows';
    else $systype = 'UNIX';

    while($file = readdir($dp)) {
      $arr = fm_get_info("$fmCurDir/$file");
      if($arr) $info[] = $arr;
    }
    @closedir($dp);
  }
  else $fmError = $msg['errOpen'] . ": $fmCurDir";

  $icons = $fmWebPath . '/icons';

  if(!$fmSortField) {
    $fmSortField = 'permissions';
    $fmSortOrder = 'desc';
  }
  $sr = ($fmSortOrder == 'asc') ? 'desc' : 'asc';
  $tooltip = ($sr == 'asc') ? $msg['cmdSortAsc'] : $msg['cmdSortDesc'];
  $imgSort = "$icons/sort_$fmSortOrder.gif";
  $pImg = ($fmSortField == 'permissions') ? $imgSort : "$icons/blank.gif";

  if(strlen($fmCurDir) > 40) {
    $arr = explode('/', $fmCurDir);
    $len = count($arr);
    $short = $arr[0] . '/' . $arr[1] . '/.../' . $arr[$len-2] . '/' . $arr[$len-1];
    $title = "[$systype] $short";
  }
  else $title = "[$systype] $fmCurDir";
?>
<script language="JavaScript"> <!--
function fmFileInfo(permissions, owner, group, size, changed, name, thumb, width, height) {
  var info = '<table border="0" cellspacing="1" cellpadding="1"><tr>' +
             '<td class="fmContent"><b><? echo $msg['name']; ?>:</b></td><td class="fmContent" nowrap>' + name + '</td>' +
             '</tr><tr>' +
             '<td class="fmContent"><b><? echo $msg['permissions']; ?>:</b></td><td class="fmContent">' + permissions + '</td>' +
             '</tr><tr>' +
             '<td class="fmContent"><b><? echo $msg['owner']; ?>:</b></td><td class="fmContent">' + owner + '</td>' +
             '</tr><tr>' +
             '<td class="fmContent"><b><? echo $msg['group']; ?>:</b></td><td class="fmContent">' + group + '</td>' +
             '</tr><tr>' +
             '<td class="fmContent"><b><? echo $msg['lastChange']; ?>:</b></td><td class="fmContent" nowrap>' + changed + '</td>' +
             '</tr><tr>' +
             '<td class="fmContent"><b><? echo $msg['size']; ?>:</b></td><td class="fmContent">' + size + '</td>' +
             ((thumb && width && height) ? '</tr><tr><td colspan="2" height="8"></td></tr><tr><td class="frmContent" colspan="2"><img src="' + thumb + '" width="' + width + '" height="' + height + '"></td>' : '') +
             '</tr></table>';
  fmOpenDialog('fmInfo', info);
}
//--> </script>
<table border="0" cellspacing="0" cellpadding="0" width="<? echo $fmWidth; ?>"><tr>
<td class="fmTH1" style="padding:4px" align="left"><? echo $title; ?></td>
</tr><tr>
<td class="fmTH1">
<table border="0" cellspacing="1" cellpadding="0" width="100%"><tr>
<td class="fmTH2">
<table border="0" cellspacing="1" cellpadding="2" width="100%"><tr align="center">
<td class="fmTH3" width="14" title="<? echo $tooltip; ?>"
 onMouseOver="this.className='fmTH4'; window.status='<? echo $tooltip; ?>'; return true"
 onMouseOut="this.className='fmTH3'; window.status=''"
 onMouseDown="this.className='fmTH5'"
 onMouseUp="this.className='fmTH4'"
 onClick="fmGoTo('<? echo "$manager&fmSortField=permissions&fmSortOrder=$sr"; ?>')">
<img src="<? echo $pImg; ?>" border="0" width="8" height="7">
</td>
<td class="fmTH3" title="<? echo $tooltip; ?>"
 onMouseOver="this.className='fmTH4'; window.status='<? echo $tooltip; ?>'; return true"
 onMouseOut="this.className='fmTH3'; window.status=''"
 onMouseDown="this.className='fmTH5'"
 onMouseUp="this.className='fmTH4'"
 onClick="fmGoTo('<? echo "$manager&fmSortField=name&fmSortOrder=$sr"; ?>')">
&nbsp;<? echo $msg['name']; ?>&nbsp;
<? if($fmSortField == 'name') echo ' <img src="' . $imgSort . '" border="0" width="8" height="7">'; ?>
</td>
<td class="fmTH3" width="15%" title="<? echo $tooltip; ?>"
 onMouseOver="this.className='fmTH4'; window.status='<? echo $tooltip; ?>'; return true"
 onMouseOut="this.className='fmTH3'; window.status=''"
 onMouseDown="this.className='fmTH5'"
 onMouseUp="this.className='fmTH4'"
 onClick="fmGoTo('<? echo "$manager&fmSortField=size&fmSortOrder=$sr"; ?>')">
&nbsp;<? echo $msg['size']; ?>&nbsp;
<? if($fmSortField == 'size') echo ' <img src="' . $imgSort . '" border="0" width="8" height="7">'; ?>
</td>
<td class="fmTH3" width="25%" title="<? echo $tooltip; ?>"
 onMouseOver="this.className='fmTH4'; window.status='<? echo $tooltip; ?>'; return true"
 onMouseOut="this.className='fmTH3'; window.status=''"
 onMouseDown="this.className='fmTH5'"
 onMouseUp="this.className='fmTH4'"
 onClick="fmGoTo('<? echo "$manager&fmSortField=changed&fmSortOrder=$sr"; ?>')">
&nbsp;<? echo $msg['lastChange']; ?>&nbsp;
<? if($fmSortField == 'changed') echo ' <img src="' . $imgSort . '" border="0" width="8" height="7">'; ?>
</td>
<?
  if($enableNewDir) {
?>
    <td class="fmTH3" width="26" title="<? echo $msg['cmdNewDir']; ?>"
     onMouseOver="this.className='fmTH4'; window.status='<? echo $msg['cmdNewDir']; ?>'; return true"
     onMouseOut="this.className='fmTH3'; window.status=''"
     onMouseDown="this.className='fmTH5'"
     onMouseUp="this.className='fmTH4'"
     onClick="fmOpenDialog('fmNewDir', '<? echo $msg['cmdNewDir']; ?>')">
    <img src="<? echo $icons; ?>/newDir.gif" border="0" width="15" height="14" alt="<? echo $msg['cmdNewDir']; ?>">
    </td>
<?
  }
  else {
    $error = $msg['cmdNewDir'] . ': ' . $msg['errDisabled'];
?>
    <td class="fmTH2" width="26"><a href="javascript:fmOpenDialog('fmError', '<? echo $error; ?>')" onMouseOver="window.status=''; return true">
    <img src="<? echo $icons; ?>/newDir_x.gif" border="0" width="15" height="14"></a></td>
<?
  }

  if($enableUpload) {
?>
    <td class="fmTH3" width="26" title="<? echo $msg['cmdUploadFile']; ?>"
     onMouseOver="this.className='fmTH4'; window.status='<? echo $msg['cmdUploadFile']; ?>'; return true"
     onMouseOut="this.className='fmTH3'; window.status=''"
     onMouseDown="this.className='fmTH5'"
     onMouseUp="this.className='fmTH4'"
     onClick="fmOpenDialog('fmNewFile', '<? echo $msg['cmdUploadFile']; ?>')">
    <img src="<? echo $icons; ?>/new.gif" border="0" width="14" height="14" alt="<? echo $msg['cmdUploadFile']; ?>">
    </td>
<?
  }
  else {
    $error = $msg['cmdUploadFile'] . ': ' . $msg['errDisabled'];
?>
    <td class="fmTH2" width="26"><a href="javascript:fmOpenDialog('fmError', '<? echo $error; ?>')" onMouseOver="window.status=''; return true">
    <img src="<? echo $icons; ?>/new_x.gif" border="0" width="14" height="14"></a></td>
<?
  }
?>
</tr>
<?
  if(strlen($fmCurDir) > strlen($startDir)) {
?>
    <tr class="fmTD1" onMouseOver="this.className='fmTD2'" onMouseOut="this.className='fmTD1'">
    <td class="fmContent" align="center">
    <a href="<? echo "$manager&fmChangeDir=.."; ?>"
     title="<? echo $msg['cmdParentDir']; ?>"
     onMouseOver="window.status='<? echo $msg['cmdParentDir']; ?>'; return true"
     onMouseOut="window.status=''">
    <img src="<? echo $icons; ?>/cdup.gif" border="0" width="12" height="10" alt="<? echo $msg['cmdParentDir']; ?>"></a>
    </td>
    <td class="fmContent" align="left">..</td>
    <td class="fmContent">&nbsp;</td>
    <td class="fmContent">&nbsp;</td>
    <td class="fmTD2" colspan="2"><img src="<? echo $icons; ?>/blank.gif" width="10" height="10"></td>
    </tr>
<?
  }

  if(is_array($info)) {
    $info = fm_sort_field($info, $fmSortField, $fmSortOrder);

    foreach($info as $val) {
?>
      <tr class="fmTD1" onMouseOver="this.className='fmTD2'" onMouseOut="this.className='fmTD1'">
      <td class="fmContent" align="center">
<?
      if($val['icon'] == 'dir') {
        $dir = $file = $val['name'];
        $tooltip = $msg['cmdChangeDir'];
?>
        <a href="<? echo "$manager&fmChangeDir=" . urlencode($dir); ?>"
         title="<? echo $tooltip; ?>"
         onMouseOver="window.status='<? echo $tooltip; ?>'; return true"
         onMouseOut="window.status=''">
<?
      }
      else if($enableDownload) {
        $dir = $fmCurDir;
        $file = $val['name'];
        $tooltip = $msg['cmdGetFile'];
        $url = "$fmWebPath/get_file.php?file=" . urlencode("$dir/$file");
?>
        <a href="<? echo $url; ?>"
         title="<? echo $tooltip; ?>"
         onMouseOver="window.status='<? echo $tooltip; ?>'; return true"
         onMouseOut="window.status=''">
<?
      }
?>
      <img src="<? echo "$icons/" . $val['icon']; ?>.gif" border="0" width="12" height="10" alt="<? echo $tooltip; ?>"></a>
      </td>
      <td class="fmContent" align="left">
      <a href="javascript:fmFileInfo(<? echo "'$val[permissions]', '$val[owner]', '$val[group]', '$val[size]', '$val[changed]', '$val[name]', '$val[thumb]', '$val[width]', '$val[height]'"; ?>)" class="fmLink"
       title="<? echo $msg['cmdFileInfo']; ?>"
       onMouseOver="window.status='<? echo $msg['cmdFileInfo']; ?>'; return true"
       onMouseOut="window.status=''">
      <? echo $val['name']; ?></a>
      </td>
      <td class="fmContent" align="right">
<?
      $size = $val['size'] / 1024;
      if($size > 999) echo number_format($size / 1024, 1) . ' MB';
      else echo number_format($size, 1) . ' KB';
?>
      </td>
      <td class="fmContent" align="center"><? echo $val['changed']; ?></td>
      <td class="fmTD2" align="center" colspan="2" nowrap>
<?
      if($enableRename) {
?>
        <a href="javascript:fmOpenDialog('fmRename', '<? echo $msg['cmdRename'] . ": $file"; ?>', '<? echo $file; ?>')"
         title="<? echo $msg['cmdRename']; ?>"
         onMouseOver="window.status='<? echo $msg['cmdRename']; ?>'; return true"
         onMouseOut="window.status=''">
        <img src="<? echo $icons; ?>/rename.gif" border="0" width="10" height="10" alt="<? echo $msg['cmdRename']; ?>"></a>
<?
      }
      else {
        $error = $msg['cmdRename'] . ': ' . $msg['errDisabled'];
?>
        <a href="javascript:fmOpenDialog('fmError', '<? echo $error; ?>')" onMouseOver="window.status=''; return true">
        <img src="<? echo $icons; ?>/rename_x.gif" border="0" width="10" height="10"></a>
<?
      }

      if($enablePermissions) {
?>
        <a href="javascript:fmOpenDialog('fmPerm', '<? echo $msg['cmdChangePerm'] . ": $file"; ?>', '<? echo $file; ?>', '<? echo $val[permissions]; ?>')"
         title="<? echo $msg['cmdChangePerm']; ?>"
         onMouseOver="window.status='<? echo $msg['cmdChangePerm']; ?>'; return true"
         onMouseOut="window.status=''">
        <img src="<? echo $icons; ?>/permissions.gif" border="0" width="10" height="10" alt="<? echo $msg['cmdChangePerm']; ?>"></a>
<?
      }
      else {
        $error = $msg['cmdChangePerm'] . ': ' . $msg['errDisabled'];
?>
        <a href="javascript:fmOpenDialog('fmError', '<? echo $error; ?>')" onMouseOver="window.status=''; return true">
        <img src="<? echo $icons; ?>/permissions_x.gif" border="0" width="10" height="10"></a>
<?
      }

      if($enableDelete) {
        if($val['icon'] == 'dir') {
          $cmd = 'fmRemDir';
          $confirm = $msg['msgRemoveDir'];
        }
        else {
          $cmd = 'fmDelFile';
          $confirm = $msg['msgDeleteFile'];
        }
?>
        <a href="javascript:fmGoToOK('<? echo $file . ':\n' . $confirm; ?>', '<? echo "$manager&$cmd=" . urlencode($file); ?>')"
         title="<? echo $msg['cmdDelete']; ?>"
         onMouseOver="window.status='<? echo $msg['cmdDelete']; ?>'; return true"
         onMouseOut="window.status=''">
        <img src="<? echo $icons; ?>/delete.gif" border="0" width="10" height="10" alt="<? echo $msg['cmdDelete']; ?>"></a>
<?
      }
      else {
        $error = $msg['cmdDelete'] . ': ' . $msg['errDisabled'];
?>
        <a href="javascript:fmOpenDialog('fmError', '<? echo $error; ?>')" onMouseOver="window.status=''; return true">
        <img src="<? echo $icons; ?>/delete_x.gif" border="0" width="10" height="10"></a>
<?
      }

      if($val['icon'] == 'text') {

        if($enableEdit) {
?>
          <a href="<? echo "$manager&fmEdit=" . urlencode($file); ?>"
           title="<? echo $msg['cmdEdit']; ?>"
           onMouseOver="window.status='<? echo $msg['cmdEdit']; ?>'; return true"
           onMouseOut="window.status=''">
          <img src="<? echo $icons; ?>/edit.gif" border="0" width="10" height="10" alt="<? echo $msg['cmdEdit']; ?>"></a>
<?
        }
        else {
          $error = $msg['cmdEdit'] . ': ' . $msg['errDisabled'];
?>
          <a href="javascript:fmOpenDialog('fmError', '<? echo $error; ?>')" onMouseOver="window.status=''; return true">
          <img src="<? echo $icons; ?>/edit_x.gif" border="0" width="10" height="10"></a>
<?
        }
      }
      else echo ' <img src="' . $icons . '/blank.gif" width="10" height="10">';
?>
      </td>
      </tr>
<?
    }
  }
?>
</table>
</td>
</tr></table>
</td>
</tr></table>
