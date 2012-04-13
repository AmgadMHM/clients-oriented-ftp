<?php
	$tablesorter = 1;
	$allowed_levels = array(9);
	require_once('sys.includes.php');
	$page_title = __('Users administration','cftp_admin');;
	include('header.php');
?>

<script type="text/javascript">
	$(document).ready( function() {
		$("#users_tbl").tablesorter( {
			sortList: [[1,0]], widgets: ['zebra'], headers: {
				0: { sorter: false }, 
				6: { sorter: false }
			}
		})
		.tablesorterPager({container: $("#pager")})

		$("#select_all").click(function(){
			var status = $(this).attr("checked");
			$("td>input:checkbox").attr("checked",status);
		});
		
		$("form").submit(function() {
			var checks = $("td>input:checkbox").serializeArray(); 
			if (checks.length == 0) { 
				alert('<?php _e('Please select at least one user to proceed.','cftp_admin'); ?>');
				return false; 
			}
			else {
				var msg_1 = '<?php _e("You are about to delete",'cftp_admin'); ?>';
				var msg_2 = '<?php _e("users. Are you sure you want to continue?",'cftp_admin'); ?>';
				if (confirm(msg_1+' '+checks.length+' '+msg_2)) {
					return true;
				} else {
					return false;
				}
			}
		});

	});
</script>

<div id="main">
	<h2><?php echo $page_title; ?></h2>

<?php

	// Mass delete
	if(isset($_POST['btn_delete_users'])) {
		if(!empty($_POST['delete'])) {
			$selected_users = $_POST['delete'];
			foreach ($selected_users as $user) {
				$this_user = new UserActions();
				$delete_user = $this_user->delete_user($user);
			}
			
			$msg = __('The selected users were deleted.','cftp_admin');
			echo system_message('ok',$msg);
		}
		else {
			$msg = __('Please select at least one user to delete.','cftp_admin');
			echo system_message('error',$msg);
		}
	}

	$database->MySQLDB();
	$sql = $database->query("SELECT * FROM tbl_users");
	$count = mysql_num_rows($sql);
?>

	<form action="users.php" name="users_list" method="post">
		<div class="form_actions">
			<div class="form_actions_count">
				<p><?php _e('Users','cftp_admin'); ?>: <span><?php echo $count; ?></span></p>
			</div>
			<div class="form_actions_submit">
				<label><?php _e('Selected users actions','cftp_admin'); ?>:</label>
				<input type="submit" name="btn_delete_users" id="btn_delete_users" value="<?php _e('Delete','cftp_admin'); ?>" class="button_form" />
			</div>
		</div>
		<table id="users_tbl" class="tablesorter vertical_middle">
			<thead>
				<tr>
					<th class="td_checkbox">
						<input type="checkbox" name="select_all" id="select_all" value="0" />
					</th>
					<th><?php _e('Full name','cftp_admin'); ?></th>
					<th><?php _e('Log in username','cftp_admin'); ?></th>
					<th><?php _e('E-mail','cftp_admin'); ?></th>
					<th><?php _e('Role','cftp_admin'); ?></th>
					<th><?php _e('Added on','cftp_admin'); ?></th>
					<th><?php _e('Actions','cftp_admin'); ?></th>
				</tr>
			</thead>
			<tbody>
			
			<?php
				while($row = mysql_fetch_array($sql)) {
				?>
				<tr>
					<td>
						<?php if ($row["id"] != '1') { ?>
							<input type="checkbox" name="delete[]" value="<?php echo $row["id"]; ?>" />
						<?php } ?>
					</td>
					<td><?php echo $row["name"]?></td>
					<td><?php echo $row["user"]?></td>
					<td><?php echo $row["email"]?></td>
					<td><?php
						switch($row["level"]) {
							case '9': echo USER_ROLE_LVL_9; break;
							case '8': echo USER_ROLE_LVL_8; break;
							case '7': echo USER_ROLE_LVL_7; break;
						}
					?>
					</td>
					<td><?php echo date(TIMEFORMAT_USE,$row['timestamp']); ?></td>
					<td>
						<a href="userform.php?do=edit&amp;user=<?php echo $row["id"]; ?>" class="button button_small button_blue"><?php _e('Edit','cftp_admin'); ?></a>
					</td>
				</tr>
						
				<?php
				}
			
				$database->Close();
			?>
			
			</tbody>
		</table>
	</form>
	
	<?php if ($count > 10) { ?>
	<div id="pager" class="pager">
		<form>
			<input type="button" class="first pag_btn" value="<?php _e('First','cftp_admin'); ?>" />
			<input type="button" class="prev pag_btn" value="<?php _e('Prev.','cftp_admin'); ?>" />
			<span><strong><?php _e('Page','cftp_admin'); ?></strong>:</span>
			<input type="text" class="pagedisplay" disabled="disabled" />
			<input type="button" class="next pag_btn" value="<?php _e('Next','cftp_admin'); ?>" />
			<input type="button" class="last pag_btn" value="<?php _e('Last','cftp_admin'); ?>" />
			<span><strong><?php _e('Show','cftp_admin'); ?></strong>:</span>
			<select class="pagesize">
				<option selected="selected" value="10">10</option>
				<option value="20">20</option>
				<option value="30">30</option>
				<option value="40">40</option>
			</select>
		</form>
	</div>
	<?php } else { ?>
		<div id="pager">
			<form>
				<input type="hidden" value="<?php echo $count; ?>" class="pagesize" />
			</form>
		</div>
	<?php } ?>

</div>

<?php include('footer.php'); ?>