<?php
/**
* Row represents a single page
*/
$wpml = $this->integrations->plugins->wpml->installed;
$wpml_pages = ( $wpml && $this->integrations->plugins->wpml->isDefaultLanguage()) ? true : false;
if ( !$wpml ) $wpml_pages = true;
?>
<div class="row <?php echo apply_filters('nestedpages_row_css_classes', $row_classes, $this->post, $this->post_type); ?>">
	
	<?php if ( $this->post_type->hierarchical ) : ?>
	<div class="child-toggle">
		<div class="child-toggle-spacer"></div>
	</div>
	<?php endif; ?>

	<div class="row-inner">

		<?php if ( $this->showSortHandle() ) : ?>
		<img src="<?php echo \NestedPages\Helpers::plugin_url() . '/assets/images/handle.svg'; ?>" alt="<?php _e('Sorting Handle', 'wp-nested-pages'); ?>" class="handle np-icon-menu <?php if ( $this->post_list_table ) echo 'has-post-list-table'; ?>">
		<?php endif; ?>

		<?php if ( !$this->post_list_table ) : ?>

			<?php include( NestedPages\Helpers::view('partials/row-post-title-link') ); ?>		

			<?php echo $this->rowActions($assigned_pt); ?>

			<?php if ( !$this->post->hierarchical ) : ?>
			<div class="np-post-columns">
				<ul class="np-post-info">
					<li><span class="np-author-display"><?php echo apply_filters('nestedpages_post_author', $this->post->author, $this->post); ?></span></li>
					<li><?php echo get_the_date(); ?></li>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ( $this->integrations->plugins->yoast->installed ) echo $this->post->score; ?>

		<?php else : ?>
			<?php include( NestedPages\Helpers::view('partials/row-post-list-table') ); ?>
		<?php endif; // post_list_table ?>

		<?php include( NestedPages\Helpers::view('partials/row-action-buttons-post') ); ?>
	</div><!-- .row-inner -->

	<?php if ( !$this->post_list_table ) include( NestedPages\Helpers::view('partials/row-thumbnail') ); ?>

	<div class="np-bulk-checkbox">
		<input type="checkbox" name="nestedpages_bulk[]" value="<?php echo esc_attr($this->post->id); ?>" data-np-bulk-checkbox="<?php echo esc_attr($this->post->title); ?>" data-np-post-type="<?php echo esc_attr($this->post->post_type); ?>" />
	</div>
</div><!-- .row -->