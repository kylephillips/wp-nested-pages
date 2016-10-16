<div class="wrap nestedpages">
	<h2 class="nestedpages-listing-title">
		<?php _e($this->post_type->labels->name); ?>
		
		<a href="<?php echo $this->post_type_repo->addNewPostLink($this->post_type->name); ?>" class="add-new-h2">
			<?php _e($this->post_type->labels->add_new); ?>
		</a>

		<?php if ( current_user_can('publish_pages') && !$this->isSearch() ) : ?>
		<a href="#" class="add-new-h2 open-bulk-modal" title="<?php _e('Add Multiple', 'nestedpages'); ?>" data-parentid="0">
			<?php _e('Add Multiple', 'nestedpages'); ?>
		</a>
		<?php endif; ?>
		
		<?php if ( current_user_can('publish_pages') && $this->post_type->name == 'page' && !$this->isSearch() && !$this->settings->menusDisabled() ) : ?>
		<a href="#" class="add-new-h2 open-redirect-modal" title="<?php _e('Add Link', 'nestedpages'); ?>" data-parentid="0">
			<?php _e('Add Link', 'nestedpages'); ?>
		</a>
		<?php endif; ?>

	</h2>

	<?php if ( $this->confirmation->getMessage() ) : ?>
		<div id="message" class="updated notice is-dismissible"><p><?php echo $this->confirmation->getMessage(); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
	<?php endif; ?>

	<div data-nestedpages-error class="updated error notice is-dismissible" style="display:none;"><p></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>

	<div class="nestedpages-top-toggles">
		<?php if ( $this->post_type->hierarchical && !$this->isSearch() ) : ?>
		<a href="#" class="np-btn nestedpages-toggleall" data-toggle="closed"><?php _e('Expand All', 'nestedpages'); ?></a>
		<?php endif; ?>

		<?php if ( $this->user->canSortPages() && !$this->isSearch() && !$this->isFiltered() ) : ?>
		<div class="np-sync-menu-cont" <?php if ( $this->confirmation->getMessage() ) echo 'style="margin-top:2px;"';?>>

			<?php if ( $this->settings->autoPageOrderDisabled() ) : ?>
			<a href="#" class="np-btn" data-np-manual-order-sync><?php echo __('Sync', 'nestedpages') . ' ' . $this->post_type->labels->singular_name . ' ' . __('Order', 'nestedpages'); ?></a>
			<?php endif; ?>

			<?php if ( $this->post_type->name == 'page' && !$this->settings->hideMenuSync() && !$this->settings->menusDisabled() ) : ?>

				<?php if ( !$this->settings->autoMenuDisabled() ) : ?>
				<label>
					<input type="checkbox" name="np_sync_menu" class="np-sync-menu" value="sync" <?php if ( get_option('nestedpages_menusync') == 'sync' ) echo 'checked'; ?>/> <?php _e('Sync Menu', 'nestedpages'); ?>
				</label>
				<?php else : ?>
					<a href="#" class="np-btn" data-np-manual-menu-sync><?php _e('Sync Menu', 'nestedpages'); ?></a>
				<?php endif; ?>

			<?php endif; ?>
			
		</div>
		<?php endif; ?>

		<img src="<?php echo NestedPages\Helpers::plugin_url(); ?>/assets/images/spinner-2x.gif" alt="loading" id="nested-loading" />
	</div><!-- .nestedpages-top-toggles -->

	<?php include(NestedPages\Helpers::view('partials/tool-list')); ?>

	<div id="np-error" class="updated error" style="clear:both;display:none;"></div>


	<div class="nestedpages">
		<?php $this->loopPosts(); ?>
		
		<div class="quick-edit quick-edit-form np-inline-modal" style="display:none;">
			<?php include( NestedPages\Helpers::view('forms/quickedit-post') ); ?>
		</div>

		<?php if ( current_user_can('publish_pages') ) : ?>
		<div class="quick-edit quick-edit-form-redirect np-inline-modal" style="display:none;">
			<?php include( NestedPages\Helpers::view('forms/quickedit-link') ); ?>
		</div>

		<div class="new-child new-child-form np-inline-modal" style="display:none;">
			<?php include( NestedPages\Helpers::view('forms/new-child') ); ?>
		</div>
		<?php endif; ?>
	</div>

</div><!-- .wrap -->

<?php 
include( NestedPages\Helpers::view('forms/more-options-modal') );
include( NestedPages\Helpers::view('forms/empty-trash-modal') );
include( NestedPages\Helpers::view('forms/clone-form') );
include( NestedPages\Helpers::view('forms/link-form') );
include( NestedPages\Helpers::view('forms/bulk-add') );
include( NestedPages\Helpers::view('forms/delete-confirmation-modal') ); 