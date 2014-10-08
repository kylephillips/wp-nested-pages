<div class="wrap">

	<h2>
		<?php echo $this->post_type->labels->name; ?>
		<a href="<?php echo $this->addNewPageLink(); ?>" class="add-new-h2"><?php echo $this->post_type->labels->add_new; ?></a>
	</h2>

	<ul class="nestedpages-toggleall">
		<li><a href="#" class="np-btn" data-toggle="closed">Expand Pages</a></li>
	</ul>

	<img src="<?php echo plugins_url(); ?>/nestedpages/assets/images/loading.gif" alt="loading" id="nested-loading" />

	<ul class="subsubsub">
		<li><a href="<?php echo $this->defaultPagesLink(); ?>">Default <?php echo $this->post_type->labels->name; ?></a></li>
	</ul>

	<div class="nestedpages">
		<?php
		// $pages = get_pages(array('sort_column'=>'menu_order'));
		// print_r($pages);
		?>

		<?php $this->loopPages(); ?>
		

		<div class="quick-edit">
			<?php // include('quickedit.php'); ?>
		</div><!-- .quick-edit -->
	</div>


</div><!-- .wrap -->