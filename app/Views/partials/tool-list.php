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
			<a href="#show" class="np-toggle-hidden"><?php _e('Show Hidden', 'nestedpages'); ?> </a>
			<span class="count">(<?php echo $this->post_repo->getHiddenCount(array($this->post_type->name)); ?>)</span> | 
		</li>
		
		<?php if ( current_user_can('delete_pages') && $trashedCount > 0) : ?>
		<li>
			<a href="<?php echo $this->post_type_repo->trashLink($this->post_type->name); ?>"><?php _e('Trash'); ?> </a>
			<span class="count">(<?php echo $trashedCount; ?>)</span>
			 | 
		</li>
		<?php endif; ?>

		<?php if ( get_option('nestedpages_hidedefault') !== 'hide' ) : ?>
		<li>
			<a href="<?php echo NestedPages\Helpers::defaultPagesLink($this->post_type->name); ?>">
				<?php _e('Default'); ?> <?php _e($this->post_type->labels->name); ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>

</div><!-- .nestedpages-tools -->