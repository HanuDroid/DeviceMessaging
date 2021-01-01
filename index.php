<?php
	require_once( 'GCMFunctions.php' );
	createDBConnection();
?>

<!DOCTYPE html>

<html lang="en">

<head>

	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

	<title>HANU GCM Utility</title>

	<!-- Bootstrap Core CSS -->
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

	<!-- Font Awesome CSS -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

</head>

<body>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

	<script src="js/gcm.js"></script>

	<div class="container" id="wrapper">

		<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="./">GCM Utility</a>
			</div>
		</div>
		</nav>

		<br><br>
		
		<?php $gcm_summary = get_gcm_summary(); ?>
		
		<div class="panel panel-success">
			<div class="panel-heading">GCM summary</div>
			<div class="panel-body">
				<div class="table-responsive">
				<table class="table table-striped" id="gcm_summary_table">
					<thead>
						<tr>
							<th>Application Name</th>
							<th>Active Devices</th>
							<th>Inactive Devices</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($gcm_summary as $row_data){
								echo "<tr>";
								echo "<td><a href=\"" . $row_data['BlogURL']."\" target=_blank>".$row_data['AppName'] . "</a></td>";
								echo "<td>" . $row_data['ActiveDeviceCount'] . "</td>";
								echo "<td>" . $row_data['InActiveDeviceCount'] . "</td>";
								echo "<td>";
								echo "<div class=\"btn-group\">";
								echo "<button type=\"button\" class=\"btn btn-info send_ping\" id=\"" . $row_data['BlogURL']. "\">Ping</button>";
								echo "<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
								echo "<span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span></button>";
								echo "<ul class=\"dropdown-menu\">";
								echo "<li><a class=\"check_duplicates btn btn-warning\" id=\"".$row_data['BlogURL']."\">Check Duplicates</a></li> ";
								echo "<li><a class=\"delete_duplicates btn btn-danger\" id=\"".$row_data['BlogURL']."\">Delete Duplicates</a></li>";
								echo "</ul></div>";
								echo "<button type=\"button\" class=\"btn btn-default send_sync_message\" id=\"" . $row_data['BlogURL']. "\">Send Sync Message</button>";
								echo "</td>";
								echo "</tr>";
							}
						?>
					</tbody>
				</table>
				</div>
			
			</div>
		</div>

		<div class="panel panel-info">
		  <div class="panel-heading">Welcome ! Manage your GCM here</div>
		  <div class="panel-body">
			<div class="input-group">
			  <span class="input-group-addon">GCM Token</span>
			  <input type="input" class="form-control" id="gcm-token">
			</div>

			<br>

			<div class="input-group">
			  <span class="input-group-addon">Blog URL</span>
			  <input type="input" class="form-control" id="blog-url">
			</div>
			
			<br>
			
			<div class="input-group">
			  <span class="input-group-addon">Instance ID</span>
			  <input type="input" class="form-control" id="instance-id">
			</div>

			<br>

			<div class="row">
				<div class="col-md-4">
					
					<div class="panel panel-info">
						<div class="panel-heading">Send Sync message</div>
						<div class="panel-body">

							<button type="button" class="btn btn-info" id="sync-msg-to-user">Send Sync Message to User</button>
							<br><br>
							<button type="button" class="btn btn-warning" id="sync-msg-to-blog-url">Send Sync Message to all Blog Users</button>
							<br><br>
							<button type="button" class="btn" id="sync-msg-to-instance">Send Sync Message to all Instance ID</button>

						</div>
					</div>
					
				</div>

				<div class="col-md-8">
					<div class="panel panel-warning">
						<div class="panel-heading">Send Notification message</div>
						<div class="panel-body">

							<div class="input-group">
							  <span class="input-group-addon">Message ID</span>
							  <input type="input" class="form-control" id="notif-message-id">
							</div>

							<br>

							<div class="input-group">
							  <span class="input-group-addon">Subject</span>
							  <input type="input" class="form-control" id="notif-subject">
							</div>

							<br>

							<div class="form-group">
							  <label for="comment">Notification Message:</label>
							  <textarea class="form-control" rows="5" id="notif-content"></textarea>
							</div>						

							<button type="button" class="btn btn-info" id="notif-to-user">Send Notif. to User</button>
							<button type="button" class="btn btn-warning" id="notif-to-blog-users">Send Notif. All Blog Users</button>
							<button type="button" class="btn btn-danger" id="notif-to-all-users">Send Notif. All Users</button>
							<button type="button" class="btn" id="notif-to-instance">Send Notif.  Instance IDs</button>
							

						</div>
						
					</div>
				</div>
				
			</div>
			
			<button type="button" class="btn btn-danger" id="clear-result-area">Clear Result Area</button>
			
		  </div>
	  
		  <div class="panel-footer">
			<div id="result"></div>
		  </div>	  

		</div>

	</div>

</body>
