
<link href="<?php echo base_url('assets/css/guest_style.css');?>" rel="stylesheet">
<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
<div class="wizard_panel">
	<div class="modal-content">
		<div class="modal-body">
		
			<?php if($type == 0):?>
			<div class="alert alert-danger" role="alert">
			
				<div class="card-body">
					
						<h5 class="card-title">Failed</h5>
						<p class="card-text">
							<?=rawurldecode($messages)?>
						</p>
				</div>
				
			</div>

			<?php else:?>	
		
			<div class="alert alert-success" role="alert">
				<div class="card-body">
					


					
						<h5 class="card-title">Congratulations</h5>
						<p class="card-text">
							<?=rawurldecode($messages)?>
						</p>
					
				
				</div>
			</div>
	<?php endif?>
		</div>

	</div>
</div>

</div>
