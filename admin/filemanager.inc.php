<?
/*
 +-------------------------------------------------------------------+
 |                     F I L E M A N A G E R   (v2.11)               |
 |                                                                   |
 | Copyright Gerd Tentler               www.gerd-tentler.de/tools    |
 | Created: Dec. 7, 2006                Last modified: Nov. 12, 2007 |
 +-------------------------------------------------------------------+
 | This program may be used and hosted free of charge by anyone for  |
 | personal purpose as long as this copyright notice remains intact. |
 |                                                                   |
 | Obtain permission before selling the code for this program or     |
 | hosting this software on a commercial website or redistributing   |
 | this software over the Internet or in any other medium. In all    |
 | cases copyright must remain intact.                               |
 +-------------------------------------------------------------------+
*/
  error_reporting(E_WARNING);

//========================================================================================================
// Set variables, if they are not registered globally; needs PHP 4.1.0 or higher
//========================================================================================================

  if(isset($_REQUEST['fmMode'])) $fmMode = $_REQUEST['fmMode'];
  if(isset($_REQUEST['fmObject'])) $fmObject = $_REQUEST['fmObject'];
  if(isset($_REQUEST['fmCurDir'])) $fmCurDir = $_REQUEST['fmCurDir'];
  if(isset($_REQUEST['fmSortField'])) $fmSortField = $_REQUEST['fmSortField'];
  if(isset($_REQUEST['fmSortOrder'])) $fmSortOrder = $_REQUEST['fmSortOrder'];
  if(isset($_REQUEST['fmChangeDir'])) $fmChangeDir = $_REQUEST['fmChangeDir'];
  if(isset($_REQUEST['fmEdit'])) $fmEdit = $_REQUEST['fmEdit'];
  if(isset($_REQUEST['fmDelFile'])) $fmDelFile = $_REQUEST['fmDelFile'];
  if(isset($_REQUEST['fmRemDir'])) $fmRemDir = $_REQUEST['fmRemDir'];
  if(isset($_REQUEST['fmSent'])) $fmSent = $_REQUEST['fmSent'];
  if(isset($_REQUEST['fmText'])) $fmText = $_REQUEST['fmText'];
  if(isset($_REQUEST['fmName'])) $fmName = $_REQUEST['fmName'];
  if(isset($_REQUEST['fmPerms'])) $fmPerms = $_REQUEST['fmPerms'];
  if(isset($_REQUEST['fmReplSpaces'])) $fmReplSpaces = $_REQUEST['fmReplSpaces'];
  if(isset($_REQUEST['fmLowerCase'])) $fmLowerCase = $_REQUEST['fmLowerCase'];

  if(isset($_FILES['fmFile'])) $fmFile = $_FILES['fmFile'];

  if(isset($_SERVER['PHP_SELF'])) $PHP_SELF = $_SERVER['PHP_SELF'];
  if(isset($_SERVER['HTTP_HOST'])) $HTTP_HOST = $_SERVER['HTTP_HOST'];

//========================================================================================================
// Includes
//========================================================================================================

 if($HTTP_HOST == 'localhost' || $HTTP_HOST == '127.0.0.1' || ereg('^192\.168\.0\.[0-9]+$', $HTTP_HOST)) {
    include('config_local.inc.php');
  }
  else {
    include('../config.php');
  }

  if(!isset($language)) $language = 'en';
  include("languages/lang_$language.inc");
  include('fmlib.inc.php');

//========================================================================================================
// Main
//========================================================================================================
?>
<script language="JavaScript" src="<? echo $fmWebPath; ?>/filemanager.js"></script>
<div id="fmListing" align="<? echo $fmAlign; ?>" style="margin:<? echo $fmMargin; ?>px">
<?
  if($ftp_server && $ftp_user) $ftp = fm_connect();
  else $ftp = false;

  $startDir = str_replace('\\', '/', ($ftp ? $startDir : realpath($startDir)));
  $fmCurDir = str_replace('\\', '/', ($ftp ? $fmCurDir : realpath($fmCurDir)));

  if($fmChangeDir) {
    $fmChangeDir = str_replace('/..', '', $fmChangeDir);
    $fmChangeDir = str_replace('../', '', $fmChangeDir);
    if($fmChangeDir == '..') $fmCurDir = ereg_replace('/[^/]+$', '', $fmCurDir);
    else $fmCurDir = (($fmCurDir == '/') ? '' : $fmCurDir) . "/$fmChangeDir";
  }
  if(substr($fmCurDir, 0, strlen($startDir)) != $startDir) $fmCurDir = $startDir;

  $manager = "$PHP_SELF?fmSortField=$fmSortField&fmSortOrder=$fmSortOrder&fmCurDir=" . urlencode($fmCurDir);

  if($fmEdit && $enableEdit) {
    include('edit.inc.php');
  }
  else {
    if($fmMode == 'rename' && $enableRename) {
      if($fmName && $fmObject) {
        if(!fm_rename("$fmCurDir/$fmObject", "$fmCurDir/$fmName")) {
          $fmError = $msg['errRename'] . ": $fmObject &raquo; $fmName";
        }
      }
    }
    else if($fmMode == 'permissions' && $enablePermissions) {
      if($fmPerms && $fmObject) {
        $mode = '0';
        for($i = $cnt = 0; $i < 3; $i++) {
          for($j = $sum = 0; $j < 3; $j++) $sum += $fmPerms[$cnt++];
          $mode .= $sum;
        }
        if(!fm_chmod("$fmCurDir/$fmObject", $mode)) $fmError = $msg['errPermChange'] . ": $fmObject";
      }
    }
    else if($fmMode == 'newDir' && $enableNewDir) {
      if($fmName) {
        if(!fm_mkdir("$fmCurDir/$fmName")) $fmError = $msg['errDirNew'] . ": $fmName";
      }
    }
    else if($fmMode == 'newFile' && $enableUpload) {
      if($fmFile && $fmFile['size']) {
        $newFile = $fmFile['name'];
        if($fmReplSpaces) $newFile = str_replace(' ', '_', $newFile);
        if($fmLowerCase) $newFile = strtolower($newFile);
        if(!fm_upload($fmFile['tmp_name'], "$fmCurDir/$newFile")) {
          $fmError = $msg['errSave'] . ": $newFile";
        }
      }
    }
    else if($fmDelFile && $enableDelete) {
      if(!fm_delete("$fmCurDir/$fmDelFile")) $fmError = $msg['errDelete'] . ": $fmDelFile";
    }
    else if($fmRemDir && $enableDelete) {
      if(!fm_rmdir("$fmCurDir/$fmRemDir")) $fmError = $msg['errDelete'] . ": $fmRemDir";
    }
    include('listing.inc.php');
  }

  if($ftp) {
    @ftp_quit($ftp);

    $tmp = str_replace('\\', '/', dirname(__FILE__)) . '/tmp';
    if($dp = @opendir($tmp)) {
      while($file = @readdir($dp)) {
        if($file != '.' && $file != '..') @unlink("$tmp/$file");
      }
      @closedir($tmp);
    }
  }
?>
</div>
<?
//========================================================================================================
// Dialog Boxes
//========================================================================================================
?>
<div id="fmInfo" class="fmDialog">
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td class="fmTH1" style="padding:4px" align="left"><? echo $msg['fileInfo']; ?></td>
<td class="fmTH1" style="padding:2px" align="right"><? echo fm_close_button(); ?></td>
</tr><tr>
<td class="fmTH1" colspan="2" style="padding:1px">
<div id="fmInfoText" class="fmTD2" style="padding:4px"></div></td>
</tr></table>
</div>

<div id="fmError" class="fmDialog">
<table border="0" cellspacing="0" cellpadding="0" width="400"><tr>
<td class="fmTH1" style="padding:4px" align="left"><? echo $msg['error']; ?></td>
<td class="fmTH1" style="padding:2px" align="right"><? echo fm_close_button(); ?></td>
</tr><tr>
<td class="fmTH3" colspan="2" style="padding:4px">
<div id="fmErrorText" class="fmError"></div></td>
</tr></table>
</div>

<div id="fmRename" class="fmDialog">
<form name="fmRename" class="fmForm" action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="fmMode" value="rename">
<input type="hidden" name="fmObject" value="">
<input type="hidden" name="fmCurDir" value="<? echo $fmCurDir; ?>">
<input type="hidden" name="fmSortField" value="<? echo $fmSortField; ?>">
<input type="hidden" name="fmSortOrder" value="<? echo $fmSortOrder; ?>">
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td id="fmRenameText" class="fmTH1" style="padding:4px" align="left" nowrap></td>
<td class="fmTH1" style="padding:2px" align="right"><? echo fm_close_button(); ?></td>
</tr><tr>
<td class="fmTH3" colspan="2" align="center" style="padding:4px">
<input type="text" name="fmName" size="40" maxlength="60" class="fmField" value=""><br>
<input type="submit" class="fmButton" value="<? echo $msg['cmdRename']; ?>">
</td>
</tr></table>
</form>
</div>

<div id="fmPerm" class="fmDialog">
<form name="fmPerm" class="fmForm" action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="fmMode" value="permissions">
<input type="hidden" name="fmObject" value="">
<input type="hidden" name="fmCurDir" value="<? echo $fmCurDir; ?>">
<input type="hidden" name="fmSortField" value="<? echo $fmSortField; ?>">
<input type="hidden" name="fmSortOrder" value="<? echo $fmSortOrder; ?>">
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td id="fmPermText" class="fmTH1" style="padding:4px" align="left" nowrap></td>
<td class="fmTH1" style="padding:2px" align="right"><? echo fm_close_button(); ?></td>
</tr><tr>
<td class="fmTH3" colspan="2" align="center" style="padding:4px">
<table border="0" cellspacing="2" cellpadding="4"><tr align="center">
<td class="fmTH2"><? echo $msg['owner']; ?></td>
<td class="fmTH2"><? echo $msg['group']; ?></td>
<td class="fmTH2"><? echo $msg['other']; ?></td>
</tr><tr align="left">
<?
  for($i = 0; $i < 9; $i += 3) {
?>
    <td class="fmTD2" nowrap>
    <input type="checkbox" name="fmPerms[<? echo $i; ?>]" value="4"> <? echo $msg['read']; ?><br>
    <input type="checkbox" name="fmPerms[<? echo $i+1; ?>]" value="2"> <? echo $msg['write']; ?><br>
    <input type="checkbox" name="fmPerms[<? echo $i+2; ?>]" value="1"> <? echo $msg['execute']; ?>
    </td>
<?
  }
?>
</tr></table>
<input type="submit" class="fmButton" value="<? echo $msg['cmdChangePerm']; ?>">
</td>
</tr></table>
</form>
</div>

<div id="fmNewFile" class="fmDialog">
<form name="fmNewFile" class="fmForm" action="<? echo $PHP_SELF; ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="fmMode" value="newFile">
<input type="hidden" name="fmCurDir" value="<? echo $fmCurDir; ?>">
<input type="hidden" name="fmSortField" value="<? echo $fmSortField; ?>">
<input type="hidden" name="fmSortOrder" value="<? echo $fmSortOrder; ?>">
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td id="fmNewFileText" class="fmTH1" style="padding:4px" align="left" nowrap></td>
<td class="fmTH1" style="padding:2px" align="right"><? echo fm_close_button(); ?></td>
</tr><tr>
<td class="fmTH3" colspan="2" align="center" style="padding:4px">
<input type="file" name="fmFile" size="20" class="fmField">
<div class="fmTH3" style="font-weight:normal; text-align:left; border:none">
<input type="checkbox" name="fmReplSpaces" value="1"<? if($replSpacesUpload) echo ' checked'; ?>>
file name =&gt; file_name<br>
<input type="checkbox" name="fmLowerCase" value="1"<? if($lowerCaseUpload) echo ' checked'; ?>>
FileName =&gt; filename
</div>
<input type="submit" class="fmButton" value="<? echo $msg['cmdUploadFile']; ?>">
</td>
</tr></table>
</form>
</div>

<div id="fmNewDir" class="fmDialog">
<form name="fmNewDir" class="fmForm" action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="fmMode" value="newDir">
<input type="hidden" name="fmCurDir" value="<? echo $fmCurDir; ?>">
<input type="hidden" name="fmSortField" value="<? echo $fmSortField; ?>">
<input type="hidden" name="fmSortOrder" value="<? echo $fmSortOrder; ?>">
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td id="fmNewDirText" class="fmTH1" style="padding:4px" align="left" nowrap></td>
<td class="fmTH1" style="padding:2px" align="right"><? echo fm_close_button(); ?></td>
</tr><tr>
<td class="fmTH3" colspan="2" align="center" style="padding:4px">
<input type="text" name="fmName" size="40" maxlength="60" class="fmField"><br>
<input type="submit" class="fmButton" value="<? echo $msg['cmdNewDir']; ?>">
</td>
</tr></table>
</form>
</div>
<?
//========================================================================================================

  if($fmError) {
?>
    <script language="JavaScript"> <!--
    setTimeout('fmViewError("<? echo $fmError; ?>")', 500);
    //--> </script>
<?
  }
?>
