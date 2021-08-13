<?php 
$trashedCount = $this->post_repo->trashedCount($this->post_type->name); 
$searchLabel = esc_attr($this->post_type->labels->search_items);

// WPML
$wpml = $this->integrations->plugins->wpml->installed;
if ( $wpml ) $current_lang = $this->integrations->plugins->wpml->getCurrentLanguage('name');
if ( $wpml && $current_lang ) $searchLabel .= ' (' . $this->integrations->plugins->wpml->getCurrentLanguage('name') . ')';
?>
<div class="nestedpages-tools">

	<ul class="subsubsub">
		<li>
			<a href="#all" class="np-toggle-publish active"><?php _e('All', 'wp-nested-pages'); ?></a> |
		</li>

		<li>
			<a href="#published" class="np-toggle-publish"><?php _e('Published', 'wp-nested-pages'); ?></a> |
		</li>

		<li>
			<a href="#draft" class="np-toggle-publish"><?php _e('Draft', 'wp-nested-pages'); ?></a>
		</li>

		<li> |
			<?php if ( !$this->listing_repo->isSearch() ) : ?>
			<a href="#hide" class="np-toggle-hidden"><?php _e('Show Hidden', 'wp-nested-pages'); ?> </a>
			<?php else : ?>
			<a href="#show" class="np-toggle-hidden"><?php _e('Hide Hidden', 'wp-nested-pages'); ?> </a>
			<?php endif; ?>
			<span class="count">(<?php echo absint($this->post_repo->getHiddenCount(array($this->post_type->name))); ?>)</span>
		</li>

		<?php if ( current_user_can('delete_pages') && $trashedCount > 0) : ?>
		<li class="np-trash-links">
			 |
			<a href="<?php echo esc_url($this->post_type_repo->trashLink($this->post_type->name)); ?>"><?php _e('Trash', 'wp-nested-pages'); ?> </a>
			<span class="count"><a href="#" class="np-empty-trash" data-posttype="<?php echo esc_attr($this->post_type->name); ?>" data-nestedpages-modal-toggle="np-trash-modal"><?php echo sprintf(__('Empty (%s)', 'wp-nested-pages'), absint($trashedCount)); ?></a></span>
		</li>
		<?php endif; ?>

		<?php if ( !$this->post_type_repo->postTypeSetting($this->post_type->name, 'hide_default') ) : ?>
		<li>
			 |
			<a href="<?php echo NestedPages\Helpers::defaultPagesLink($this->post_type->name); ?>">
				<?php echo apply_filters('nestedpages_default_submenu_text', sprintf(__('Default %s', 'wp-nested-pages'),$this->post_type->labels->name), $this->post_type); ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>

	<?php
	if ( $this->integrations->plugins->wpml->installed ) 
		if ( $this->post_type->name !== 'post' ) echo $this->integrations->plugins->wpml->languageToolLinks(esc_attr($this->post_type->name));
	?>

	<?php 
	if ( $this->post_type_repo->hasSortOptions($this->post_type->name) ) : ?>
	<div class="np-tools-primary">
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="np-tools-sort">
			<?php if ( $this->post_type_repo->sortOptionEnabled($this->post_type->name, 'author') ) : ?>
			<div class="select">
				<select id="np_sortauthor" name="np_author" class="nestedpages-sort">
					<?php
						$out = '<option value="all">' . __('All Authors', 'wp-nested-pages') . '</option>';
						$users = $this->user->allUsers();
						foreach( $users as $user ){
							$out .= '<option value="' . $user->ID . '"';
							if ( isset($_GET['author']) && ($_GET['author'] == $user->ID) ) $out .= ' selected';
							$out .= '>' . esc_html($user->display_name) . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<?php endif; ?>
			<?php 
			if ( $this->post_type_repo->sortOptionEnabled($this->post_type->name, 'orderby') ) : 
			$default_order_by = $this->post_type_repo->defaultSortOption($this->post_type->name, 'orderby');
			if ( isset($_GET['orderby']) ) $default_order_by = false;
			?>
			<div class="select">
				<select id="np_orderby" name="np_orderby" class="nestedpages-sort">
					<?php
						$options = array(
							'menu_order' => __('Menu Order', 'wp-nested-pages'),
							'date' => __('Date', 'wp-nested-pages'),
							'title' => __('Title', 'wp-nested-pages')
						);
						$out = '<option value="">' . __('Order By', 'wp-nested-pages') . '</option>';
						foreach ( $options as $key => $option ){
							$out .= '<option value="' . $key . '"';
							if ( $default_order_by && $default_order_by == $key ) $out .= ' selected';
							if ( isset($_GET['orderby']) && ($_GET['orderby'] == $key) ) $out .= ' selected';
							$out .= '>' . esc_html($option) . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<?php endif; ?>
			<?php 
			if ( $this->post_type_repo->sortOptionEnabled($this->post_type->name, 'order') ) : 
			$default_order = $this->post_type_repo->defaultSortOption($this->post_type->name, 'order');
			if ( isset($_GET['order']) ) $default_order = false;
			?>
			<div class="select">
				<select id="np_order" name="np_order" class="nestedpages-sort">
					<?php
						$options = [
							'ASC' => __('Ascending', 'wp-nested-pages'),
							'DESC' => __('Descending', 'wp-nested-pages')
						];
						$out = '';
						foreach ( $options as $key => $option ){
							$out .= '<option value="' . esc_attr($key) . '"';
							if ( $default_order && $default_order == $key ) $out .= ' selected';
							if ( isset($_GET['order']) && ($_GET['order'] == $key) ) $out .= ' selected';
							$out .= '>' . esc_html($option) . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<?php endif; ?>
			<?php 
				// Taxonomies
				$taxonomies = array_merge($this->h_taxonomies, $this->f_taxonomies);
				foreach ( $taxonomies as $tax ) :
					if ( $this->post_type_repo->sortOptionEnabled($this->post_type->name, $tax->name, true) ) :
						$terms = get_terms($tax->name);
						$out = '<div class="select">';
						$out .= '<select id="np_taxonomy_' . $tax->name . '" name="' . $tax->name . '" class="nestedpages-sort">';
						$out .= '<option value="all">' . $tax->labels->all_items . '</option>';
						foreach ( $terms as $term ) :
							$out .= '<option value="' . $term->term_id . '"';
							if ( isset($_GET[$tax->name]) && $_GET[$tax->name] == $term->term_id ) $out .= ' selected';
							$out .= '>' . $term->name . '</option>';
						endforeach;
						$out .= '</select>';
						$out .= '</div>';
						echo $out;
					endif;
				endforeach;
			?>
			<div class="select">
				<input type="hidden" name="action" value="npListingSort">
				<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">
				<?php wp_nonce_field('nestedpages-nonce', 'nonce'); ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr($this->post_type->name); ?>">
				<input type="submit" id="nestedpages-sort" class="button" value="<?php echo esc_attr__('Apply', 'wp-nested-pages'); ?>">
			</div>
		</form>
	</div>
	<?php endif; ?>


	<?php if ( $this->post_type->name == 'page' && $this->post_type_repo->categoriesEnabled($this->post_type->name) ) : ?>
	<div class="np-tools-primary">	
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="np-tools-sort">
			<div class="select">
				<select id="np_category" name="np_category" class="nestedpages-sort">
					<?php
						$tax = get_taxonomy('category');
						$out = '<option value="all">' . __('All ', 'wp-nested-pages') . esc_html($tax->labels->name) . '</option>';
						$terms = get_terms('category');
						foreach( $terms as $term ){
							$out .= '<option value="' . esc_attr($term->term_id) . '"';
							if ( isset($_GET['category']) && ($_GET['category'] == $term->term_id) ) $out .= ' selected';
							$out .= '>' . esc_html($term->name) . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<div class="select">
				<input type="hidden" name="action" value="npCategoryFilter">
				<?php wp_nonce_field('nestedpages-nonce', 'nonce'); ?>
				<input type="hidden" name="page" value="<?php echo esc_url($this->pageURL()); ?>">
				<input type="submit" id="nestedpages-sort" class="button" value="Apply">
			</div>
		</form>
	</div><!-- .np-tools-primary -->
	<?php endif; ?>

	<div class="np-tools-search">
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
			<input type="hidden" name="action" value="npSearch">
			<input type="hidden" name="posttype" value="<?php echo esc_attr($this->post_type->name); ?>">
			<input type="hidden" name="page" value="<?php echo esc_url($this->pageURL()); ?>">
			<?php wp_nonce_field('nestedpages-nonce', 'nonce'); ?>
			<input type="search" name="search_term" id="nestedpages-search" placeholder="<?php echo esc_attr($this->post_type->labels->search_items); ?>" <?php if ( $this->listing_repo->isSearch() ) echo ' value="' . esc_attr(sanitize_text_field($_GET['search'])) . '"'; ?>>
			<input type="submit" name="" class="button" value="<?php echo $searchLabel;?>">
		</form>
	</div><!-- .np-tools-search -->


</div><!-- .nestedpages-tools -->
