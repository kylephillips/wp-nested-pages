<?php
$wpml_pages = ( $this->integrations->plugins->wpml->installed && $this->integrations->plugins->wpml->isDefaultLanguage()) ? true : false;
if ( !$this->integrations->plugins->wpml->installed ) $wpml_pages = true;
$dark_mode = ( $this->integrations->plugins->dark_mode->installed ) ? true : false;
if ( $dark_mode ) :
	$dark_meta = get_user_meta(get_current_user_id(), 'dark_mode', true);
	if ( $dark_meta !== 'on' ) $dark_mode = false;
endif;
?>
<div class="wrap nestedpages <?php if ( $dark_mode ) echo 'np-dark-mode'; ?>">
	<div class="nestedpages-listing-title">
		<h1 class="wp-heading-inline">
			<?php echo apply_filters('nestedpages_page_title', esc_html__($this->post_type->labels->name), $this->post_type); ?>
		</h1>
		
		<?php if ( $this->user->canPublish($this->post_type->name) ) :  ?>
		<a href="<?php echo $this->post_type_repo->addNewPostLink($this->post_type->name); ?>" class="page-title-action">
			<?php echo apply_filters('nestedpages_add_new_text', esc_html__($this->post_type->labels->add_new), $this->post_type); ?>
		</a>
		<?php endif; ?>

		<?php if ( $this->user->canPublish($this->post_type->name) && !$this->listing_repo->isSearch() && $wpml_pages ) : ?>
		<a href="#" class="open-bulk-modal page-title-action" title="<?php _e('Add Multiple', 'wp-nested-pages'); ?>" data-parentid="0" data-nestedpages-modal-toggle="np-bulk-modal">
			<?php echo apply_filters('nestedpages_add_multiple_text', esc_html__('Add Multiple', 'wp-nested-pages'), $this->post_type); ?>
		</a>
		<?php endif; ?>
		
		<?php if ( $this->user->canPublish($this->post_type->name) && $this->post_type->name == 'page' && !$this->listing_repo->isSearch() && !$this->listing_repo->isOrdered($this->post_type->name) && !$this->settings->menusDisabled() && !$this->integrations->plugins->wpml->installed ) : ?>
		<a href="#" class="open-redirect-modal page-title-action" title="<?php _e('Add Link', 'wp-nested-pages'); ?>" data-parentid="0">
			<?php echo apply_filters('nestedpages_add_link_text', esc_html__('Add Link', 'wp-nested-pages'), $this->post_type); ?>
		</a>
		<?php endif; ?>

		<div class="nestedpages-top-toggles">
		<?php if ( $this->post_type->hierarchical && !$this->listing_repo->isSearch() ) : ?>
		<a href="#" class="np-btn nestedpages-toggleall" data-toggle="closed"><?php esc_html_e('Expand All', 'wp-nested-pages'); ?></a>
		<?php endif; ?>

		<?php if ( $this->user->canSortPages() && !$this->listing_repo->isSearch() && !$this->listing_repo->isFiltered() && !$this->listing_repo->isOrdered($this->post_type->name) ) : ?>
		<div class="np-sync-menu-cont" <?php if ( $this->confirmation->getMessage() ) echo 'style="margin-top:2px;"';?>>

			<?php if ( $this->settings->autoPageOrderDisabled() ) : ?>
			<a href="#" class="np-btn" data-np-manual-order-sync><?php echo sprintf(__('Sync %s Order', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
			<?php endif; ?>

			<?php 
				$wpml = $this->integrations->plugins->wpml->installed;
				$primary_language = ( $wpml && $this->integrations->plugins->wpml->isDefaultLanguage() ) ? true : false;
				if ( !$wpml ) $primary_language = true;

				if ( $this->post_type->name == 'page' && 
				!$this->settings->hideMenuSync() && 
				!$this->settings->menusDisabled() &&
				$primary_language ) : 
				?>

				<?php if ( !$this->settings->autoMenuDisabled() ) : ?>
				<label>
					<input type="checkbox" name="np_sync_menu" class="np-sync-menu" value="sync" <?php if ( get_option('nestedpages_menusync') == 'sync' ) echo 'checked'; ?>/> 
					<?php 
						esc_html_e('Sync Menu', 'wp-nested-pages'); 
						if ( $wpml ) echo ' (' . esc_html__($this->integrations->plugins->wpml->getCurrentLanguage('name')) . ')';
					?>
				</label>
				<?php else : ?>
					<a href="#" class="np-btn" data-np-manual-menu-sync><?php esc_html_e('Sync Menu', 'wp-nested-pages'); ?></a>
				<?php endif; ?>


			<?php endif; ?>
			
			<?php if ( $wpml && !$primary_language ) echo $this->integrations->plugins->wpml->syncMenusButton(); ?>
			
		</div>
		<?php endif; ?>

		<div id="nested-loading">
			<?php include( NestedPages\Helpers::asset('images/spinner.svg') ); ?>
		</div>
	</div><!-- .nestedpages-top-toggles -->

	</div><!-- .nestedpages-listing-title -->

	<?php if ( $this->confirmation->getMessage() ) : ?>
		<div id="message" class="updated notice is-dismissible"><p><?php echo $this->confirmation->getMessage(); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'wp-nested-pages'); ?></span></button></div>
	<?php endif; ?>

	<div data-nestedpages-error class="updated error notice is-dismissible" style="display:none;"><p></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'wp-nested-pages'); ?></span></button></div>

	<?php include(NestedPages\Helpers::view('partials/tool-list')); ?>

	<div id="np-error" class="updated error" style="clear:both;display:none;"></div>


	<div class="nestedpages">
		<?php $this->getPosts(); ?>
		
		<div class="quick-edit quick-edit-form np-inline-modal" style="display:none;">
			<?php include( NestedPages\Helpers::view('forms/quickedit-post') ); ?>
		</div>

		<?php if ( $this->user->canPublish($this->post_type->name) && !$this->integrations->plugins->wpml->installed ) : ?>
		<div class="quick-edit quick-edit-form-redirect np-inline-modal" style="display:none;">
			<?php include( NestedPages\Helpers::view('forms/quickedit-link') ); ?>
		</div>
		<?php endif; ?>

		<?php if ( $this->user->canPublish($this->post_type->name) ) : ?>
		<div class="new-child new-child-form np-inline-modal" style="display:none;">
			<?php include( NestedPages\Helpers::view('forms/new-child') ); ?>
		</div>
		<?php endif; ?>
	</div>

</div><!-- .wrap -->

<?php 
include( NestedPages\Helpers::view('forms/empty-trash-modal') );
include( NestedPages\Helpers::view('forms/clone-form') );
if ( !$this->integrations->plugins->wpml->installed) include( NestedPages\Helpers::view('forms/link-form') );
include( NestedPages\Helpers::view('forms/bulk-add') );
include( NestedPages\Helpers::view('forms/delete-confirmation-modal') ); 
if ( $this->integrations->plugins->wpml->installed ) include( NestedPages\Helpers::view('partials/wpml-translations') );