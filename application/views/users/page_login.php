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
		<?php echo form_open('users/form_login', array('autocomplete' => 'off', 'id' => 'login_form')); ?>
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
				<div class="form-group form-check" style="padding-left: 20px;">
					<input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="1">
					<label class="form-check-label" for="remember_me" style="cursor: pointer;">Remember Me</label>
				</div>

		</div>

		<div class="form-group" style="text-align: center; padding-left: 15px; padding-right:15px;">
			<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Login</button>
		</div>
		<div class="modal-footer">

			<div class="col-md-6">
				<a href="<?php echo site_url('users/view_forgot');?>" style="font-size: 18px;color: #999">
					Forgot password?</a>

			</div>


			<div class="col-md-6 text-right" style="padding-right: 10px;">
				<a href="<?php echo site_url('users/view_signup');?>">
					<i class="material-icons" style="font-size: 18px; color: #E95420">border_color</i>
					Sign Up</a>
			</div>
		</div>


		</form>
	</div>
</div>

</div>
<script>

	$(document).ready(function() {

		loginform = new non_ajax_validation("#login_form");

		return_json = '<?=$json_error?>';

		loginform.show_errors(return_json);

		loginform.post_back(return_json);

	});

	function success()
	{
		return null;
	}
</script>
