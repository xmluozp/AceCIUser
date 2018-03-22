<style>

	body{
		background-image: url("<?php echo base_url('assets/images/bg.jpg');?>");
		background-repeat: no-repeat;
		background-size: 100% 100%;
		background-color: #000000;
	}
</style>


<link href="<?php echo base_url('assets/css/guest_style.css');?>" rel="stylesheet">

<div class="logo_large">
<img src="<?php echo base_url('assets/images/logo_lg.png');?>">
</div>

<div class="login_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_signup', array('autocomplete' => 'off', 'id' => 'signup_form')); ?>
		<div class="modal-body">


				<?php if($errorMessages):?>

				<div class="alert alert-danger" role="alert">
					<?=rawurldecode($errorMessages)?>
				</div>
				<?php endif?>
				<div class="form-group">
					<label for="user_email">Email</label>
					<input type="email" class="form-control" data-validation autocomplete="off" id="user_email" name="user_email" aria-describedby="emailHelp"
						   placeholder="Enter email" style="background: rgba(255, 255, 255, 0.3);">
				</div>

				<div class="form-group">
					<label for="user_password">Password</label>
					<input type="password" class="form-control" data-validation autocomplete="off" name="user_password"
						   id="user_password" placeholder="Password" style="background: rgba(255, 255, 255, 0.3);">
				</div>
				
				<div class="form-group">
					<label for="user_confirm">Confirm</label>
					<input type="password" class="form-control" data-validation autocomplete="off" name="user_confirm"
						   id="user_confirm" placeholder="Password" style="background: rgba(255, 255, 255, 0.3);">
				</div>
				
				

		</div>

		<div class="form-group" style="text-align: center; padding-left: 15px; padding-right:15px;">
			<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Create Account</button>
		</div>
		<div class="modal-footer">

	
			<div class="col-md-12" style="padding-left: 10px;">
				<a href="<?php echo site_url('users/view_login');?>" style="color: #999">
					<i class="material-icons" style="color: #999">arrow_back</i>
					Back To Login</a>
			</div>



		</div>


		</form>
	</div>
</div>

</div>
<script>

	$(document).ready(function() {

		loginform = new non_ajax_validation("#signup_form");

		return_json = '<?=$json_error?>';

		loginform.show_errors(return_json);

		loginform.post_back(return_json);

	});

	function success()
	{
		return null;
	}
</script>