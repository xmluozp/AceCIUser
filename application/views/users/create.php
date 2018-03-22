<h2><?php echo $title; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('users/create'); ?>

    <label for="user_email">Email</label>
    <input type="input" name="user_email" /><br />

    <label for="user_password">Password</label>
	<input type="password" name="user_password"></input><br />

	<label for="user_group_id">user_group_id test</label>
	<input type="input" name="user_group_id"/><br />

	<input type="submit" name="submit" value="Create user" />

</form>
