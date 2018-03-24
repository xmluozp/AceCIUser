<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
</div>
<div class="wizard_panel" style="width: 80%">
	<div class="modal-content">
		<div class="modal-body">
			<div class="card-body">
				<h5 class="card-title">Welcome!</h5>
				<p class="card-text home_info">
					How do you grab information you need? <br/><br/>
					
					
					<label>Your Email:</label> get_user_email() <span>or</span> $_SESSION['user_email']<br/>
					<label>So you get:</label> <?=get_user_email()?>  <br/><br/>
	

					<label>Your User Id:</label> get_user_id() <span>or</span> $_SESSION['user_id']<br/>
					<label>So you get:</label> <?=get_user_id()?>  <br/><br/>

					<label>Your User group Id:</label> get_user_group_id()<br/>
					<label>So you get:</label> <?=get_user_group_id()?>  <br/><br/>

					<label>User group Name:</label> get_user_group_name() <span>or</span> $_SESSION['get_user_group_name']<br/>
					<label>So you get:</label> <?=get_user_group_name()?>  <br/><br/>

					<label>Your Group Level:</label> get_user_group_level()<br/>
					<label>So you get:</label> <?=get_user_group_level()?>  <br/><br/>

					<label>Your Organization Name:</label> get_organization_name() <br/>
					<label>So you get:</label> <?=get_organization_name()?>  <br/><br/>

					<label>Your Organization ID:</label> get_organization_id() <span>or</span> $_SESSION['organization_id']<br/>
					<label>So you get:</label> <?=get_organization_id()?>  <br/><br/>

					<label>Organization Logo:</label> get_organization_logo() <br/>
					<label>So you get:</label> <?=get_organization_logo()?>  <br/><br/>

					<label>If you want to display it:</label><br/>
					<img src = "<?=base_url(get_organization_logo())?>" alt="<?=get_organization_name()?>" title = "<?=get_organization_name()?>"/>
					
	
					
					
				</p>
			</div>
		</div>
	</div>
</div>

<style>
.home_info label{
	display:inline-block;
	width: 200px;
	font-weight:bold;
}
.home_info span{
	display:inline-block;
	text-align:center;
	width: 50px;
	font-weight:bold;
}


</style>
