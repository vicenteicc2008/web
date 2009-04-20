<?php
    /*
    RSDB - ReactOS Support Database
    Copyright (C) 2005-2006  Klemens Friedl <frik85@reactos.org>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
    */


class Item_Details extends HTML_Item
{

  protected function body()
  {
    global $RSDB_intern_link_item;
    global $RSDB_intern_link_vendor_sec;
    global $RSDB_intern_user_id;


  $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_item_comp WHERE comp_visible = '1' AND comp_id = :comp_id ORDER BY comp_name ASC");
  $stmt->bindParam('comp_id',@$_GET['item'],PDO::PARAM_STR);
  $stmt->execute();
	$result_page = $stmt->fetch(PDO::FETCH_ASSOC);
	
if ($result_page['comp_id']) {
	echo "<h2>".$result_page['comp_name'] ." [". "ReactOS ".@show_osversion($result_page['comp_osversion']) ."]</h2>"; 
	
  $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_groups WHERE grpentr_id = :group_id");
  $stmt->bindParam('group_id',$result_page['comp_groupid'],PDO::PARAM_STR);
  $stmt->execute();
	$result_entry_vendor2 = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_item_vendor WHERE vendor_id = :vendor_id");
  $stmt->bindParam('vendor_id',$result_entry_vendor2['grpentr_vendor'],PDO::PARAM_STR);
  $stmt->execute();
	$result_entry_vendor = $stmt->fetch(PDO::FETCH_ASSOC);
	
?>
	<table width="100%" border="0" cellpadding="1" cellspacing="5">
      <tr>
        <td width="40%" valign="top">
			<h3>Details</h3>
		  <p><span class="simple"><strong>Application</strong></span> </p>
			<ul class=simple>
              <li><strong>Name:</strong> <?php echo htmlentities($result_page['comp_name']); ?></li>
              <li><strong>Version:</strong> <?php echo htmlentities($result_page['comp_appversion']); ?></li>
              <li><strong>Company:</strong> <?php echo '<a href="'.$RSDB_intern_link_vendor_sec.$result_entry_vendor['vendor_id'].'">'.htmlentities($result_entry_vendor['vendor_name']).'</a>'; ?></li>
              <li><strong>Description:</strong> <?php echo wordwrap(nl2br(htmlentities($result_page['comp_description'], ENT_QUOTES))); ?></li>
		  </ul>
			<span class="simple"><strong>ReactOS</strong></span>
            <ul class=simple>
              <li><strong>Version:</strong> <?php echo "ReactOS ". @show_osversion($result_page['comp_osversion']); ?></li>
			  <li><strong>Other tested versions:</strong><ul class=simple>
			  <?php
		
      $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_item_comp WHERE comp_name = :name AND comp_visible = '1' AND comp_groupid = :group_id ORDER BY comp_osversion DESC");
      $stmt->bindParam('name',$result_page['comp_name'],PDO::PARAM_STR);
      $stmt->bindParam('group_id',$result_page['comp_groupid'],PDO::PARAM_STR);
      $stmt->execute();
			while($result_entry_osver = $stmt->fetch(PDO::FETCH_ASSOC)) {
				if ($result_entry_osver['comp_osversion'] != $result_page['comp_osversion']) {
					echo "<li><a href=\"".$RSDB_intern_link_item.$result_entry_osver['comp_id']."\">"."ReactOS ". @show_osversion($result_entry_osver['comp_osversion'])."</a></li>";
				}
			}
		
		?>			  </ul></li>
			 
            </ul>
            <span class="simple"><strong>Compatibility</strong></span>
            <ul class=simple>
              <li><strong>Award:</strong> <img src="media/icons/awards/<?php echo Award::icon($result_page['comp_award']); ?>_32.gif" alt="<?php echo Award::name($result_page['comp_award']); ?>" width="32" height="32" />
			  <?php echo Award::name($result_page['comp_award']); ?></li>
              <li><strong>Function:</strong> <?php
			
			$counter_stars_install = 0;
			$counter_stars_function = 0;
			$counter_stars_user = 0;

      $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_item_comp_testresults WHERE test_visible = '1' AND test_comp_id = :comp_id ORDER BY test_comp_id ASC");
      $stmt->bindParam('comp_id',@$_GET['item'],PDO::PARAM_STR);
      $stmt->execute();

			while($result_count_stars = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$counter_stars_install += $result_count_stars['test_result_install'];
				$counter_stars_function += $result_count_stars['test_result_function'];
				$counter_stars_user++;
			}
			
			echo Star::drawNormal($counter_stars_function, $counter_stars_user, 5, "tests");

			
			?></li>
              <li><strong>Install:</strong> <?php
			
			echo Star::drawNormal($counter_stars_install, $counter_stars_user, 5, "tests");
			
			?></li>
            </ul>
            <span class="simple"><strong>Further Information</strong></span>
            <ul class=simple>
<?php
          $stmt=CDBConnection::getInstance()->prepare("SELECT COUNT(*) FROM rsdb_item_comp_testresults WHERE test_comp_id = :comp_id AND test_visible = '1'");
          $stmt->bindParam('comp_id',$result_page['comp_id'],PDO::PARAM_STR);
          $stmt->execute();
					$result_count_testentries = $stmt->fetch();
					
					echo '<b><li><a href="'. $RSDB_intern_link_item.$result_page['comp_id'] .'&amp;item2=tests">Compatibility Tests</b>';
					
					if ($result_count_testentries[0] > 0) {
						echo " (". $result_count_testentries[0] .")</a></li>";
					}
					else {
						echo "</a></li>";
					}
?>
<?php
          $stmt=CDBConnection::getInstance()->prepare("SELECT COUNT(*) FROM ".CDBT_COMMENTS." WHERE entry_id = :comp_id AND visible IS TRUE");
          $stmt->bindParam('comp_id',$result_page['comp_id'],PDO::PARAM_STR);
          $stmt->execute();
					$result_count_forumentries = $stmt->fetch();
					
					if ($result_count_forumentries[0] > 0) {
						echo "<b>";
					}
			  		
					echo '<li><a href="'. $RSDB_intern_link_item.$result_page['comp_id'] .'&amp;item2=forum">Forum';
					
					if ($result_count_forumentries[0] > 0) {
						echo "</b> (". $result_count_forumentries[0] .")</a></li>";
					}
					else {
						echo "</a></li>";
					}
?>
<?php
          $stmt=CDBConnection::getInstance()->prepare("SELECT COUNT(*) FROM ".CDBT_ATTACHMENTS." WHERE entry_id = :group_id AND visible IS TRUE");
          $stmt->bindParam('group_id',$result_page['comp_media'],PDO::PARAM_STR);
          $stmt->execute();
					$result_count_screenshots = $stmt->fetch();
					
					if ($result_count_screenshots[0] > 0) {
						echo "<b>";
					}
			  		
					echo '<li><a href="'. $RSDB_intern_link_item.$result_page['comp_id'] .'&amp;item2=screens">Screenshots';
					
					if ($result_count_screenshots[0] > 0) {
						echo "</b> (". $result_count_screenshots[0] .")</a></li>";
					}
					else {
						echo "</a></li>";
					}
?>
			  <li><a href="<?php echo "http://www.reactos.org/bugzilla/buglist.cgi?bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&field0-0-0=product&type0-0-0=substring&value0-0-0=".$result_page['comp_name']."&field0-0-1=component&type0-0-1=substring&value0-0-1=".$result_page['comp_name']."&field0-0-2=short_desc&type0-0-2=substring&value0-0-2=".$result_page['comp_name']."&field0-0-3=status_whiteboard&type0-0-3=substring&value0-0-3=".$result_page['comp_name']; ?>" target="_blank">Bugs</a></li>
          </ul>
		</td>
        <td width="10%" align="center" valign="top"></td>
        <td width="40%" valign="top">
        <h3 align="right">Screenshot</h3>
        <p align="center"><?php
		
      $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM ".CDBT_ATTACHMENTS." WHERE entry_id = :group_id LIMIT 1");
      $stmt->bindParam('group_id',$result_page['comp_media'],PDO::PARAM_STR);
      $stmt->execute();
			$result_screenshots= $stmt->fetch(PDO::FETCH_ASSOC);
	
				echo '<a href="'.$RSDB_intern_link_item.$result_page['comp_id'].'&amp;item2=screens"><img src="media/files/picture/'.urlencode($result_screenshots['file']).'" width="250" height="188" border="0" alt="'.htmlentities($result_screenshots['description']).'" /></a>';
		
		?></p>
		<p>&nbsp;</p>
		<?php
			if ($result_page['comp_infotext']) {
		?>
				<h4 align="left">Information:</h4>
				<p align="left"><font face="Arial, Helvetica, sans-serif" size="2"><?php echo wordwrap(nl2br(htmlentities($result_page['comp_infotext'], ENT_QUOTES))); ?></font></p></td>
     	<?php
			}
		?>
	  </tr>
    </table>
<?php

	if (CUser::isModerator($RSDB_intern_user_id)) {
    $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_item_comp WHERE comp_visible = '1' AND comp_id = :comp_id LIMIT 1");
    $stmt->bindParam('comp_id',@$_GET['item'],PDO::PARAM_STR);
    $stmt->execute();
		$result_maintainer_item = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_groups WHERE grpentr_visible = '1' AND grpentr_id = :group_id AND grpentr_comp = '1' LIMIT 1");
    $stmt->bindParam('group_id',$result_maintainer_item['comp_groupid'],PDO::PARAM_STR);
    $stmt->execute();
		$result_maintainer_group = $stmt->fetch(PDO::FETCH_ASSOC);



		$RSDB_referrer="";
		$RSDB_usragent="";
		$RSDB_ipaddr="";
		if (array_key_exists('HTTP_REFERER', $_SERVER)) $RSDB_referrer=htmlspecialchars($_SERVER['HTTP_REFERER']);
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) $RSDB_usragent=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
		if (array_key_exists('REMOTE_ADDR', $_SERVER)) $RSDB_ipaddr=htmlspecialchars($_SERVER['REMOTE_ADDR']);

		$RSDB_TEMP_pmod = "";
		$RSDB_TEMP_txtreq1 = "";
		$RSDB_TEMP_txtreq2 = "";
		$RSDB_TEMP_txtspam = "";
		$RSDB_TEMP_verified = "";
		$RSDB_TEMP_appn = "";
		$RSDB_TEMP_apppr = "";
		$RSDB_TEMP_appit = "";
		$RSDB_TEMP_appdesc = "";
		$RSDB_TEMP_version = "";
		$RSDB_TEMP_appinfo = "";
		if (array_key_exists("pmod", $_POST)) $RSDB_TEMP_pmod=htmlspecialchars($_POST["pmod"]);
		if (array_key_exists("txtreq1", $_POST)) $RSDB_TEMP_txtreq1=htmlspecialchars($_POST["txtreq1"]);
		if (array_key_exists("txtreq2", $_POST)) $RSDB_TEMP_txtreq2=htmlspecialchars($_POST["txtreq2"]);
		if (array_key_exists("txtspam", $_POST)) $RSDB_TEMP_txtspam=htmlspecialchars($_POST["txtspam"]);
		if (array_key_exists("verified", $_POST)) $RSDB_TEMP_verified=htmlspecialchars($_POST["verified"]);
		if (array_key_exists("appn", $_POST)) $RSDB_TEMP_appn=htmlspecialchars($_POST["appn"]);
		if (array_key_exists("apppr", $_POST)) $RSDB_TEMP_apppr=htmlspecialchars($_POST["apppr"]);
		if (array_key_exists("appit", $_POST)) $RSDB_TEMP_appit=htmlspecialchars($_POST["appit"]);
		if (array_key_exists("appdesc", $_POST)) $RSDB_TEMP_appdesc=htmlspecialchars($_POST["appdesc"]);
		if (array_key_exists("version", $_POST)) $RSDB_TEMP_version=htmlspecialchars($_POST["version"]);
		if (array_key_exists("appinfo", $_POST)) $RSDB_TEMP_appinfo=htmlspecialchars($_POST["appinfo"]);


		// Edit application group data:
		if ($RSDB_TEMP_pmod == "ok" && @$_GET['item'] != "" && $RSDB_TEMP_appn != "" && $RSDB_TEMP_apppr != "" && $RSDB_TEMP_appit != "" && $RSDB_TEMP_version != "" && CUser::isModerator($RSDB_intern_user_id)) {

      $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_groups WHERE grpentr_visible = '1' AND grpentr_id = :group_id AND grpentr_comp = '1' LIMIT 1");
      $stmt->bindParam('group_id',$RSDB_TEMP_appn,PDO::PARAM_STR);
      $stmt->execute();
			$result_maintainer_group2 = $stmt->fetch(PDO::FETCH_ASSOC);

			// Update item entry:
      $stmt=CDBConnection::getInstance()->prepare("UPDATE rsdb_item_comp SET comp_name = :new_name, comp_appversion = :new_appversion, comp_groupid = :new_groupid, comp_description = :new_description, comp_infotext = :new_infotext, comp_osversion = :new_osversion WHERE comp_id = :comp_id");
      $stmt->bindValue('new_name',$result_maintainer_group2['grpentr_name']." ".$RSDB_TEMP_apppr,PDO::PARAM_STR);
      $stmt->bindParam('new_appversion',$RSDB_TEMP_appit,PDO::PARAM_STR);
      $stmt->bindParam('new_groupid',$RSDB_TEMP_appn,PDO::PARAM_STR);
      $stmt->bindParam('new_description',$RSDB_TEMP_appdesc,PDO::PARAM_STR);
      $stmt->bindParam('new_infotext',$RSDB_TEMP_appinfo,PDO::PARAM_STR);
      $stmt->bindParam('new_osversion',$RSDB_TEMP_version,PDO::PARAM_STR);
      $stmt->bindParam('comp_id',@$_GET['item'],PDO::PARAM_STR);
      $stmt->execute();
			
			CLog::add("low", "comp_item", "edit", "[App Item] Edit entry", @usrfunc_GetUsername($RSDB_intern_user_id)." changed the group data from: \n\nAppName: ".htmlentities($result_maintainer_item['comp_name'])." - ".$result_maintainer_item['comp_id']."\n\nDesc: ".htmlentities($result_maintainer_item['comp_description'])." \n\GroupID: ".$result_maintainer_item['comp_groupid']." \n\ReactOS version: ".$result_maintainer_item['comp_osversion']." \n\n\nTo: \n\nAppName: ".htmlentities($result_maintainer_group['grpentr_name']." ".$RSDB_TEMP_apppr)." - ".htmlentities($RSDB_TEMP_appn)."\n\nInternVersion: ".htmlentities($RSDB_TEMP_appit)." \n\nDesc: ".htmlentities($RSDB_TEMP_appdesc)." \n\nReactOS version: ".htmlentities($RSDB_TEMP_version), "0");
			?>
			<script language="JavaScript">
				window.setTimeout('window.location.href="<?php echo $RSDB_intern_link_item_item2_both_javascript; ?>"','500')
			</script>
			<?php
		}

		// Special request:
		if ($RSDB_TEMP_pmod == "ok" && $RSDB_TEMP_txtreq1 != "" && $RSDB_TEMP_txtreq2 != "" && CUser::isModerator($RSDB_intern_user_id)) {
      CLog::add(null, null, null, $RSDB_TEMP_txtreq1, $RSDB_TEMP_txtreq2,'');

		}
		// Report spam:
		if ($RSDB_TEMP_pmod == "ok" && $RSDB_TEMP_txtspam != "" && CUser::isModerator($RSDB_intern_user_id)) {
			$stmt=CDBConnection::getInstance()->prepare("UPDATE rsdb_item_comp SET comp_visible = '3' WHERE comp_id = :comp_id");
      $stmt->bindParam('comp_id',@$_GET['item'],PDO::PARAM_STR);
      $stmt->execute();
			CLog::add("low", "comp_item", "report_spam", "[App Item] Spam/ads report", @usrfunc_GetUsername($RSDB_intern_user_id)." wrote: \n".htmlentities($RSDB_TEMP_txtspam)." \n\n\n\nUser: ".@usrfunc_GetUsername($result_maintainer_item['comp_usrid'])." - ".$result_maintainer_item['comp_usrid']."\n\nAppName: ".htmlentities($result_maintainer_item['comp_name'])." - ".$result_maintainer_item['comp_id']."\n\nDesc: ".htmlentities($result_maintainer_item['comp_description'])." \n\GroupID: ".$result_maintainer_item['comp_groupid']." \n\ReactOS version: ".$result_maintainer_item['comp_osversion'], $result_maintainer_item['comp_usrid']);
		
		}
		// Verified:
		if ($result_maintainer_item['comp_checked'] == "no") {
			$temp_verified = "1";
		}
		else if ($result_maintainer_item['comp_checked'] == "1") {
			$temp_verified = "yes";
		}
		if ($result_maintainer_item['comp_checked'] == "1" || $result_maintainer_item['comp_checked'] == "no") {
			if ($RSDB_TEMP_pmod == "ok" && $RSDB_TEMP_verified == "done" && CUser::isModerator($RSDB_intern_user_id)) {
				echo "!";
        $stmt=CDBConnection::getInstance()->prepare("UPDATE rsdb_item_comp SET comp_checked = :checked WHERE comp_id = :comp_id ");
        $stmt->bindParam('checked',$temp_verified,PDO::PARAM_STR);
        $stmt->bindParam('comp_id',@$_GET['item'],PDO::PARAM_STR);
        $stmt->execute();
				CLog::add("low", "comp_item", "verified", "[App Item] Verified", @usrfunc_GetUsername($RSDB_intern_user_id)." has verified the following app version: \n\n\n\nUser: ".@usrfunc_GetUsername($result_maintainer_item['comp_usrid'])." - ".$result_maintainer_item['comp_usrid']."\n\nAppName: ".htmlentities($result_maintainer_item['comp_name'])." - ".$result_maintainer_item['comp_id']."\n\nDesc: ".htmlentities($result_maintainer_item['comp_description'])." \n\GroupID: ".$result_maintainer_item['comp_groupid']." \n\ReactOS version: ".$result_maintainer_item['comp_osversion'], "0");
			}
		}
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintainer">
	  <tbody>
		<tr>
		  <td><p><b><a name="maintainerbar"></a>Maintainer: </b>
			  <?php if ($result_maintainer_item['comp_checked'] != "yes") { ?><a href="javascript:Show_verify()">Verify entry</a> | <?php  } ?><a href="javascript:Show_groupentry()">Edit application versions data</a> | <a href="javascript:Show_spam()">Report spam/ads</a> | <a href="javascript:Show_requests()">Special requests</a></p>
		    <div id="groupentry" style="display: block">
			<fieldset>
			<legend>Edit application versions data</legend>
				<div align="left">
				  <form name="form1" method="post" action="<?php echo $RSDB_intern_link_item_item2_both."#maintainerbar"; ?>">
				      <p><font size="2">Application name: 
				        <select name="appn" id="appn">
                          <?php
          $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_groups WHERE grpentr_visible = '1' ORDER BY grpentr_name ASC");
					while($result_appn = $stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<option value="'. $result_appn['grpentr_id'] .'"';
						if  ($result_maintainer_item['comp_groupid'] == $result_appn['grpentr_id']) {
							echo ' selected';
						}
						echo '>'. $result_appn['grpentr_name'] .'</option>';
					}
				?>
						</select>
				        <font size="1">			          [<?php echo htmlentities($result_maintainer_group['grpentr_name']); ?>] (this will move the entry to another application group!) </font></font></p>
				      <p><font size="2">Application PR name: 
                      <input name="apppr" type="text" id="apppr" value="<?php echo htmlentities(substr($result_maintainer_item['comp_name'], strlen($result_maintainer_group['grpentr_name'])+1 )); ?>" size="30" maxlength="100">
		              (max. 100 chars) <br>
		              <br>
		              Application intern version:
                      <input name="appit" type="text" id="appit" value="<?php echo $result_maintainer_item['comp_appversion']; ?>" size="10" maxlength="15">
		              (number) </font></p>
				      <p><font size="2">Application description:
                          <input name="appdesc" type="text" id="appdesc" value="<?php echo htmlentities($result_maintainer_item['comp_description']); ?>" size="50" maxlength="255">
					(max. 255 chars) </font></p>
				      <p><font size="2">Additional information: <br>
                          <textarea name="appinfo" cols="70" rows="10" id="appinfo"><?php echo htmlentities($result_maintainer_item['comp_infotext']); ?></textarea>
                      <br>
	                    <br>
                      ReactOS version:
                      <select name="version" id="version">
				  <?php
          $stmt=CDBConnection::getInstance()->prepare("SELECT * FROM rsdb_object_osversions WHERE ver_visible = '1' ORDER BY ver_value DESC");
          while($result_osvers = $stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<option value="'. $result_osvers['ver_value'] .'"';
						if  ($result_maintainer_item['comp_osversion'] == $result_osvers['ver_value']) {
							echo ' selected';
						}
						echo '>'. $result_osvers['ver_name'] .'</option>';
					}
				?>
				            </select> 
	                  <font size="1">						[<?php echo "ReactOS ". @show_osversion($result_maintainer_item['comp_osversion']); ?>]</font>
			            <input name="pmod" type="hidden" id="pmod" value="ok">
			            <br>
                      <br>
	                    <br>
	                    <input type="submit" name="Submit" value="Save">	
		                        </font>				  
				                    </p>
				  </form>
				</div>
			</fieldset>
		</div>
		<div id="verify" style="display: block">
			<fieldset><legend>Verify entry</legend>
				<div align="left">
				  <p><font size="2">User &quot;<?php echo @usrfunc_GetUsername($result_maintainer_item['comp_usrid']); ?>&quot; has submitted this application group on &quot;<?php echo $result_maintainer_item['comp_date']; ?>&quot;. </font></p>
				  <p><font size="2"><strong>Application  name:</strong> <?php echo htmlentities($result_maintainer_group['grpentr_name']); ?><br>
		          <br>
			        <strong>Application PR name:</strong>			      <?php if ($result_maintainer_item['comp_name']) { echo htmlentities($result_maintainer_item['comp_name']); } else { echo '""'; } ?>
			      <br>
		          <br>
			        <strong>Application intern version:</strong>			      <?php 
					
						echo htmlentities($result_maintainer_item['comp_appversion']);
					
					 ?>
		          <br>
		          <br>
			        <strong>Application description:</strong>			      <?php 
					
						echo htmlentities($result_maintainer_item['comp_description']);
					
					 ?>
</font></p>
				  <p><font size="2"><strong>ReactOS version:</strong>
                  <?php echo "ReactOS ". @show_osversion($result_maintainer_item['comp_osversion']); ?>                 </font></p>
				  <p><font size="2">			        Please verify the data and choose one of the three available options below:</font></p>
				  <form name="form2" method="post" action="<?php echo $RSDB_intern_link_item_item2_both."#maintainerbar"; ?>">
				  <ul>
				    <li><font size="2"><a href="javascript:Show_spam()"><strong>Report spam/ads</strong></a></font></li>
				  </ul>
				  <ul>
				    <li><font size="2"><a href="javascript:Show_groupentry()"><strong>Correct/edit data</strong></a></font></li>
				  </ul>
				  <ul>
			        <li>
			            <font size="2">
			            <input type="submit" name="Submit2" value="I have verified the data and everything is okay!">
						<input name="pmod" type="hidden" id="pmod" value="ok">
                        <input name="verified" type="hidden" id="verified" value="done">
						</font> </li>
				  </ul>
	              </form> 
				</div>
			</fieldset>
		</div>
		<div id="spam" style="display: block">
			<fieldset>
			<legend>Report spam/ads</legend>
				<div align="left">
				  <form name="form4" method="post" action="<?php echo $RSDB_intern_link_item_item2_both."#maintainerbar"; ?>">
				    <p><font size="2">Please write a useful description:<br> 
			          <textarea name="txtspam" cols="70" rows="5" id="txtspam"></textarea>
</font><font size="2" face="Arial, Helvetica, sans-serif">
<input name="pmod" type="hidden" id="pmod" value="ok">
</font><font size="2">                    </font></p>
				    <p><font size="2"><strong>Note:</strong><br>
			        When you click on the submit button, the application group will get immediately invisible, and the user who submitted this entry a bad mark. If a user has some bad marks, he will not be able to submit anything for a certain periode.<br>
			        Only administrators can revert this task, so if you made a mistake use the <a href="javascript:Show_requests()">Special requests</a> function.</font></p>
				    <p>
				      <input type="submit" name="Submit4" value="Submit">
	                </p>
				  </form>
				</div>
			</fieldset>
		</div>
		<div id="addbundle" style="display: block">
			<fieldset><legend>Add to bundle</legend>
				<div align="left">
				  <p><font size="2">This interface is currently not available!</font></p>
				  <p><font size="2">Ask a admin to do that task for the meanwhile: <a href="javascript:Show_requests()">Special requests</a></font></p>
				</div>
			</fieldset>
		</div>
		<div id="requests" style="display: block">
			<fieldset><legend>Special requests</legend>
				<div align="left">
				  <form name="form4" method="post" action="<?php echo $RSDB_intern_link_item_item2_both."#maintainerbar"; ?>">
				    <p><font size="2">Message title:<br> 
		            <input name="txtreq1" type="text" id="txtreq1" size="40" maxlength="100">
				    </font></p>
				    <p><font size="2">Text:<br> 
		              <textarea name="txtreq2" cols="70" rows="5" id="txtreq2"></textarea>
</font><font size="2" face="Arial, Helvetica, sans-serif">
<input name="pmod" type="hidden" id="pmod" value="ok">
</font><font size="2">                    </font></p>
				    <p><font size="2"><strong>Note:</strong><br>
			        Please do NOT misuse this function. All administrators will be able to see your message and one of them may contact you per forum private message, email or just do the task you suggested/requested.</font></p>
				    <p><font size="2">If you want to ask something, or the task needs (in all the circumstances) a feedback,  use the website forum, the #reactos-web IRC channel, the mailing list or the forum private message system instead. </font></p>
				    <p><font size="2">This form is not a bug tracking tool nor a feature request function! Use <a href="http://www.reactos.org/bugzilla/">bugzilla</a> for such things instead!</font></p>
				    <p><font size="2"><strong>A sample usage for this form:</strong><br>
			        If you need a new category which doesn't exist, then write a request and one of the admins will read it and may add the missing category. Then you will be able to move this application group to the right category (if you have placed the application somewhere else temporary).</font></p>
				    <p>
				      <font size="2">
				      <input type="submit" name="Submit4" value="Submit">
                      </font> </p>
				  </form>
				</div>
			</fieldset>
		</div>
		  </td>
		</tr>
	  </tbody>
	</table>
	<script language="JavaScript1.2">

		document.getElementById('groupentry').style.display = 'none';
		document.getElementById('verify').style.display = 'none';
		document.getElementById('spam').style.display = 'none';
		document.getElementById('addbundle').style.display = 'none';
		document.getElementById('requests').style.display = 'none';
	
		function Show_groupentry()
		{
			document.getElementById('groupentry').style.display = (document.getElementById('groupentry').style.display == 'none') ? 'block' : 'none';
			document.getElementById('verify').style.display = 'none';
			document.getElementById('spam').style.display = 'none';
			document.getElementById('addbundle').style.display = 'none';
			document.getElementById('requests').style.display = 'none';
		}
		
		function Show_verify()
		{
			document.getElementById('groupentry').style.display = 'none';
			document.getElementById('verify').style.display = (document.getElementById('verify').style.display == 'none') ? 'block' : 'none';
			document.getElementById('spam').style.display = 'none';
			document.getElementById('addbundle').style.display = 'none';
			document.getElementById('requests').style.display = 'none';
		}

		function Show_spam()
		{
			document.getElementById('groupentry').style.display = 'none';
			document.getElementById('verify').style.display = 'none';
			document.getElementById('spam').style.display = (document.getElementById('spam').style.display == 'none') ? 'block' : 'none';
			document.getElementById('addbundle').style.display = 'none';
			document.getElementById('requests').style.display = 'none';
		}
		
		function Show_addbundle()
		{
			document.getElementById('groupentry').style.display = 'none';
			document.getElementById('verify').style.display = 'none';
			document.getElementById('spam').style.display = 'none';
			document.getElementById('addbundle').style.display = (document.getElementById('addbundle').style.display == 'none') ? 'block' : 'none';
			document.getElementById('requests').style.display = 'none';
		}


		function Show_requests()
		{
			document.getElementById('groupentry').style.display = 'none';
			document.getElementById('verify').style.display = 'none';
			document.getElementById('spam').style.display = 'none';
			document.getElementById('addbundle').style.display = 'none';
			document.getElementById('requests').style.display = (document.getElementById('requests').style.display == 'none') ? 'block' : 'none';
		}

	</script>
<?php
	}
?>

<br />

<?php
	if (CUser::isAdmin($RSDB_intern_user_id)) {
	
		$RSDB_TEMP_padmin = "";
		$RSDB_TEMP_done = "";
		$RSDB_TEMP_medal = "";
		if (array_key_exists("padmin", $_POST)) $RSDB_TEMP_padmin=htmlspecialchars($_POST["padmin"]);
		if (array_key_exists("done", $_POST)) $RSDB_TEMP_done=htmlspecialchars($_POST["done"]);
		if (array_key_exists("medal", $_POST)) $RSDB_TEMP_medal=htmlspecialchars($_POST["medal"]);

		
		if ($RSDB_TEMP_padmin == "ok" && $RSDB_TEMP_medal != "" && isset($_GET['item']) && $_GET['item'] != "" && CUser::isAdmin($RSDB_intern_user_id)) {
      $stmt=CDBConnection::getInstance()->prepare("UPDATE rsdb_item_comp SET comp_award = :award WHERE comp_id = :comp_id");
      $stmt->bindParam('award',$RSDB_TEMP_medal,PDO::PARAM_STR);
      $stmt->bindParam('comp_id',$_GET['item'],PDO::PARAM_STR);
      $stmt->execute();
			CLog::add("medium", "comp_item", "change award", "[App Item] Change Award", @usrfunc_GetUsername($RSDB_intern_user_id)." (".$RSDB_intern_user_id.") has changed the award symbol from: ".$result_maintainer_item['comp_award']." to ".$RSDB_TEMP_medal, "0");
			?>
			<script language="JavaScript">
				window.setTimeout('window.location.href="<?php echo $RSDB_intern_link_item_item2_both; ?>"','500')
			</script>
			<?php
		}
		
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin">
	  <tr>
		<td><b><a name="adminbar"></a>Admin: </b> <a href="javascript:Show_medal()">Change award symbol</a> | <a href="javascript:Show_readrequests()">Read special requests</a> | <font size="1">all other functions are under construction ...
        </font>
		<div id="readrequests" style="display: block">
			<fieldset><legend>Read special requests</legend>

 <table width="100%" border="1">  
    <tr><td width="10%"><div align="center"><font color="#000000"><strong><font size="2" face="Arial, Helvetica, sans-serif">Date</font></strong></font></div></td> 
    <td width="10%"><div align="center"><font color="#000000"><strong><font size="2" face="Arial, Helvetica, sans-serif">User</font></strong></font></div></td> 
    <td width="25%"><div align="center"><font color="#000000"><strong><font size="2" face="Arial, Helvetica, sans-serif">Title</font></strong></font></div></td> 
    <td width="45%"><div align="center"><font color="#000000"><strong><font size="2" face="Arial, Helvetica, sans-serif">Request</font></strong></font></div></td> 
    <td width="10%"><div align="center"><font color="#000000"><strong><font size="2" face="Arial, Helvetica, sans-serif">Done?</font></strong></font></div></td>
    </tr>
</table>

			</fieldset>
		</div>
		
		<div id="medal" style="display: block">
			<fieldset><legend>Change award symbol</legend>
				<div align="left">
				  <p><font size="2">Please read the <a href="<?php echo $RSDB_intern_link_db_sec; ?>help#sym" target="_blank">FAQ &amp; Help page</a> about the award/medal symbols before you change something!</font></p>
				  <p><font size="2">Please only change the award symbol if you have tested the application yourself. Do NOT forget to submit a compatibility test report (so that at least one test report exist) before you change the award symbol! </font></p>
				  <form name="form3" method="post" action="<?php echo $RSDB_intern_link_item_item2_both."#maintainerbar"; ?>">
				    <p>
				      <font size="2">
				      <select name="medal" id="medal">
				          <option value="10" <?php if ($result_maintainer_item['comp_award'] == "10") { echo "selected"; } ?>>Platinum</option>
				          <option value="9" <?php if ($result_maintainer_item['comp_award'] == "9") { echo "selected"; } ?>>Gold</option>
				          <option value="8" <?php if ($result_maintainer_item['comp_award'] == "8") { echo "selected"; } ?>>Silver</option>
				          <option value="7" <?php if ($result_maintainer_item['comp_award'] == "7") { echo "selected"; } ?>>Bronze</option>
				          <option value="5" <?php if ($result_maintainer_item['comp_award'] == "5") { echo "selected"; } ?>>Honorable Mention</option>
				          <option value="0" <?php if ($result_maintainer_item['comp_award'] == "0") { echo "selected"; } ?>>Untested</option>
				          <option value="2" <?php if ($result_maintainer_item['comp_award'] == "2") { echo "selected"; } ?>>Known not to work</option>
	                  </select>
                      <input name="padmin" type="hidden" id="padmin" value="ok">
			</font> </p>
				    <p>
				      <input type="submit" name="Submit3" value="Save">
	                </p>
				  </form>
				</div>
			</fieldset>
		</div>
		
				</td>
	  </tr>
	</table>
	<script language="JavaScript1.2">

		document.getElementById('readrequests').style.display = 'none';
		document.getElementById('medal').style.display = 'none';
	
		function Show_readrequests()
		{
			document.getElementById('readrequests').style.display = (document.getElementById('readrequests').style.display == 'none') ? 'block' : 'none';
			document.getElementById('medal').style.display = 'none';
		}
		function Show_medal()
		{
			document.getElementById('readrequests').style.display = 'none';
			document.getElementById('medal').style.display = (document.getElementById('medal').style.display == 'none') ? 'block' : 'none';
		}

	</script>
<?php
	}
}
  } // end of member function body
}
?>
