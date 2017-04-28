<?php $trashedCount = $this->post_repo->trashedCount($this->post_type->name); ?>
<div class="nestedpages-tools">

	<ul class="subsubsub">
		<li>
			<a href="#all" class="np-toggle-publish active"><?php _e('All'); ?></a> |
		</li>

		<li>
			<a href="#published" class="np-toggle-publish"><?php _e('Published'); ?></a> |
		</li>

		<li>
			<a href="#draft" class="np-toggle-publish"><?php _e('Draft'); ?></a>
		</li>

		<li> |
			<?php if ( !$this->isSearch() ) : ?>
			<a href="#hide" class="np-toggle-hidden"><?php _e('Show Hidden', 'nestedpages'); ?> </a>
			<?php else : ?>
			<a href="#show" class="np-toggle-hidden"><?php _e('Hide Hidden', 'nestedpages'); ?> </a>
			<?php endif; ?>
			<span class="count">(<?php echo $this->post_repo->getHiddenCount(array($this->post_type->name)); ?>)</span>
		</li>

		<?php if ( current_user_can('delete_pages') && $trashedCount > 0) : ?>
		<li class="np-trash-links">
			 |
			<a href="<?php echo $this->post_type_repo->trashLink($this->post_type->name); ?>"><?php _e('Trash'); ?> </a>
			<span class="count">(<a href="#" class="np-empty-trash" data-posttype="<?php echo $this->post_type->name; ?>"><?php _e('Empty', 'nestedpages'); ?></a> <?php echo $trashedCount; ?>)</span>
		</li>
		<?php endif; ?>

		<?php if ( !$this->post_type_repo->postTypeSetting($this->post_type->name, 'hide_default') ) : ?>
		<li>
			 |
			<a href="<?php echo NestedPages\Helpers::defaultPagesLink($this->post_type->name); ?>">
				<?php _e('Default'); ?> <?php _e($this->post_type->labels->name); ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>

	<?php if ( !$this->post_type->hierarchical ) : ?>
	<div class="np-tools-primary">
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="np-tools-sort">
			<input type="hidden" name="action" value="npListingSort">
			<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">
			<div class="select first">
				<select id="np_sortauthor" name="np_author" class="nestedpages-sort">
					<?php
						$out = '<option value="all">' . __('All Authors', 'nestedpages') . '</option>';
						$users = $this->user->allUsers();
						foreach( $users as $user ){
							$out .= '<option value="' . $user->ID . '"';
							if ( isset($_GET['author']) && ($_GET['author'] == $user->ID) ) $out .= ' selected';
							$out .= '>' . $user->display_name . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<div class="select">
				<select id="np_orderby" name="np_orderby" class="nestedpages-sort">
					<?php
						$options = array(
							'menu_order' => __('Menu Order', 'nestedpages'),
							'date' => __('Date', 'nestedpages'),
							'title' => __('Title', 'nestedpages')
						);
						$out = '<option value="">' . __('Order By', 'nestedpages') . '</option>';
						foreach ( $options as $key => $option ){
							$out .= '<option value="' . $key . '"';
							if ( isset($_GET['orderby']) && ($_GET['orderby'] == $key) ) $out .= ' selected';
							$out .= '>' . $option . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<div class="select">
				<select id="np_order" name="np_order" class="nestedpages-sort">
					<?php
						$options = array(
							'ASC' => __('Ascending', 'nestedpages'),
							'DESC' => __('Descending', 'nestedpages')
						);
						$out = '';
						foreach ( $options as $key => $option ){
							$out .= '<option value="' . $key . '"';
							if ( isset($_GET['order']) && ($_GET['order'] == $key) ) $out .= ' selected';
							$out .= '>' . $option . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<div class="select">
				<input type="submit" id="nestedpages-sort" class="button" value="Apply">
			</div>
		</form>
	</div>
	<?php endif; ?>


	<?php if ( $this->post_type->name == 'page' && $this->post_type_repo->categoriesEnabled($this->post_type->name) ) : ?>
	<div class="np-tools-primary">	
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="np-tools-sort">
			<input type="hidden" name="action" value="npCategoryFilter">
			<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">
			<div class="select first">
				<select id="np_category" name="np_category" class="nestedpages-sort">
					<?php
						$tax = get_taxonomy('category');
						$out = '<option value="all">' . __('All ', 'nestedpages') . $tax->labels->name . '</option>';
						$terms = get_terms('category');
						foreach( $terms as $term ){
							$out .= '<option value="' . $term->term_id . '"';
							if ( isset($_GET['category']) && ($_GET['category'] == $term->term_id) ) $out .= ' selected';
							$out .= '>' . $term->name . '</option>';
						}
						echo $out;
					?>
				</select>
			</div>
			<div class="select">
				<input type="submit" id="nestedpages-sort" class="button" value="Apply">
			</div>
		</form>
	</div><!-- .np-tools-primary -->
	<?php endif; ?>

	<div class="np-tools-search">
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
			<input type="hidden" name="action" value="npSearch">
			<input type="hidden" name="posttype" value="<?php echo $this->post_type->name; ?>">
			<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">
			<?php wp_nonce_field('nestedpages-nonce', 'nonce'); ?>
			<input type="search" name="search_term" id="nestedpages-search" placeholder="<?php echo $this->post_type->labels->search_items; ?>" <?php if ( $this->isSearch() ) echo ' value="' . sanitize_text_field($_GET['search']) . '"'; ?>>
			<input type="submit" name="" class="button" value="<?php echo $this->post_type->labels->search_items;?>">
		</form>
	</div><!-- .np-tools-search -->


</div><!-- .nestedpages-tools -->
