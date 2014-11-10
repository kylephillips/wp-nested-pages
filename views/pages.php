<div class="wrap">

	<h2>
		<?php echo $this->post_type->labels->name; ?>
		<a href="<?php echo $this->addNewPageLink(); ?>" class="add-new-h2"><?php echo $this->post_type->labels->add_new; ?></a>
		<?php if ( current_user_can('publish_pages') ) : ?>
		<a href="#" class="add-new-h2 open-redirect-modal" title="<?php _e('Add Link', 'nestedpages'); ?>" data-parentid="0"><?php _e('Add Link', 'nestedpages'); ?></a>
		<?php endif; ?>
	</h2>

	<?php if ( $this->confirmation() ) echo $this->confirmation() . '<div style="clear:both;"></div>'; ?>

	<ul class="nestedpages-toggleall" <?php if ( $this->confirmation() ) echo 'style="margin-top:0"';?>>
		<li><a href="#" class="np-btn" data-toggle="closed"><?php _e('Expand Pages'); ?></a></li>
	</ul>

	<?php if ( current_user_can('edit_theme_options') ) : ?>
	<div class="np-sync-menu-cont" <?php if ( $this->confirmation() ) echo 'style="margin-top:2px;"';?>>
		<label>
			<input type="checkbox" name="np_sync_menu" class="np-sync-menu" value="sync" <?php if ( get_option('nestedpages_menusync') == 'sync' ) echo 'checked'; ?>/> <?php _e('Sync Menu'); ?>
		</label>
	</div>
	<?php endif; ?>

	<img src="<?php echo plugins_url(); ?>/wp-nested-pages/assets/images/loading.gif" alt="loading" id="nested-loading" />

	<ul class="subsubsub">
		<li><a href="#all" class="np-toggle-publish active"><?php _e('All'); ?></a> | </li>
		<li><a href="#published" class="np-toggle-publish"><?php _e('Published'); ?></a> | </li>
		<li><a href="#show" class="np-toggle-hidden"><?php _e('Show Hidden'); ?></a> | </li>
		<?php if ( current_user_can('delete_pages') ) : ?>
		<li><a href="edit.php?post_status=trash&post_type=page">
			<?php _e('Trash'); ?> (<?php echo $this->trashCount(); ?>)
		</a></li> | </li>
		<?php endif; ?>
		<li><a href="<?php echo $this->defaultPagesLink(); ?>"><?php _e('Default'); ?> <?php echo $this->post_type->labels->name; ?></a></li>
	</ul>

	<div id="np-error" class="updated error" style="clear:both;display:none;"></div>

	<div class="nestedpages">
		<?php $this->loopPages(); ?>
		
		<div class="quick-edit quick-edit-form" style="display:none;">
			<?php include( NP_Helpers::view('quickedit') ); ?>
		</div>

		<?php if ( current_user_can('publish_pages') ) : ?>
		<div class="quick-edit quick-edit-form-redirect" style="display:none;">
			<?php include( NP_Helpers::view('quickedit-redirect') ); ?>
		</div>
		<?php endif; ?>
	</div>

</div><!-- .wrap -->

<?php include( NP_Helpers::view('link-form') ); ?>