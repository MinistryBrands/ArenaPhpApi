<?php
	
require 'bootstrap.php';

$arena->login();

$groups = $arena->listGroups([
	'categoryid' => 12,
]);

if ($_POST)
{
	$people = $arena->listPersons([
	   'firstName' => $_POST['firstName'],
	   'lastName' => $_POST['lastName'],
	   //'email' => $_POST['email'],
	   'phoneCell' => $_POST['phoneCell'],
	]);

	$personId = NULL;

	if ($people->Persons)
	{
		$personId = $people->Persons[0]->PersonID;
	}
	else
	{
		$person = $arena->addPerson([
		   'phoneCell' => $_POST['phoneCell'],
		   'email' => $_POST['email'],
		   'firstName' => $_POST['firstName'],
		   'lastName' => $_POST['lastName'],
		]);
		
		if ($person->ObjectID)
		{
			$personId = $person->ObjectID;
			
			/*$arena->addPersonToProfile([
			   'profileId' => 56,
			   'personId' => $personId,
			]);*/
		}
	}
	
	if ($personId)
	{
		$ret = $arena->addGroupMember([
		   'groupId' => $_POST['groupId'],
		   'personId' => $personId,
		]);
		
		$_SESSION['groupMemberships'][$_POST['groupId']] = TRUE;
		
		$message = 'You have been added to the group!';
	}
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Group Finder</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</head>
	<body>
		<div class="container">
	  		<div class="row">
		    	<h1>Find a Group!</h1>
		    	
		    	<?php if ($message): ?>
		    		<div class="alert alert-success" role="alert"><?php print $message; ?></div>
		    	<?php endif; ?>
		    	
				<table class="table">
				    <thead>
					    <th>Group Name</th>
					    <th>Description</th>
					    <th>Join</th>
				    </thead>
				    <tbody>
					    <?php foreach ($groups->Groups AS $group): ?>
					    	<tr>
								<td><?php print $group->Name; ?></td>
								<td><?php print $group->Description; ?></td>
								<td>
									<a href="javascript:void(0);" data-groupid="<?php print $group->GroupID; ?>" class="<?php print $_SESSION['groupMemberships'][$group->GroupID] ? 'disabled' : ''; ?> btn btn-success join-button">Join</a></td>
					    	</tr>
						<?php endforeach; ?>
				    </tbody>
			    </table>
	  		</div>
	  	</div>
	  	
	  	<div class="modal fade" id="joinModal" tabindex="-1" role="dialog" aria-labelledby="joinModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Join a Group</h4>
					</div>
					<form action="" method="post" id="joinForm" role="form">
						<div class="modal-body">
							<div class="form-group">
								<label>First Name <span class="form-required" title="This field is required.">*</span></label>
								<input class="form-control" type="text" value="" name="firstName" id="firstName" />
							</div>
							<div class="form-group">
								<label>Last Name <span class="form-required" title="This field is required.">*</span></label>
								<input class="form-control" type="text" value="" name="lastName"  id="lastName" />
							</div>
							<div class="form-group">
								<label>Cell Phone Number <span class="form-required" title="This field is required.">*</span></label>
								<input class="form-control" type="tel" value="" name="phoneCell" id="phoneCell" />
							</div>
							<div class="form-group">
								<label>Email Address <span class="form-required" title="This field is required.">*</span></label>
								<input class="form-control" type="email" value="" name="email" id="email" />
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" id="code" name="code" />
							<input type="hidden" id="groupId" name="groupId" />
							<input class="btn btn-success" type="submit" value="Join" id="" />
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</form>
					<div class="modal-body" id="joinConfirmation" style="display:none;">
						<p>Your request has been submitted. Thank you!</p>
					</div>
				</div>
			</div>
		</div>
	  	
	  	<script>
			
			$(".join-button").click(function()
			{
				if ($(this).hasClass('disabled'))
				{
					return false;
				}
				
				$("#joinModal").modal('show');
				
				$("#groupId").val($(this).data('groupid'));
				
				return false;
			});
			  	
		</script>
  	</body>
</html>