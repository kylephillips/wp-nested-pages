var NestedPages = NestedPages || {};

/**
* Formatting updates
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Formatter = function()
{
	
	var plugin = this;
	var $ = jQuery;


	// Update the Submenu Toggle Button State based on if the child menu is visible
	plugin.updateSubMenuToggle = function()
	{
		var allButtons = $(NestedPages.selectors.childToggle);
		for ( var i = 0; i < allButtons.length; i++ ){
			var button = allButtons[i];
			var row = $(button).parent('.row').parent('li');
			if ( $(row).children('ol').length > 0 ){ // Row has a child menu

				// Hide the toggle and child list if all items are in the trash
				if ( $(row).children('ol').find('li.page-row').length < 1 ){
					$(row).children('ol').hide();
					continue;
				}
				
				var open = ( $(row).children('ol:visible').length > 0 ) ? true : false;
				var html = '<div class="child-toggle-spacer"></div>';
				html += '<a href="#"';
				if ( open ) html += ' class="open"';
				html += '><span class="np-icon-arrow"></span></a>';
				$(button).html(html);

				if ( ($(row).children('ol').children('.np-hide').length > 0) && ($(row).children('ol').children('.np-hide.shown').length === 0) ){
					$(button).find('a').hide();
				} else if ( ($(row).children('ol').children('.np-hide').length > 0) && ($(row).children('ol').children('.np-hide.shown').length > 0) ){
					$(button).find('a').show();
				}

				continue;
			}
			$(button).empty().html('<div class="child-toggle-spacer"></div>'); // No Child Menu
		}
	}


	// Fix :visible :first css limitation when toggling various options
	plugin.setBorders = function()
	{
		$(NestedPages.selectors.rows).removeClass(NestedPages.cssClasses.noborder);
		$.each($(NestedPages.selectors.lists), function(){
			$(this).find('.page-row:visible:first').addClass(NestedPages.cssClasses.noborder);
		});
	}


	// Adjust nested margins based on how deep the list is nested
	plugin.setNestedMargins = function()
	{
		plugin.setIndent();
	}

	plugin.setIndent = function()
	{
		var amount = ( nestedpages.non_indent === '1' ) ? 20 : 30;
		var indent_element = ( nestedpages.non_indent === '1' ) ? '.row-inner' : '.child-toggle';
		$.each($(NestedPages.selectors.lists), function(i, v){
			var parent_count = $(this).parents(NestedPages.selectors.lists).length;
			var padding = 0;
			if ( !NestedPages.jsData.sortable ) padding = 10;
			if ( parent_count > 0 ){
				var padding = ( parent_count * amount ) + padding;
				$(this).find(indent_element).css('padding-left', padding + 'px');
				return;
			}
			if ( !NestedPages.jsData.sortable || $(this).hasClass('no-sort') ){
				$(this).find('.row-inner').css('padding-left', '10px');	
				return;
			}
			$(this).find('.row-inner').css('padding-left', '0px');
		});
	}

	plugin.setClassicIndent = function()
	{
		$.each($(NestedPages.selectors.lists), function(i, v){
			var parent_count = $(this).parents(NestedPages.selectors.lists).length;
			var padding = 0;
			if ( !NestedPages.jsData.sortable ) padding = 10;
			if ( parent_count > 0 ){
				var padding = ( parent_count * 20 ) + padding;
				$(this).find('.row-inner').css('padding-left', padding + 'px');
				return;
			}
			if ( !NestedPages.jsData.sortable || $(this).hasClass('no-sort') ){
				$(this).find('.row-inner').css('padding-left', '10px');	
				return;
			}
			$(this).find('.row-inner').css('padding-left', '0px');
		});
	}


	// Update the width of the placeholder ( width changes depending on level of nesting )
	plugin.updatePlaceholderWidth = function(ui)
	{
		if ( NestedPages.jsData.nestable ){
			var parentCount = $(ui.placeholder).parents('ol').length;
			var listWidth = $(NestedPages.selectors.sortable).width();
			var offset = ( parentCount * 40 ) - 40;
			var newWidth = listWidth - offset;
			$(ui.placeholder).width(newWidth).css('margin-left', offset + 'px');
		}
		plugin.updateListVisibility(ui);
	}


	// Update the list visibility on sort (prevent lists from collapsing when nesting)
	plugin.updateListVisibility = function(ui)
	{
		var parentList = $(ui.placeholder).parent('ol');
		if ( !$(parentList).is(':visible') ){
			$(parentList).addClass('nplist');
			$(parentList).show();
		}
	}


	// Remove the Quick Edit Overlay
	plugin.removeQuickEdit = function()
	{
		$(NestedPages.selectors.quickEditOverlay).removeClass('active').remove();
		$('.sortable .quick-edit').remove();
		$('.row').show();
	}


	// Show the Quick Edit Overlay
	plugin.showQuickEdit = function()
	{
		$('body').append('<div class="np-inline-overlay"></div>');
		setTimeout(function(){
			$('.np-inline-overlay').addClass('active');
		}, 50);
	}


	// Flash an updated row
	plugin.flashRow = function(row)
	{	
		$(row).addClass('np-updated');
		plugin.setBorders();
		setTimeout(function(){
			$(row).addClass('np-updated-show');
		}, 1500);
	}


	// Show an error message
	plugin.showAjaxError = function(message)
	{
		$(NestedPages.selectors.ajaxError).find('p').text(message);
		$(NestedPages.selectors.ajaxError).show();
	}


	// Hide the error message
	plugin.hideAjaxError = function(message)
	{
		$(NestedPages.selectors.ajaxError).hide();
	}


	// Size the link thumbnails to the same as the page/post thumbnails
	plugin.sizeLinkThumbnails = function()
	{
		var thumbnail = $(NestedPages.selectors.thumbnailContainer).not(NestedPages.selectors.thumbnailContainerLink).first().find('img');
		var width = $(thumbnail).width();
		var height = $(thumbnail).height();
		$.each($(NestedPages.selectors.thumbnailContainerLink), function(){
			$(this).width(width);
			$(this).height(height);
		});
	}

}
var NestedPages = NestedPages || {};
/**
* Dropdowns
* 
* @author Kyle Phillips
* 
* To use, wrap dropdown content and toggle link/button in an element with data attribute of data-dropdown
* Give content data attribute of data-dropdown-content and toggle element data attribute of data-dropdown-toggle
* For CSS, wrapping/parent element gets class of "dropdown", content gets class of "dropdown-content"
*/
NestedPages.Dropdowns = function()
{
	var self = this;
	var $ = jQuery;

	self.dropdown = ''; // The Active Dropdown
	self.activeBtn = ''; // The Active Button
	self.activeContent = ''; // The Active Dropdown Content
	self.toggleBtn = '[data-dropdown-toggle]';
	self.dropdownContainer = '[data-dropdown]';
	self.dropdownContent = '[data-dropdown-content]'

	self.selectors = {
		caret_up : 'icon-arrow_drop_up',
		caret_down : 'icon-arrow_drop_down'
	}

	self.bindEvents = function()
	{
		$(document).on('click', self.toggleBtn, function(e){
			e.preventDefault();
			self.activeBtn = $(this);
			self.dropdown = $(this).parents(self.dropdownContainer);
			self.toggleDropdown();
		});
		$(document).on('click', function(e){
			self.closeDropdowns(e.target);
		});
		$(document).on('dropdown-opened', function(e, content){
			if ( $(content).parents(NestedPages.selectors.row).length > 0 ){
				$(content).parents(NestedPages.selectors.row).addClass('active');
			}
		});
		$(document).on('dropdown-closed', function(){
			$(NestedPages.selectors.row).removeClass('active');
		});
	}

	self.toggleDropdown = function()
	{
		$('.' + self.selectors.caret_up).attr('class', self.selectors.caret_down);
		var content = $(self.dropdown).find(self.dropdownContent);
		self.activeContent = content;
		if ( $(content).hasClass('active') ){
			$(content).removeClass('active');
			$(self.activeBtn).removeClass('active');
			$(self.activeBtn).find('.' + self.selectors.caret_up).attr('class', self.selectors.caret_down);
			$(document).trigger('dropdown-closed', content);
			return;
		}
		self.setPositioning();
		$(self.toggleBtn).removeClass('active');
		$(self.dropdownContent).removeClass('active');
		$(content).addClass('active');
		$(self.activeBtn).find('.' + self.selectors.caret_down).attr('class', self.selectors.caret_up);
		$(self.activeBtn).addClass('active');
		$(document).trigger('dropdown-opened', content);
	}


	self.setPositioning = function()
	{
		var buttonHeight = $(self.activeBtn).outerHeight();
		$(self.activeContent).css('top', buttonHeight + 'px');
	}

	self.closeDropdowns = function(target)
	{
		if ( $(target).parents(self.dropdownContainer).length === 0 ){
			$(self.dropdownContent).removeClass('active');
			$(self.toggleBtn).removeClass('active');
			$(self.activeBtn).find('.' + self.selectors.caret_up).attr('class', self.selectors.caret_down);
			var content;
			$(document).trigger('dropdown-closed', content);
		}
	}

	return self.bindEvents();
}
var NestedPages = NestedPages || {};
/**
* Modal Windows
* 
* @author Kyle Phillips
* 
* To use, include a modal backdrop and modal content window with the appropriate data-attributes
* The data attributes should match the value of the toggle buttons data-modal-toggle attribute
*/
NestedPages.Modals = function()
{
	var self = this;
	var $ = jQuery;

	self.activeBtn = '';
	self.activeModal = '';
	self.modalOpen = false;

	self.selectors = {
		toggleBtn : '[data-nestedpages-modal-toggle]',
		backdrop : '[data-nestedpages-modal-backdrop]',
		closeBtn : '[data-nestedpages-modal-close]',
		title : 'data-nestedpages-modal-title',
		content : 'data-nestedpages-modal-content'
	}

	self.bindEvents = function()
	{
		$(document).on('click', self.selectors.toggleBtn, function(e){
			e.preventDefault();
			self.activeBtn = $(this);
			self.openModal();
		});
		$(document).on('click', self.selectors.closeBtn, function(e){
			e.preventDefault();
			self.closeModals();
		});
		$(document).on('open-modal-manual', function(e, modal){
			self.activeModal = $('[data-nestedpages-modal="' + modal + '"]');
			self.openModal();
		});
		$(document).on('close-modal-manual', function(e){
			self.closeModals();
		});
		$(document).on('click', self.selectors.backdrop, function(e){
			self.closeModals();
		});
		$(document).ready(function(){
			self.checkHash();
		});
	}

	/**
	* Open the Modal Window
	*/
	self.openModal = function()
	{
		if ( self.modalOpen ){
			self.closeModals();
			return;
		}
		if ( $(self.activeBtn).length > 0 ){
			var modal = $(self.activeBtn).attr('data-nestedpages-modal-toggle');
			self.activeModal = $('*[data-nestedpages-modal="' + modal + '"]');
		}
		$(self.activeModal).addClass('active');
		self.modalOpen = true;
		self.populateModal();
		$(document).trigger('open-modal', [self.activeBtn, self.activeModal]);
	}

	/**
	* Close the Modal Window
	*/
	self.closeModals = function()
	{
		self.modalOpen = false;
		$('[data-nestedpages-modal]').removeClass('active');
		self.activeModal = '';
		self.activeBtn = '';
	}

	/**
	* Populate the Modal if needed
	*/
	self.populateModal = function()
	{
		var title = $(self.activeBtn).attr(self.selectors.title);
		if ( typeof title !== 'undefined' && title !== '' ){
			$(self.activeModal).find('[data-nestedpages-modal-title]').text(title);
		}
		var content = $(self.activeBtn).attr(self.selectors.content);
		if ( typeof content !== 'undefined' && content !== '' ){
			$(self.activeModal).find('[data-nestedpages-modal-content]').html(content);
		}
	}

	/**
	* Check for Hash
	*/
	self.checkHash = function()
	{
		if ( !window.location.hash ) return;
		var hashType = window.location.hash.substring(0, 6);
		if ( hashType !== '#modal' ) return;
		
		var modalId = window.location.hash.substring(7);
		self.activeModal = $('*[data-nestedpages-modal="' + modalId + '"]');
		self.openModal();
	}

	return self.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Check All functionality for Nested Pages
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.CheckAll = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.activeCheckbox = "";

	plugin.selectors = {
		checkbox : '[data-np-check-all]',
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', plugin.selectors.checkbox, function(){
			plugin.activeCheckbox = $(this);
			plugin.toggleCheckboxes();
		});
		$(document).on('change', 'input[type=checkbox]', function(){
			plugin.checkAllStatus($(this));
		});
	}

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.toggleCheckboxes = function()
	{
		var checked = ( $(plugin.activeCheckbox).is(':checked') ) ? true : false;
		var name = $(plugin.activeCheckbox).attr('data-np-check-all');

		var checkboxes = $('*[name="' + name + '"]');
		$.each(checkboxes, function(){
			var row = $(this).parents(NestedPages.selectors.rows);
			$(this).prop('checked', checked);
			// Uncheck any hidden checkboxes
			if ( $(row).hasClass('np-hide') && !$(row).is(':visible') ) {
				$(row).find(NestedPages.selectors.bulkActionsCheckbox).prop('checked', false)
			};
		});

		plugin.toggleCheckAll();
	}

	/**
	* Toggle the "Partial" class for the checkall checkbox
	*/
	plugin.toggleCheckAll = function()
	{
		var name = $(plugin.activeCheckbox).attr('data-np-check-all');
		
		var checkboxes_total = $('*[name="' + name + '"]').length;
		var hidden_checkboxes = $('.np-hide').find(NestedPages.selectors.bulkActionsCheckbox).length;
		var hidden_checkboxes_visible = $('.np-hide:visible').find(NestedPages.selectors.bulkActionsCheckbox).length;

		checkboxes_total = ( checkboxes_total - hidden_checkboxes ) + hidden_checkboxes_visible;
		
		var checkboxes_checked = $('*[name="' + name + '"]:checked').length;

		if ( checkboxes_total == checkboxes_checked ){
			$(plugin.activeCheckbox).prop('checked', true);
			$(plugin.activeCheckbox).removeClass('check-all-partial');
			return;
		}
		if ( checkboxes_checked > 0 ){
			$(plugin.activeCheckbox).addClass('check-all-partial');
			return;
		}
		$(plugin.activeCheckbox).attr('checked', false);
		$(plugin.activeCheckbox).removeClass('check-all-partial');
	}

	plugin.checkAllStatus = function(checkbox)
	{
		var name = $(checkbox).attr('name');
		var toggleAllCheckbox = $('*[data-np-check-all="' + name + '"]');
		if ( toggleAllCheckbox.length === 0 ) return;
		plugin.activeCheckbox = $(toggleAllCheckbox)[0];
		plugin.toggleCheckAll();
	}

	return plugin.init();
}
var NestedPages = NestedPages || {};

/**
* Bulk Actions for Nested View
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.BulkActions = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectedNumber = 0;
	plugin.selectedLinks = [];
	plugin.selectedPosts = []; // array

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', NestedPages.selectors.bulkActionsCheckbox, function(){
			plugin.toggleBulkForm();
		});
		$(document).on('submit', NestedPages.selectors.bulkActionsForm, function(e){
			if ( $('select[name=np_bulk_action]').val() === 'edit' ){
				e.preventDefault();
				plugin.toggleBulkEdit(true);
			}
		});
		$(document).on('click', NestedPages.selectors.bulkEditRemoveItem, function(e){
			e.preventDefault();
			var id = $(this).siblings('input[type=hidden]').val();
			plugin.uncheckBulkItem(id);
		});
		$(document).on('click', NestedPages.selectors.bulkEditCancel, function(e){
			e.preventDefault();
			plugin.uncheckAllBulkItems();
		});
	}

	/**
	* Toggle the Bulk Actions Form & Populate the Hidden ID Fields for posts and redirects
	*/
	plugin.toggleBulkForm = function()
	{
		var checked = false;
		var checked_ids = '';
		var checked_redirect_ids = '';
		plugin.selectedPosts = [];
		plugin.selectedLinks = [];
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).is(':checked') ) {
				var row = $(this).parents(NestedPages.selectors.rows);
				checked = true;
				if ( !$(this).parent('div').hasClass('np-check-all') && !$(row).hasClass('post-type-np-redirect') ) checked_ids += $(this).val() + ',';
				if ( $(row).hasClass('post-type-np-redirect') ) {
					checked_redirect_ids += $(this).val() + ',';
					plugin.selectedLinks.push($(this).val());
				}
				if ( $(this).attr('data-np-post-type') !== 'np-redirect' && !$(this).parent('div').hasClass('np-check-all') ){
					var post = [];
					post['title'] = $(this).attr('data-np-bulk-checkbox');
					post['id'] = $(this).val();
					plugin.selectedPosts.push(post);
				}
			}
		});
		plugin.setBulkEditPosts();
		plugin.toggleEditOption();
		plugin.toggleLinkCountAlert();
		if ( checked ){
			$(NestedPages.selectors.bulkActionsForm).show();
			$(NestedPages.selectors.bulkActionsIds).val(checked_ids);
			$(NestedPages.selectors.bulkActionRedirectIds).val(checked_redirect_ids);
			plugin.setSelectedNumber();
			return;
		}
		$(NestedPages.selectors.bulkActionsIds).val('');
		$(NestedPages.selectors.bulkActionsForm).hide();
	}

	/**
	* Set the Posts for Bulk Edit
	*/
	plugin.setBulkEditPosts = function()
	{
		var html = '';
		for ( var i = 0; i < plugin.selectedPosts.length; i++ ){
			html += '<li><a href="#" class="np-remove" data-np-remove-bulk-item>&times;</a>';
			html += plugin.selectedPosts[i].title;
			html += '<input type="hidden" name="post_ids[]" value="' + plugin.selectedPosts[i].id + '"></li>';
		}
		$(NestedPages.selectors.bulkEditTitles).html(html);
	}

	/**
	* Uncheck a bulk item
	*/
	plugin.uncheckBulkItem = function(id)
	{
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).val() == id ) {
				$(this).prop('checked', false).change();
			}
		});
		// Hide the form if all posts are removed
		if ( $(NestedPages.selectors.bulkEditRemoveItem).length === 0 ){
			plugin.toggleBulkEdit(false);
		}
	}

	/**
	* Uncheck all bulk items
	*/
	plugin.uncheckAllBulkItems = function()
	{
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			$(this).prop('checked', false).change();
		});
		plugin.toggleBulkEdit(false);
	}

	/**
	* Set the number of total selected
	*/
	plugin.setSelectedNumber = function()
	{
		var checkedLength = $(NestedPages.selectors.bulkActionsCheckbox + ':checked').not('.np-check-all input').length;
		var option = $(NestedPages.selectors.bulkActionsForm).find('select option').first();
		$(option).text(nestedpages.bulk_actions + ' (' + checkedLength + ')');
	}

	/**
	* Toggle the edit option to disabled if no post checkboxes are checked
	* Prevents opening the bulk edit form with only np-redirects checked
	*/
	plugin.toggleEditOption = function()
	{
		var checkedLength = $(NestedPages.selectors.bulkActionsCheckbox + ':checked').not('.np-check-all input').not('.np-redirect-bulk').length;
		var option = $(NestedPages.selectors.bulkActionsForm).find('select option[value=edit]');
		if ( checkedLength === 0 ){
			$(option).prop('disabled', true);
			$(NestedPages.selectors.bulkActionsForm).find('select option').first().prop('selected', true);
			plugin.toggleBulkEdit(false);
			return;
		}
		$(option).prop('disabled', false);
	}

	/**
	* Toggle the bulk edit form
	*/
	plugin.toggleBulkEdit = function(visible)
	{
		plugin.toggleLinkCountAlert();
		if ( visible ){
			plugin.disableParentOptions();
			$(NestedPages.selectors.bulkEditForm).show();
			$(NestedPages.selectors.bulkActionsForm).hide();
			plugin.setWPSuggest();
			return;
		}
		$(NestedPages.selectors.bulkEditForm).hide();
		$(NestedPages.selectors.bulkActionsForm).show();
		$(NestedPages.selectors.bulkEditLinkCount).parent('div').hide();
		$(NestedPages.selectors.bulkActionsForm).find('select option').first().text(nestedpages.bulk_actions);
		plugin.resetBulkEditFields();
	}

	/**
	* Toggle the bulk edit link count alert
	*/
	plugin.toggleLinkCountAlert = function()
	{
		var selectedLinkCount = plugin.selectedLinks.length;
		if ( selectedLinkCount === 0 ) {
			$(NestedPages.selectors.bulkEditLinkCount).parent('div').hide();
			return;
		}
		$(NestedPages.selectors.bulkEditLinkCount).parent('div').show();
	}

	/**
	* Set parent options to disabled for selected posts
	*/
	plugin.disableParentOptions = function()
	{
		var selectElement = $(NestedPages.selectors.bulkEditForm).find('select[name=post_parent]');
		for ( var i = 0; i < plugin.selectedPosts.length; i++ )
		{
			$(selectElement).find('option[value=' + plugin.selectedPosts[i].id + ']').attr('disabled', true);
		}
	}

	/**
	* Initialize WP Auto Suggest on Flat Taxonomy fields
	*/
	plugin.setWPSuggest = function()
	{
		var tagfields = $(NestedPages.selectors.bulkEditForm).find('[data-autotag]');
		$.each(tagfields, function(i, v){
			var taxonomy = $(this).attr('data-taxonomy');
			$(this).suggest(ajaxurl + '?action=ajax-tag-search&tax=' + taxonomy , {multiple:true, multipleSep: ","});
		});
	}

	/**
	* Clear out the bulk edit fields
	*/
	plugin.resetBulkEditFields = function()
	{
		var selectFields = $(NestedPages.selectors.bulkEditForm).find('select');
		$.each(selectFields, function(){
			$(this).find('option').first().prop('selected', true);
			$(this).find('option').removeAttr('disabled');
		});
		var categoryChecklists = $(NestedPages.selectors.bulkEditForm).find('.cat-checklist');
		$.each(categoryChecklists, function(){
			$(this).find('input[type=checkbox]').prop('checked', false);
		});
	}

	return plugin.init();
}
var NestedPages = NestedPages || {};

/**
* The Hidden Item Count for selected items in the Nested View
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.HiddenItemCount = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', NestedPages.selectors.bulkActionsCheckbox, function(){
			plugin.toggleHiddenCount();
		});
		$(document).on('click', NestedPages.selectors.toggleAll, function(){
			plugin.toggleHiddenCount();
		});
		$(document).on('click', NestedPages.selectors.pageToggle, function(){
			plugin.toggleHiddenCount();
		});
	}

	/**
	* Toggle the Hidden Count
	*/
	plugin.toggleHiddenCount = function()
	{
		var hiddenCount = 0;
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).is(':checked') ) {
				if ( $(this).parent('div').hasClass('np-check-all') ) return;
				var row = $(this).closest('.page-row');
				if ( !$(row).is(':visible') ) hiddenCount++;
			}
		});
		if ( hiddenCount < 1 ){
			$(NestedPages.selectors.hiddenItemCountParent).hide();
			return;
		}
		$(NestedPages.selectors.hiddenItemCount).text(hiddenCount);
		$(NestedPages.selectors.hiddenItemCountParent).show();
	}

	return plugin.init();
}
var NestedPages = NestedPages || {};

/**
* Toggles Menu Elements
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MenuToggle = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.childToggleLink, function(e){
			e.preventDefault();
			plugin.toggleSingleMenu($(this));
		});
		$(document).on('click', NestedPages.selectors.toggleAll, function(e){
			e.preventDefault();
			plugin.toggleAllMenus();
		});
	}


	// Toggle individual submenus
	plugin.toggleSingleMenu = function(button)
	{
		var submenu = $(button).parent(NestedPages.selectors.childToggle).parent(NestedPages.selectors.row).siblings('ol');
		$(button).toggleClass('open');
		$(submenu).toggle();
		plugin.formatter.setBorders();
		plugin.formatter.setNestedMargins();
		plugin.syncUserToggles();
	}


	// Toggle All Submenus
	plugin.toggleAllMenus = function()
	{
		var button = NestedPages.selectors.toggleAll;
		if ( $(button).attr('data-toggle') === 'closed' ){
			$(NestedPages.selectors.lists).show();
			$(button).attr('data-toggle', 'opened').text(NestedPages.jsData.collapseText);
			$(NestedPages.selectors.childToggle + ' a').addClass('open');
			plugin.formatter.setBorders();
			plugin.syncUserToggles();
			return;
		}
		
		$(NestedPages.selectors.lists).not($(NestedPages.selectors.lists)[0]).hide();
		$(button).attr('data-toggle', 'closed').text(NestedPages.jsData.expandText);
		$(NestedPages.selectors.childToggle + ' a').removeClass('open');
		plugin.formatter.setBorders();
		plugin.syncUserToggles();
	}


	// Get an array of visible rows
	plugin.visibleRowIDs = function()
	{
		var visible_ids = [];
		var visible = $(NestedPages.selectors.rows + ':visible');
		$.each(visible, function(i, v){
			var id = $(this).attr('id');
			visible_ids.push(id.replace("menuItem_", ""));
		});
		return visible_ids;
	}


	// Save the user's toggled menus
	plugin.syncUserToggles = function()
	{
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncToggles,
				nonce : NestedPages.jsData.nonce,
				ids : plugin.visibleRowIDs(),
				posttype : NestedPages.jsData.posttype
			},
			success: function(data){
				if ( data.status !== 'success' ){
					console.log('There was an error saving toggled pages.');
				}
			}
		});
	}


	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Toggle Page Rows
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.PageToggle = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.activeButton = '';

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.toggleHidden, function(e){
			e.preventDefault();
			plugin.activeButton = $(this);
			plugin.toggleHidden();
		});
		$(document).on('click', NestedPages.selectors.toggleStatus, function(e){
			e.preventDefault();
			plugin.toggleStatus($(this));
		});
	}


	// Toggle Hidden Pages
	plugin.toggleHidden = function()
	{
		var button = $(plugin.activeButton);
		var action = $(button).attr('href');

		if ( action == '#show' ){
			$(button).attr('href', '#hide').text(NestedPages.jsData.showHiddenText);
			$(NestedPages.selectors.hiddenRows).removeClass('shown').hide();
			plugin.formatter.updateSubMenuToggle();
			plugin.formatter.setBorders();
			return;
		}

		if ( action == '#hide' ){
			$(button).attr('href', '#show').text(NestedPages.jsData.hideHiddenText);
			$(NestedPages.selectors.hiddenRows).addClass('shown').show();
			plugin.formatter.updateSubMenuToggle();
			plugin.formatter.setBorders();
		}
	}


	// Toggle Pages based on status
	plugin.toggleStatus = function(button)
	{
		var target = $(button).attr('href');
		target = target.substring(1);
		$(NestedPages.selectors.syncCheckbox).attr('disabled', false);
		$(NestedPages.selectors.toggleStatus).removeClass('active');
		$(button).addClass('active');
		if ( target == 'draft' ){
			$(NestedPages.selectors.syncCheckbox).attr('disabled', true);
			$('.' + target).addClass('nested-visible');
		}
		if ( target == 'all' ){
			$(NestedPages.selectors.rows).show();
			return;
		}
		$(NestedPages.selectors.rows).hide();
		$('.' + target).show();
		return;
	}


	return plugin.init();

}
var NestedPages = NestedPages || {};

/**
* Enables and Saves Nesting
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Nesting = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;

	// Make the Menu sortable
	plugin.initializeSortable = function()
	{
		if ( !NestedPages.jsData.nestable ) return plugin.initializeSortableFlat();

		$(NestedPages.selectors.sortable).not(NestedPages.selectors.notSortable).nestedSortable({
			items : NestedPages.selectors.rows,
			toleranceElement: '> .row',
			handle: NestedPages.selectors.handle,
			placeholder: "ui-sortable-placeholder",
			tabSize : 56,
			isAllowed: function(placeholder, placeholderParent, currentItem){
				return ( $(placeholderParent).hasClass('post-type-np-redirect') && !$(currentItem).hasClass('post-type-np-redirect') ) ? false : true;
			},
			start: function(e, ui){
				ui.placeholder.height(ui.item.height());
			},
			sort: function(e, ui){
				plugin.formatter.updatePlaceholderWidth(ui);
			},
			stop: function(e, ui){
				setTimeout(
					function(){
						plugin.formatter.updateSubMenuToggle();
						plugin.formatter.setBorders();
						plugin.formatter.setNestedMargins();
					}, 100
				);
				plugin.syncNesting();
			},
		});
	}

	// Initialize Flat Sortable (Non-Hierarchical Post Types)
	plugin.initializeSortableFlat = function()
	{
		var lists = $(NestedPages.selectors.lists).not(NestedPages.selectors.notSortable);
		$.each(lists, function(){
			$(this).sortable({
				items : '>' + NestedPages.selectors.rows,
				handle: NestedPages.selectors.handle,
				placeholder: "ui-sortable-placeholder",
				forcePlaceholderSize: true,
				start: function(e, ui){
					ui.placeholder.height(ui.item.height());
				},
				sort: function(e, ui){
					plugin.formatter.updatePlaceholderWidth(ui);
				},
				stop: function(e, ui){
					setTimeout(
						function(){
							plugin.formatter.updateSubMenuToggle();
							plugin.formatter.setBorders();
							plugin.formatter.setNestedMargins();
						}, 100
					);
					plugin.syncNesting();
				},
			});
		});
	}

	// Disable Nesting
	plugin.disableNesting = function()
	{
		$(NestedPages.selectors.sortable).sortable('destroy');
	}


	// Sync Nesting
	plugin.syncNesting = function(manual, callback)
	{
		var list;

		if ( nestedpages.manual_order_sync === '1' && !manual) return;
		$(NestedPages.selectors.errorDiv).hide();
		$(NestedPages.selectors.loadingIndicator).show();
		if ( NestedPages.jsData.nestable ){
			list = $(NestedPages.selectors.sortable).nestedSortable('toHierarchy', {startDepthCount: 0});
		} else {
			list = plugin.setNestingArray();
		}
		
		plugin.disableNesting();

		var syncmenu = NestedPages.jsData.syncmenu;
		if ( nestedpages.manual_menu_sync === '1' ) syncmenu = 'nosync';

		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncNesting,
				nonce : NestedPages.jsData.nonce,
				list : list,
				post_type : NestedPages.jsData.posttype,
				syncmenu : syncmenu
			},
			success: function(data, callback){
				plugin.initializeSortable();
				if (data.status === 'error'){
					$(NestedPages.selectors.errorDiv).text(data.message).show();
					$(NestedPages.selectors.loadingIndicator).hide();
				} else {
					if ( callback && typeof callback === 'function') {
						callback();
						return;
					}
					$(NestedPages.selectors.loadingIndicator).hide();
				}
			}
		});
	}

	plugin.setNestingArray = function(list)
	{
		ret = [];
		$(NestedPages.selectors.lists).first().children('li.page-row').each(function() {
			var level = plugin.recursiveNesting(this);
			ret.push(level);
		});
		return ret;
	}

	plugin.recursiveNesting = function(item) {
		var id = $(item).attr('id');
		var currentItem;
		if (id) {
			id = id.replace('menuItem_', '');
			currentItem = {
				"id": id
			};
			if ($(item).children(NestedPages.selectors.lists).children(NestedPages.selectors.rows).length > 0) {
				currentItem.children = [];
				$(item).children(NestedPages.selectors.lists).children(NestedPages.selectors.rows).each(function() {
					var level = plugin.recursiveNesting(this);
					currentItem.children.push(level);
				});
			}
			return currentItem;
		}
	}
}
var NestedPages = NestedPages || {};

/**
* Sync the "sync menu" setting
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.SyncMenuSetting = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){ // catches trash updates
			if ( nestedpages.manual_menu_sync === '1' ) return;
			if ( nestedpages.syncmenu === '1' ) plugin.syncSetting(); 
		});
		$(document).on('change', NestedPages.selectors.syncCheckbox, function(){
			plugin.syncSetting();
		});
	}

	// Sync the "Sync menu" preference / setting
	plugin.syncSetting = function()
	{

		if ( NestedPages.jsData.posttype !== 'page' ) return;
		if ($(NestedPages.selectors.syncCheckbox).length === 0) return;
		
		NestedPages.jsData.syncmenu = ( $(NestedPages.selectors.syncCheckbox).is(':checked') ) ? 'sync' : 'nosync';

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncMenu,
				nonce : NestedPages.jsData.nonce,
				post_type : NestedPages.jsData.posttype,
				syncmenu : NestedPages.jsData.syncmenu
			},
			success: function(data){
				if (data.status === 'error'){
					plugin.formatter.showAjaxError(data.message);
				}
			},
			error: function(data){
				console.log(data);
			}
		});
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Add new post(s) - Top level & child
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.NewPost = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.parent_id = 0; // Parent ID for the post(s) to add
	plugin.posts = ''; // The newly added posts
	plugin.form = ''; // The active form

	plugin.bindEvents = function()
	{
		$(document).on('open-modal', function(e, button, modal){
			var target = $(button).attr('data-nestedpages-modal-toggle');
			if ( typeof target !== 'undefined' && target == 'np-bulk-modal' ){
				plugin.openModal();
			}
		});
		$(document).on('submit', NestedPages.selectors.newPageForm, function(e){
			e.preventDefault();
		});
		$(document).on('click', NestedPages.selectors.newPageSubmitButton, function(e){
			e.preventDefault();
			plugin.submitForm($(this));
		});
		$(document).on('click', NestedPages.selectors.newPageTitle, function(e){
			e.preventDefault();
			plugin.addTitleField($(this));
		});
		$(document).on('click', NestedPages.selectors.newPageRemoveTitle, function(e){
			e.preventDefault();
			plugin.removeTitleField($(this));
		});
		$(document).on('click', NestedPages.selectors.addChildButton, function(e){
			e.preventDefault();
			plugin.openQuickEdit($(this));
		});
		$(document).on('click', NestedPages.selectors.cancelNewChildButton, function(e){
			e.preventDefault();
			plugin.cancelNewPage();
		});
		$(document).on('click', '[' + NestedPages.selectors.newBeforeButton + ']', function(e){
			e.preventDefault();
			plugin.openQuickEdit($(this));
		});
		$(document).on('click', '[' + NestedPages.selectors.newAfterButton + ']', function(e){
			e.preventDefault();
			plugin.openQuickEdit($(this));
		});
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) {
				plugin.cancelNewPage();
				$(document).click(); // Close Dropdown
			}
		});
	}

	// Open the form modal
	plugin.openModal = function()
	{
		var newform = $(NestedPages.selectors.newPageFormContainer).clone().find(NestedPages.selectors.newPageForm);
		$(newform).addClass('in-modal');
		$(NestedPages.selectors.newPageModal).find('.modal-body').html(newform);
		$(NestedPages.selectors.newPageModal).find('h3').text(nestedpages.add_multiple);
		$(NestedPages.selectors.newPageModal).find('.page_parent_id').val(plugin.parent_id);
		$(newform).find('.np_title').first().focus();
		$(newform).find(NestedPages.selectors.newPageTitle).first().prop('tabindex', '2');
	}

	// Open the new child quick edit
	plugin.openQuickEdit = function(button)
	{
		var before = $(button).attr(NestedPages.selectors.newBeforeButton);
		before = ( typeof before === 'undefined' || before === '' ) ? false : before;

		var after = $(button).attr(NestedPages.selectors.newAfterButton);
		after = ( typeof after === 'undefined' || after === '' ) ? false : after;

		var parent_li = $(button).closest(NestedPages.selectors.row).parent('li');
		var newform = $(NestedPages.selectors.newPageFormContainer).clone();

		// Append the form to the list item
		if ( $(parent_li).children('ol').length > 0 ){
			var child_ol = $(parent_li).children('ol');
			$(newform).insertBefore(child_ol);
		} else {
			$(newform).appendTo(parent_li);
		}


		$(newform).siblings(NestedPages.selectors.row).hide();

		plugin.formatter.showQuickEdit();

		$(newform).find('.parent_name').html('<em>Parent:</em> ' + $(button).attr('data-parentname'));
		if ( !before && !after ) $(newform).find('.page_parent_id').val($(button).attr('data-id'));

		if ( before ) {
			$(newform).find('.page_before_id').val(before);
			$(newform).find('[data-new-post-relation-title]').text(nestedpages.insert_before + ': ' + $(button).attr('data-parentname'));
		}
		if ( after ) {
			$(newform).find('.page_after_id').val(after);
			$(newform).find('[data-new-post-relation-title]').text(nestedpages.insert_after + ': ' + $(button).attr('data-parentname'));
		}

		$(newform).show();
		$(newform).find('.np_title').focus();
		$(newform).find(NestedPages.selectors.newPageTitle).prop('tabindex', '2');
	}

	// Close the form modal
	plugin.cancelNewPage = function()
	{
		plugin.formatter.removeQuickEdit();
		$(NestedPages.selectors.newChildError).hide();
		$(NestedPages.selectors.newPageModal).find('.modal-body').empty();
		$(NestedPages.selectors.sortable).find('.new-child').remove();
		$(NestedPages.selectors.row).show();
	}

	// Add a page title field to the form
	plugin.addTitleField = function(button)
	{		
		var form = $(button).parents('form');
		var fieldcount = $(button).siblings('.new-page-titles').children('li').length + 1;
		var html = '<li><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg><div class="form-control new-child-row"><label>' + NestedPages.jsData.titleText + '</label><div><input type="text" name="post_title[]" class="np_title" placeholder="' + NestedPages.jsData.titleText + '" value="" tabindex="' + fieldcount + '" /><a href="#" class="button-secondary np-remove-child">-</a></div></div></li>';
		var container = $(button).siblings('.new-page-titles').append(html);
		$(form).find('.np_title').last().focus();
		$(form).find(NestedPages.selectors.newPageTitle).prop('tabindex', fieldcount++);
		$('.new-page-titles').sortable({
			items : 'li',
			handle: '.handle',
		});
		plugin.toggleAddEditButton(form);
	}

	// Remove a page title field
	plugin.removeTitleField = function(button)
	{
		var form = $(button).parents('form');
		$(button).parents('.new-child-row').parent('li').remove();
		plugin.toggleAddEditButton(form);
	}

	// Submit the New Page Form
	plugin.submitForm = function(button)
	{
		plugin.toggleLoading(true);
		plugin.form = $(button).parents('form');

		var addedit = ( $(button).hasClass('add-edit') ) ? true : false;
		var action = NestedPages.formActions.newPage;
		if ( $(plugin.form).find('.page_before_id').val() !== '' ) action = NestedPages.formActions.newBeforeAfter;
		if ( $(plugin.form).find('.page_after_id').val() !== '' ) action = NestedPages.formActions.newBeforeAfter;
		
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: $(plugin.form).serialize() + '&action=' + action + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
			success: function(data){
				if (data.status === 'error'){
					plugin.toggleLoading(false);
					$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
					return;
				}
				if ( addedit === true ){ // Redirect to Edit Screen
					var link = data.new_pages[0].edit_link;
					link = link.replace(/&amp;/g, '&');
					window.location.replace(link);
					return;
				}
				plugin.toggleLoading(false);
				plugin.posts = data.new_pages;
				plugin.addPosts();
			},
			error: function(data){
				console.log(data);
				plugin.toggleLoading(false);
				$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text('The form could not be saved at this time.').show();
			}
		});
	}

	// Add the new posts
	plugin.addPosts = function()
	{
		// Before/After ID if applicable
		var before = $(plugin.form).find('.page_before_id').val();
		before = ( before !== '' ) ? before : false;
		var after = $(plugin.form).find('.page_after_id').val();
		after = ( after !== '' ) ? after : false;

		var parent_li = $(plugin.form).parent('.new-child').parent('.page-row');
		
		// If parent li doesn't have a child ol, add one
		if ( $(parent_li).children('ol').length === 0 && !before && !after ){
			$(parent_li).append('<ol class="nplist"></ol>');
		}

		if ( $(plugin.form).hasClass('in-modal') ){
			var appendto = $('.nplist.sortable li.page-row:first');
		} else {
			var appendto = $(parent_li).children('ol');
		}

		for (i = 0; i < plugin.posts.length; i++){
			plugin.appendRows(appendto, plugin.posts[i], before, after);
		}

		// Show the child page list and reset submenu toggles
		if ( !before && !after ){
			$(appendto).show();
		}

		plugin.formatter.updateSubMenuToggle();
		plugin.formatter.setNestedMargins();
		plugin.cancelNewPage();
		$(document).trigger('close-modal-manual');
	}

	// Append new post rows to the nested view
	plugin.appendRows = function(appendto, post, before, after)
	{
		var html = '<li id="menuItem_' + post.id + '" class="page-row';
		if ( post.status === 'publish' ) html += ' published';
		html += '">';

		if ( NestedPages.jsData.hierarchical ){
			html += '<div class="row">';
			html += '<div class="child-toggle"></div>';
		} else {
			html += '<div class="row non-hierarchical">';
			html += '<div class="non-hierarchical-spacer"></div>';
		}

		html += '<div class="row-inner">';
		// Submenu
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-sub-menu"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M19 15l-6 6-1.42-1.42L15.17 16H4V4h2v10h9.17l-3.59-3.58L13 9l6 6z" class="arrow" /></svg>';
		// Handle
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>';
		html += '<a href="' + post.edit_link + '" class="page-link page-title">';
		html += '<span class="title">' + post.title + '</span>';
		
		// Status
		if ( post.status !== 'Publish' ){
			html += '<span class="status">(' + post.status + ')</span>';
		} else {
			html += '<span class="status"></span>';
		}

		// Nav Status
		html += '<span class="nav-status">';
		if ( post.np_nav_status === 'hide' ){
			html += ' ' + nestedpages.hidden;
		}
		html += '</span>';

		html += '<span class="edit-indicator">Edit</span>';
		html += '</a>';

		// Non-Hierarchical Data
		if ( !NestedPages.jsData.hierarchical ){
			html += '<div class="np-post-columns">';
			html += '<ul class="np-post-info">';
			html += '<li><span class="np-author-display">' + post.author_formatted + '</span></li>';
			html += '<li>' + post.date_formatted + '</li>';
			html += '</ul>';
			html += '</div>';
		}

		// Yoast
		if ( $('.nplist').first().hasClass('has-yoast') ) {
			html += '<span class="np-seo-indicator na"></span>';
		}

		// Action Buttons
		html += '<div class="action-buttons">';
		html += '<div class="nestedpages-dropdown" data-dropdown><a href="#" class="np-btn has-icon toggle" data-dropdown-toggle><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg></a><ul class="nestedpages-dropdown-content" data-dropdown-content>';
		// Add Link
		html += '<li><a href="#" class="open-redirect-modal" data-parentid="' + post.id + '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>' + nestedpages.add_link + '</a></li>';
		// Add Child
		html += '<li><a href="#" class="add-new-child" data-id="' + post.id + '" data-parentname="' + post.title + '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M3 21h18v-2H3v2zM3 8v8l4-4-4-4zm8 9h10v-2H11v2zM3 3v2h18V3H3zm8 6h10V7H11v2zm0 4h10v-2H11v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>' + nestedpages.add_child_short + '</a></li>';
		html += '</ul></div>';
		
		// Quick Edit (data attrs)
		html += '<a href="#" class="np-btn np-quick-edit" data-id="' + post.id + '" data-template="' + post.page_template + '" data-title="' + post.title + '" data-slug="' + post.slug + '" data-commentstatus="closed" data-status="' + post.status.toLowerCase() + '" data-np-status="show"	data-navstatus="show" data-author="' + post.author + '" data-template="' + post.template + '" data-month="' + post.month + '" data-day="' + post.day + '" data-year="' + post.year + '" data-hour="' + post.hour + '" data-minute="' + post.minute + '" data-datepicker="' + post.datepicker + '" data-time="' + post.time + '" data-formattedtime="' + post.formattedtime + '" data-ampm="' + post.ampm + '">' + nestedpages.quick_edit + '</a>';

		html += '<a href="' + post.view_link + '" class="np-btn" target="_blank">' + nestedpages.view + '</a>';

		// Trash
		html += '<a href="' + post.delete_link + '" class="np-btn np-btn-trash"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-remove"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="icon"/><path d="M0 0h24v24H0z" fill="none"/></svg></a>';
		html += '</div><!-- .action-buttons -->';

		html += '</div><!-- .row-inner --></div><!-- .row -->';
		html += '</li>';

		if ( before ){
			var row = plugin.findRowById(before);
			$(html).insertBefore(row);
			return;
		}
		if ( after ){
			var row = plugin.findRowById(after);
			$(html).insertAfter(row);
			return;
		}

		$(appendto).append(html);
	}

	// Find the row for inserting before/after
	plugin.findRowById = function(id)
	{
		var row = $(NestedPages.selectors.rows + '#menuItem_' + id);
		return row;
	}

	// Toggle the "Add & Edit" & "Add" buttons depending on row count
	plugin.toggleAddEditButton = function(form)
	{
		var titleCount = $(form).find('.np_title').length;
		if ( titleCount < 1 ){
			$(NestedPages.selectors.newPageSubmitButton).hide();
			return;
		}
		$(NestedPages.selectors.newPageSubmitButton).show();
		if ( titleCount > 1 ){
			$(NestedPages.selectors.newPageSubmitButton + '.add-edit').hide()
			return;
		}
		$(NestedPages.selectors.newPageSubmitButton + '.add-edit').show()
	}

	// Toggle the form loading state
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(NestedPages.selectors.quickEditErrorDiv).hide();
			$(NestedPages.selectors.newPageSubmitButton).attr('disabled', 'disabled');
			$(NestedPages.selectors.quickEditLoadingIndicator).show();
			return;
		}
		$(NestedPages.selectors.newPageSubmitButton).attr('disabled', false);
		$(NestedPages.selectors.quickEditLoadingIndicator).hide();
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Quick Edit functionality for posts
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.QuickEditPost = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.button = ''; // The quick edit button
	plugin.initialData = ''; // The unedited post data
	plugin.parent_li = ''; // The post's nested pages list element
	plugin.form = ''; // The newly created form
	plugin.flatTerms = ''; // Object containing flat taxonomy IDs
	plugin.termNames = ''; // Flat Taxonomy Term Names
	plugin.saveButton = ''; // Save button
	plugin.newData = ''; // New Data, after save
	plugin.row = ''; // The row being edited

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.quickEditOpen, function(e){
			e.preventDefault();
			plugin.button = $(this);
			plugin.openForm();
		});
		$(document).on('click', NestedPages.selectors.quickEditCancel, function(e){
			e.preventDefault();
			plugin.formatter.removeQuickEdit();
		});
		$(document).on('change', '.keep_private', function(){
			if ( this.checked ){
				$('.post_password').val('').prop('readonly', true);
			} else {
				$('.post_password').prop('readonly', false);
			}
		});
		$(document).on('click', NestedPages.selectors.quickEditSaveButton, function(e){
			e.preventDefault();
			plugin.saveButton = $(this);
			plugin.save();
		});
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) plugin.formatter.removeQuickEdit();
		});
	}


	// Create and open the quick edit form
	plugin.openForm = function()
	{
		plugin.setInitialData();
		plugin.createForm();
		plugin.populateForm();
		plugin.populateFlatTaxonomies();
	}


	// Set the unedited initial data
	plugin.setInitialData = function()
	{
		plugin.initialData = {
			id : $(plugin.button).attr('data-id'),
			title : $(plugin.button).attr('data-title'),
			slug : $(plugin.button).attr('data-slug'),
			author : $(plugin.button).attr('data-author'),
			cs : $(plugin.button).attr('data-commentstatus'),
			status : $(plugin.button).attr('data-status'),
			template : $(plugin.button).attr('data-template'),
			month : $(plugin.button).attr('data-month'),
			day : $(plugin.button).attr('data-day'),
			year : $(plugin.button).attr('data-year'),
			hour : $(plugin.button).attr('data-hour'),
			minute : $(plugin.button).attr('data-minute'),			
			navstatus : $(plugin.button).attr('data-navstatus'),
			npstatus : $(plugin.button).attr('data-np-status'),
			navtitle : $(plugin.button).attr('data-navtitle'),
			navtitleattr : $(plugin.button).attr('data-navtitleattr'),
			navcss : $(plugin.button).attr('data-navcss'),
			linktarget : $(plugin.button).attr('data-linktarget'),
			password : $(plugin.button).attr('data-password'),
			datepicker : $(plugin.button).attr('data-datepicker'),
			time: $(plugin.button).attr('data-formattedtime'),
			timeTwentyFour : $(plugin.button).attr('data-time'),
			ampm: $(plugin.button).attr('data-ampm'),
			timeFormat: $(plugin.button).attr('data-timeformat'),
			sticky: $(plugin.button).attr('data-sticky')
		};

		// Add Custom Fields if Available
		var attrs = $(plugin.button)[0].attributes;
		$.each(attrs, function(i, attr){
			if ( !attr.name.includes('data-npcustom') ) return;
			plugin.initialData[attr.name] = attr.value;
		});

		// Add Array of Taxonomies to the data object using classes applied to the list element
		plugin.initialData.h_taxonomies = [];
		plugin.initialData.f_taxonomies = [];

		plugin.parent_li = $(plugin.button).closest(NestedPages.selectors.row).parent('li');
		var classes = $(plugin.parent_li).attr('class').split(/\s+/);
		for ( i = 0; i < classes.length; i++ ){
			if ( classes[i].substring(0, 3) === 'in-'){
				plugin.initialData.h_taxonomies.push(classes[i]);
			}
			if ( classes[i].substring(0, 4) === 'inf-' ){
				plugin.initialData.f_taxonomies.push(classes[i]);	
			}
		}
	}

	
	// Create the form and append it to the row
	plugin.createForm = function()
	{
		plugin.form = $(NestedPages.selectors.quickEditPostForm).clone();
		if ( $(plugin.parent_li).children('ol').length > 0 ){
			var child_ol = $(plugin.parent_li).children('ol');
			$(plugin.form).insertBefore(child_ol);
		} else {
			$(plugin.form).appendTo(plugin.parent_li);
		}
		$(plugin.form).siblings(NestedPages.selectors.row).hide();
		$(plugin.form).show();
	}


	// Populate the new quick edit form
	plugin.populateForm = function()
	{
		$(plugin.form).find('.page_id').html('<em>ID:</em> ' + plugin.initialData.id);
		$(plugin.form).find('.np_id').val(plugin.initialData.id);
		$(plugin.form).find('.np_title').val(plugin.initialData.title);
		$(plugin.form).find('.np_slug').val(plugin.initialData.slug);
		$(plugin.form).find('.np_author select').val(plugin.initialData.author);
		$(plugin.form).find('.np_status').val(plugin.initialData.status);
		$(plugin.form).find('.np_nav_title').val(plugin.initialData.navtitle);
		$(plugin.form).find('.np_title_attribute').val(plugin.initialData.navtitleattr);
		$(plugin.form).find('.np_nav_css_classes').val(plugin.initialData.navcss);
		$(plugin.form).find('.post_password').val(plugin.initialData.password);
		$(plugin.form).find('.np_publish_date').val(plugin.initialData.datepicker);
		if ( plugin.initialData.cs === 'open' ) $(plugin.form).find('.np_cs').attr('checked', 'checked');

		if ( plugin.initialData.template !== '' ){
			$(plugin.form).find('.np_template').val(plugin.initialData.template);
		} else {
			$(plugin.form).find('.np_template').val('default');
		}

		if ( plugin.initialData.status === 'private' ){
			$(plugin.form).find('.post_password').attr('readonly', true);
			$(plugin.form).find('.keep_private').attr('checked', true);
		}

		if ( plugin.initialData.npstatus === 'hide' ){
			$(plugin.form).find('.nested_pages_status').attr('checked', 'checked');
		} else {
			$(plugin.form).find('.nested_pages_status').removeAttr('checked');
		}
		
		if ( plugin.initialData.navstatus === 'hide' ) {
			$(plugin.form).find('.np_nav_status').attr('checked', 'checked');
		} else {
			$(plugin.form).find('.np_nav_status').attr('checked', false);
		}

		if ( plugin.initialData.linktarget === "_blank" ) {
			$(plugin.form).find('.link_target').attr('checked', 'checked');
		} else {
			$(plugin.form).find('.link_target').attr('checked', false);
		}

		if ( plugin.initialData.status === "private" ) {
			$(plugin.form).find('.np_status').val('publish');
		}

		if ( plugin.initialData.sticky === 'sticky' ){
			$(plugin.form).find('.np-sticky').attr('checked', 'checked');
		} else {
			$(plugin.form).find('.np-sticky').removeAttr('checked');
		}
		
		// Date Fields
		if ( plugin.initialData.timeFormat === 'H:i' ){
			$(plugin.form).find('.np_time').val(plugin.initialData.timeTwentyFour);
		} else {
			$(plugin.form).find('.np_time').val(plugin.initialData.time);
			$(plugin.form).find('.np_ampm').val(plugin.initialData.ampm);
			$(plugin.form).find('select[name="mm"]').val(plugin.initialData.month);
			$(plugin.form).find('input[name="jj"]').val(plugin.initialData.day);
			$(plugin.form).find('input[name="aa"]').val(plugin.initialData.year);
			$(plugin.form).find('input[name="hh"]').val(plugin.initialData.hour);
			$(plugin.form).find('input[name="mn"]').val(plugin.initialData.minute);
		}

		// Custom Fields
		for ( var key in plugin.initialData ){
			if ( !key.includes('npcustom') ) continue;
			if ( plugin.initialData.hasOwnProperty(key) ){
				var inputName = key.replace('data-npcustom-', '');
				inputName = inputName.toLowerCase();
				$(plugin.form).find('[data-np-custom-field="' + inputName + '"]').val(plugin.initialData[key]);
			}
		}

		plugin.populateFlatTaxonomies();

		// Populate Hierarchical Taxonomy Checkboxes
		if ( plugin.initialData.hasOwnProperty('h_taxonomies') ){
			var taxonomies = plugin.initialData.h_taxonomies;
			for ( i = 0; i < taxonomies.length; i++ ){
				var tax = '#' + taxonomies[i];
				$(plugin.form).find(tax).attr('checked', 'checked');
			}
		}

		var datepickers = $(plugin.form).find('.np_datepicker');
		$.each(datepickers, function(){
			var $this = $(this);
			$this.datepicker({
				dateFormat: $this.attr('data-datepicker-format'),
				beforeShow: function(input, inst) {
					$('#ui-datepicker-div').addClass('nestedpages-datepicker');
				}
			});
		});

		plugin.formatter.showQuickEdit();
		$(plugin.form).show();		
	}


	// Populate the flat taxonomies
	plugin.populateFlatTaxonomies = function()
	{
		if ( !plugin.initialData.hasOwnProperty('f_taxonomies') ) return;
		plugin.createTaxonomyObject();
		plugin.getTermNames();
		plugin.setWPSuggest();
	}


	// Create an object of taxonomies from class names
	plugin.createTaxonomyObject = function()
	{
		var out = "";
		var terms = {};
		for ( i = 0; i < plugin.initialData.f_taxonomies.length; i++ ){
			
			// Get the term
			var singleTerm = plugin.initialData.f_taxonomies[i];

			var tax_array = singleTerm.split('-'); // split the string into an array
			var splitter = tax_array.indexOf('nps'); // find the index of the name splitter
			var term = tax_array.splice(splitter + 1); // Splice off the name
			term = term.join('-'); // Join the name back into a string


			// Get the taxonomy
			var tax = singleTerm.split('-').splice(0, splitter);
			tax.shift('inf');
			var taxonomy = tax.join('-');				

			// Add taxonomy array to object
			if ( !(taxonomy in terms) ){
				terms[taxonomy] = [];
			}
			// push term to taxonomy array
			var term_array = terms[taxonomy];
			term_array.push(term);
		}
		plugin.flatTerms = terms;
	}


	// Get the taxonomy names from the ids
	plugin.getTermNames = function()
	{
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data : {
				action : NestedPages.formActions.getTaxonomies,
				nonce : NestedPages.jsData.nonce,
				terms : plugin.flatTerms
			},
			success: function(data){
				plugin.termNames = data.terms;
				plugin.populateFlatTaxonomyFields();
			}
		});
	}


	// Populate the flat taxonomy fields in the form
	plugin.populateFlatTaxonomyFields = function()
	{
		if ( !plugin.termNames ) return;
		$.each(plugin.termNames, function(i, v){
			var textarea = $('#' + i + '-quickedit');
			$(textarea).val(v.join(','));
		});
	}


	// Initialize WP Auto Suggest on Flat Taxonomy fields
	plugin.setWPSuggest = function()
	{
		var tagfields = $(plugin.form).find('[data-autotag]');
		$.each(tagfields, function(i, v){
			var taxonomy = $(this).attr('data-taxonomy');
			$(this).suggest(ajaxurl + '?action=ajax-tag-search&tax=' + taxonomy , {multiple:true, multipleSep: ","});
		});
	}


	// Save the quick edit
	plugin.save = function()
	{
		plugin.toggleLoading(true);

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: $(plugin.form).find('form').serialize() + '&action=' + NestedPages.formActions.quickEditPost + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
			success: function(data){
				if (data.status === 'error'){
					plugin.toggleLoading(false);
					$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
				} else {
					plugin.toggleLoading(false);
					plugin.newData = data.post_data;
					plugin.updatePostRow();
				}
			},
			error: function(data){
				console.log(data);
			}
		});
	}


	// Update the Row after saving quick edit data
	plugin.updatePostRow = function()
	{
		plugin.row = $(plugin.button).parents('.row-inner');
		
		$(plugin.row).find('.title').text(plugin.newData.post_title);
		$(plugin.row).find('.np-view-button').attr('href', plugin.newData.permalink);
		
		var status = $(plugin.row).find('.status');
		if ( (plugin.newData._status !== 'publish') && (plugin.newData._status !== 'future') ){
			var newStatus = nestedpages.post_statuses[plugin.newData._status].label;
			$(status).text('(' + newStatus + ')');
		} else {
			$(status).text('');
		}
		if ( plugin.newData.keep_private === 'private' ){
			$(status).text(nestedpages.private);
		}

		// Password Lock Icon
		if ( plugin.newData.post_password !== "" && typeof plugin.newData.post_password !== 'undefined'){
			var statustext = $(status).text();
			statustext += ' <span class="locked">';
			statustext += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>'
			statustext += '</span>';
			$(status).html(statustext);
		}

		// Hide / Show in Nav
		var nav_status = $(plugin.row).find('.nav-status');
		if ( (plugin.newData.nav_status == 'hide') ){
			$(nav_status).text('(Hidden)');
		} else {
			$(nav_status).text('');
		}

		// Hide / Show in Nested Pages
		var li = $(plugin.row).parent('li');
		if ( (plugin.newData.np_status == 'hide') ){
			$(li).addClass('np-hide');
			$(plugin.row).find('.status').after('<svg class="row-status-icon status-np-hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path class="icon" d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>');
		} else {
			$(li).removeClass('np-hide');
			$(plugin.row).find('.status-np-hidden').remove();
		}

		// Sticky
		var sticky = $(plugin.row).find('.sticky');
		if ( (plugin.newData.sticky == 'sticky') ){
			$(sticky).show();
		} else {
			$(sticky).hide();
		}

		// Author for Non-Hierarchical Types
		if ( !NestedPages.jsData.hierarchical ){
			$(plugin.row).find('.np-author-display').text(plugin.newData.author_name);
		}

		var button = $(plugin.row).find(NestedPages.selectors.quickEditOpen);

		$(button).attr('data-id', plugin.newData.post_id);
		$(button).attr('data-template', plugin.newData.page_template);
		$(button).attr('data-title', plugin.newData.post_title);
		$(button).attr('data-slug', plugin.newData.post_name);
		$(button).attr('data-commentstatus', plugin.newData.comment_status);
		$(button).attr('data-status', plugin.newData._status);
		$(button).attr('data-sticky', plugin.newData.sticky);
		
		// Private Status
		if ( plugin.newData.keep_private === 'private' ) {
			$(button).attr('data-status', 'private');
		}
		
		$(button).attr('data-author', plugin.newData.post_author);
		$(button).attr('data-np-status', plugin.newData.np_status);
		$(button).attr('data-password', plugin.newData.post_password);
		
		$(button).attr('data-navstatus', plugin.newData.nav_status);
		$(button).attr('data-navtitle', plugin.newData.np_nav_title);
		$(button).attr('data-linktarget', plugin.newData.link_target);
		$(button).attr('data-navtitleattr', plugin.newData.np_title_attribute);
		$(button).attr('data-navcss', plugin.newData.np_nav_css_classes);

		$(button).attr('data-month', plugin.newData.mm);
		$(button).attr('data-day', plugin.newData.jj);
		$(button).attr('data-year', plugin.newData.aa);
		$(button).attr('data-hour', plugin.newData.hh);
		$(button).attr('data-minute', plugin.newData.mn);
		$(button).attr('data-datepicker', plugin.newData.np_date);
		$(button).attr('data-time', plugin.newData.np_time);
		$(button).attr('data-formattedtime', plugin.newData.np_time);
		$(button).attr('data-ampm', plugin.newData.np_ampm);

		// Custom Fields
		for ( var key in plugin.newData ){
			if ( !key.includes('np_custom') ) continue;
			if ( plugin.newData.hasOwnProperty(key) ){
				var attrName = key.replace('np_custom_', 'data-npcustom-');
				$(button).attr(attrName, plugin.newData[key]);
			}
		}

		plugin.removeTaxonomyClasses();
		plugin.addCategoryClasses();
		plugin.addHierarchicalClasses();
		plugin.addFlatClasses();
		plugin.addStatusClass();

		plugin.formatter.removeQuickEdit();
		plugin.formatter.flashRow(plugin.row);
	}


	// Add Status Class
	plugin.addStatusClass = function()
	{
		var statuses = ['published', 'draft', 'pending', 'future'];
		for ( i = 0; i < statuses.length; i++ ){
			$(plugin.row).removeClass(statuses[i]);
		}
		$(plugin.row).addClass(plugin.newData._status);
	}


	// Remove Taxonomy Classes from the updated row
	plugin.removeTaxonomyClasses = function()
	{
		taxonomies = [];
		var classes = $(plugin.row).attr('class').split(/\s+/);
		for ( i = 0; i < classes.length; i++ ){
			if ( classes[i].substring(0, 3) === 'in-'){ // hierarchical
				$(plugin.row).removeClass(classes[i]);
			}
			if ( classes[i].substring(0, 4) === 'inf-'){ // flat
				$(plugin.row).removeClass(classes[i]);
			}
		}
	}


	// Add Category Classes to the Row
	plugin.addCategoryClasses = function()
	{
		if ( !plugin.newData.hasOwnProperty('post_category') ) return;
		var cats = plugin.newData.post_category;
		for ( i = 0; i < cats.length; i++ ){
			var taxclass = 'in-category-' + cats[i];
			$(plugin.row).addClass(taxclass);
		}
	}


	// Add hierarchical taxonomy classes to the row
	plugin.addHierarchicalClasses = function()
	{
		if ( !plugin.newData.hasOwnProperty('tax_input') ) return;
		var taxonomies = plugin.newData.tax_input;
		$.each(taxonomies, function(tax, terms){
			for (i = 0; i < terms.length; i++){
				var taxclass = 'in-' + tax + '-' + terms[i];
				$(plugin.row).addClass(taxclass);
			}
		});
	}


	// Add flat taxonomy classes to the row
	plugin.addFlatClasses = function()
	{
		if ( !plugin.newData.hasOwnProperty('flat_tax') ) return;
		var taxonomies = plugin.newData.flat_tax;
		$.each(taxonomies, function(tax, terms){
			for (i = 0; i < terms.length; i++){
				var taxclass = 'inf-' + tax + '-nps-' + terms[i];
				$(plugin.row).addClass(taxclass);
			}
		});
	}


	// Toggle Form Loading State
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(NestedPages.selectors.quickEditErrorDiv).hide();
			$(plugin.saveButton).attr('disabled', 'disabled');
			$(NestedPages.selectors.quickEditLoadingIndicator).show();
			return;
		}
		$(plugin.saveButton).attr('disabled', false);
		$(NestedPages.selectors.quickEditLoadingIndicator).hide();
	}

	return plugin.bindEvents();

}
var NestedPages = NestedPages || {};

/**
* Quick Edit functionality for links
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.QuickEditLink = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.button = ''; // The Active Quick Edit Button
	plugin.postData = ''; // Data for Post being edited (before update)
	plugin.newPostData = ''; // Data after update
	plugin.form = ''; // The newly created Quick Edit Form

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.quickEditButtonLink, function(e){
			e.preventDefault();
			plugin.formatter.removeQuickEdit();
			plugin.button = $(this);
			plugin.openQuickEdit();
		});
		$(document).on('click', NestedPages.selectors.quickEditLinkSaveButton, function(e){
			e.preventDefault();
			plugin.submitForm();
		});
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) plugin.formatter.removeQuickEdit();
		});
	}


	// Open the Quick Edit Form
	plugin.openQuickEdit = function()
	{
		plugin.setData();
		plugin.createForm();
		plugin.populateForm();
	}


	// Set the Quick Edit Data
	plugin.setData = function()
	{
		plugin.postData = {
			id : $(plugin.button).attr('data-id'),
			url : $(plugin.button).attr('data-url'),
			title : $(plugin.button).attr('data-title'),
			status : $(plugin.button).attr('data-status'),
			navstatus : $(plugin.button).attr('data-navstatus'),
			npstatus : $(plugin.button).attr('data-np-status'),
			linktarget : $(plugin.button).attr('data-linktarget'),
			parentid : $(plugin.button).attr('data-parentid'),
			navtitleattr : $(plugin.button).attr('data-navtitleattr'),
			navcss : $(plugin.button).attr('data-navcss'),
			navtype : $(plugin.button).attr('data-nav-type'),
			navobject : $(plugin.button).attr('data-nav-object'),
			navobjectid : $(plugin.button).attr('data-nav-object-id'),
			navoriginallink : $(plugin.button).attr('data-nav-original-link'),
			navoriginaltitle : $(plugin.button).attr('data-nav-original-title')
		};
	}


	// Create the quick edit form
	plugin.createForm = function()
	{
		var parent_li = $(plugin.button).closest(NestedPages.selectors.row).parent('li');
		plugin.form = $(NestedPages.selectors.quickEditLinkForm).clone();
		
		// Append the form to the list item
		if ( $(parent_li).children('ol').length > 0 ){
			var child_ol = $(parent_li).children('ol');
			$(plugin.form).insertBefore(child_ol);
		} else {
			$(plugin.form).appendTo(parent_li);
		}

		var row = $(plugin.form).siblings(NestedPages.selectors.row).hide();
	}


	// Populate the Quick Edit form with the post data
	plugin.populateForm = function()
	{
		$(plugin.form).find('.np_id').val(plugin.postData.id);
		$(plugin.form).find('.np_title').val(plugin.postData.title);
		$(plugin.form).find('.np_author select').val(plugin.postData.author);
		$(plugin.form).find('.np_status').val(plugin.postData.status);
		$(plugin.form).find('.np_content').val(plugin.postData.url);
		$(plugin.form).find('.np_parent_id').val(plugin.postData.parentid);
		$(plugin.form).find('.np_title_attribute').val(plugin.postData.navtitleattr);
		$(plugin.form).find('.np_nav_css_classes').val(plugin.postData.navcss);

		if ( plugin.postData.npstatus === 'hide' ){
			$(plugin.form).find('.np_status').prop('checked', 'checked');
		} else {
			$(plugin.form).find('.np_status').removeAttr('checked');
		}
		
		if ( plugin.postData.navstatus === 'hide' ) {
			$(plugin.form).find('.np_nav_status').prop('checked', 'checked');
		} else {
			$(plugin.form).find('.np_nav_status').removeAttr('checked');
		}

		if ( plugin.postData.linktarget === "_blank" ) {
			$(plugin.form).find('.link_target').prop('checked', 'checked');
		} else {
			$(plugin.form).find('.link_target').removeAttr('checked');
		}

		// Relationship Links
		if ( plugin.postData.navobject !== 'custom' && plugin.postData.navobject !== '' ){
			var html = '<div class="form-control original-link">Original: <a href="' + plugin.postData.navoriginallink + '" target="_blank">' + plugin.postData.navoriginaltitle + '</a></div>';
			$(plugin.form).find('[data-url-field]').remove();
			$(html).insertAfter($(plugin.form).find('h3'));
			$(plugin.form).find('[data-np-menu-object-input]').val(plugin.postData.navobject);
			$(plugin.form).find('[data-np-menu-objectid-input]').val(plugin.postData.navobjectid);
			$(plugin.form).find('[data-np-menu-type-input]').val(plugin.postData.navtype);
			$(plugin.form).find('h3').text('Link: ' + plugin.postData.navoriginaltitle);
		} else {
			$(plugin.form).find('h3').text('Link');
			$(plugin.form).find('[data-np-menu-object-input]').val('custom');
			$(plugin.form).find('[data-np-menu-type-input]').val('custom');
		}

		plugin.formatter.showQuickEdit();
		$(plugin.form).show();
	}


	// Submit the form
	plugin.submitForm = function()
	{
		plugin.toggleLoading(true);

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: $(plugin.form).find('form').serialize() + '&action=' + NestedPages.formActions.quickEditLink + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
			success: function(data){
				if (data.status === 'error'){
					plugin.toggleLoading(false);
					$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
				} else {
					plugin.toggleLoading(false);
					plugin.newPostData = data.post_data;
					plugin.updateRow();					
				}
			},
			error: function(data){
				plugin.toggleLoading(false);
				$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text('The form could not be saved at this time.').show();
			}
		});
	}


	// Update the row after successfully saving quick edit data
	plugin.updateRow = function()
	{
		var row = $(plugin.form).siblings('.row');
		$(row).find('.title').html(plugin.newPostData.post_title + ' <svg class="link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="icon" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>');
		
		var status = $(row).find('.status');
		if ( (plugin.newPostData._status !== 'publish') && (plugin.newPostData._status !== 'future') ){
			$(status).text('(' + plugin.newPostData._status + ')');
		} else {
			$(status).text('');
		}

		// Hide / Show in Nav
		var nav_status = $(row).find('.nav-status');
		if ( (plugin.newPostData.nav_status == 'hide') ){
			$(nav_status).text('(' + NestedPages.jsData.hiddenText + ')');
		} else {
			$(nav_status).text('');
		}

		// Hide / Show in Nested Pages
		var li = $(row).parent('li');
		if ( (plugin.newPostData.np_status == 'hide') ){
			$(li).addClass('np-hide');
			$(row).find('.status').after('<svg class="row-status-icon status-np-hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path class="icon" d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>');
		} else {
			$(li).removeClass('np-hide');
			$(row).find('.status-np-hidden').remove();
		}

		var button = $(row).find(NestedPages.selectors.quickEditButtonLink);

		$(button).attr('data-id', plugin.newPostData.post_id);
		$(button).attr('data-title', plugin.newPostData.post_title);
		$(button).attr('data-url', plugin.newPostData.post_content);
		$(button).attr('data-status', plugin.newPostData._status);
		$(button).attr('data-navstatus', plugin.newPostData.nav_status);
		$(button).attr('data-np-status', plugin.newPostData.np_status);
		$(button).attr('data-linkTarget', plugin.newPostData.linkTarget);
		$(button).attr('data-navtitleattr', plugin.newPostData.titleAttribute);
		$(button).attr('data-navcss', plugin.newPostData.cssClasses);

		plugin.formatter.removeQuickEdit();
		plugin.formatter.flashRow(row);
	}


	// Toggle loading state in form
	plugin.toggleLoading = function(loading)
	{
		$('.row').removeClass('np-updated').removeClass('np-updated-show');
		if ( loading ){
			$(NestedPages.selectors.quickEditErrorDiv).hide();
			$(NestedPages.selectors.quickEditLinkSaveButton).attr('disabled', 'disabled');
			$(NestedPages.selectors.quickEditLoadingIndicator).show();
			return;
		}
		$(NestedPages.selectors.quickEditLinkSaveButton).attr('disabled', false);
		$(NestedPages.selectors.quickEditLoadingIndicator).hide();
	}

	return plugin.bindEvents();

}
var NestedPages = NestedPages || {};

/**
* Post clone functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Clone = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.parent_id = ''; // The parent/source post ID
	plugin.parent_title = ''; // The parent title
	plugin.parentLi = null;

	plugin.formatter = new NestedPages.Formatter;

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.cloneButton, function(e){
			e.preventDefault();
			plugin.parent_id = $(this).attr('data-id');
			plugin.parent_title = $(this).attr('data-parentname');
			plugin.parentLi = $(this).parent('.row').parent('.page-row').parent('.npList');
			plugin.openModal();
		});
		$(document).on('click', NestedPages.selectors.confirmClone, function(e){
			e.preventDefault();
			plugin.clone();
		});
	}

	// Open the modal with clone options
	plugin.openModal = function()
	{
		$('#' + NestedPages.selectors.cloneModal).find('[data-clone-parent]').text(plugin.parent_title);
		$(document).trigger('open-modal-manual', NestedPages.selectors.cloneModal);
	}

	// Clone the post
	plugin.clone = function()
	{
		plugin.toggleLoading(true);
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data : {
				action : NestedPages.formActions.clonePost,
				parent_id : plugin.parent_id,
				quantity : $(NestedPages.selectors.cloneQuantity).val(),
				status : $(NestedPages.selectors.cloneStatus).val(),
				author : $(NestedPages.selectors.cloneAuthor).find('select').val(),
				nonce : NestedPages.jsData.nonce,
				posttype : NestedPages.jsData.posttype
			},
			success : function(data){
				plugin.toggleLoading(false);
				$(document).trigger('close-modal-manual');
				location.reload();
			}
		});
	}

	// Toggle Loading
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$('#' + NestedPages.selectors.cloneModal).find('[data-clone-loading]').show();
			$(NestedPages.selectors.confirmClone).attr('disabled', 'disabled');
			return;
		}
		$('#' + NestedPages.selectors.cloneModal).find('[data-clone-loading]').hide();
		$(NestedPages.selectors.confirmClone).attr('disabled', false);
	}

	return plugin.bindEvents();
}
/**
* Tabs
* 
* @author Kyle Phillips
* 
* To use, include links with a data-tab-toggle attribute which matches the tab pane's data-tab-pane attribute
* The tabs and panes should all the same data-tab-group attribute value
* Add a CSS selector of .tab-pane to panes to hide them
*/
var NestedPages = NestedPages || {};
NestedPages.Tabs = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		tabToggle : 'data-np-tab-toggle',
		tabPane : 'data-np-tab-pane',
		tabGroup : 'data-np-tab-group'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.tabToggle + ']', function(e){
			e.preventDefault();
			self.toggleTabs($(this));
		});
	}

	/**
	* Toggle the Tabs
	*/
	self.toggleTabs = function(tab)
	{
		var tabGroup = $(tab).attr(self.selectors.tabGroup);
		var tabPanes = $('*[' + self.selectors.tabGroup + '=' + tabGroup + '][' + self.selectors.tabPane + ']');
		var activeTab = $(tab).attr(self.selectors.tabToggle);
		var buttons = $('*[' + self.selectors.tabGroup + '=' + tabGroup + '][' + self.selectors.tabToggle + ']');
		var listItems = [];

		for ( var i = 0; i < buttons.length; i++ ){
			listItems[i] = $(buttons[i]).parent('li')[0];
		}

		$(tabPanes).hide();
		$(buttons).removeClass('active');
		$(listItems).removeClass('active');

		$.each(tabPanes, function(){
			if ( $(this).attr(self.selectors.tabPane) == activeTab ) $(this).show();
		});

		$.each(buttons, function(){
			if ( $(this).attr(self.selectors.tabToggle) == activeTab ) {
				$(this).addClass('active');
				$(this).parent('li').addClass('active');
			}
		});

		$(document).trigger('tabs-changed', [activeTab, tabGroup, tab]);
	}

	return self.bindEvents();
}
/**
* Primary Nested Pages Initialization
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/

jQuery(document).ready(function(){
	new NestedPages.Factory;
});

var NestedPages = NestedPages || {};


// DOM Selectors
NestedPages.selectors = {
	childToggle : '.child-toggle', // Child Toggle Buttons
	childToggleLink : '.child-toggle a', // Actual link in button
	toggleAll : '.nestedpages-toggleall', // Toggle All Button
	toggleHidden : '.np-toggle-hidden', // Toggle Hidden Pages
	toggleStatus : '.np-toggle-publish', // Toggle Published Pages
	lists : '.nplist', // OL elements
	rows : '.page-row', // Page Row,
	row : '.row', // Inner row div element
	sortable : '.sortable', // Sortable List
	notSortable : '.no-sort', // Unsortable List
	handle : '.handle', // Sortable Handle
	published : '.published', // Published Rows
	hiddenRows : '.np-hide', // Hidden Rows
	errorDiv : '#np-error', // Error Alert
	loadingIndicator : '#nested-loading', // Loading Indicator,
	syncCheckbox : '.np-sync-menu', // Sync menu checkbox
	syncForm: '.np-sync-menu-cont', // The form/container for the sync menu element
	ajaxError : '[data-nestedpages-error]', // AJAX error notification
	trashWithChildrenButton : '[data-nestedpages-trash-children]',

	// Responsive Toggle
	toggleEditButtons : '.np-toggle-edit', // Button that toggles responsive buttons

	// Bulk Actions
	bulkActionsHeader : '.nestedpages-list-header',
	bulkActionsForm : '[data-np-bulk-form]',
	bulkActionsCheckbox : '[data-np-bulk-checkbox]',
	bulkActionsIds : '[data-np-bulk-ids]',
	bulkActionRedirectIds : '[data-np-bulk-redirect-ids]',
	hiddenItemCount : '[data-np-hidden-count]',
	hiddenItemCountParent : '[data-np-hidden-count-parent]',
	bulkEditForm : '[data-np-bulk-edit-form]', // The primary bulk edit form
	bulkEditTitles : '[data-np-bulk-titles]', // Titles to perform bulk edits on (includes hidden ids),
	bulkEditRemoveItem : '[data-np-remove-bulk-item]', // Remove an item from bulk edit
	bulkEditCancel : '[data-np-cancel-bulk-edit]', // Cancel button in bulk edit form
	bulkEditLinkCount : '[data-bulk-edit-link-count]', // Count of selected links in bulk edit

	// Quick Edit
	quickEditOverlay : '.np-inline-overlay', // The inline modal
	quickEditLoadingIndicator : '.np-qe-loading', // Loading indicator in Quick Edit
	quickEditErrorDiv : '.np-quickedit-error', // Error Div in Quick Edit
	quickEditCancel : '.np-cancel-quickedit', // Cancel button in quick edit

	// Quick Edit - Links
	quickEditButtonLink : '.np-quick-edit-redirect', // Button to open link quick edit
	quickEditLinkForm : '.quick-edit-form-redirect', // Form for link quick edits
	quickEditLinkSaveButton : '.np-save-quickedit-redirect', // Save button in link quick edit form

	// Quick Edit - Posts
	quickEditOpen : '.np-quick-edit', // Button to open post quick edit
	quickEditPostForm : '.quick-edit-form', // Form container
	quickEditSaveButton : '.np-save-quickedit', // Save button in quick edit (posts)

	// Link Items
	openLinkModal : '.open-redirect-modal', // Opens new link modal
	linkModal : 'np-link-modal', // The add a link modal
	saveLink : '.np-save-link', // Save Link Button
	linkLoadingIndicator : '.np-link-loading', // Loading Indicator in Link Modal
	linkErrorDiv : '.np-new-link-error', // Error Div in Link Modal
	linkForm : '.np-new-link-form', // The form element for a new link

	// Link Deletion
	linkDeleteButton : '[data-np-confirm-delete]',
	linkDeleteConfirmationButton : '[data-delete-confirmation]',
	linkDeleteConfirmationModal : 'np-delete-confirmation-modal',
	linkDeleteConfirmationModalText : '[data-np-link-delete-text]',

	// New Page Items
	openPageModal : '.open-bulk-modal', // Opens the new page(s) modal
	newPageModal : '#np-bulk-modal', // The modal with the new page form
	newPageFormContainer : '.new-child-form', // The new page form container
	newPageForm : '.np-new-child-form', // The form element
	newPageSubmitButton : '.np-save-newchild', // Submit button in new page form
	newPageTitle : '.add-new-child-row', // Button to add a new page title field to the form
	newPageRemoveTitle : '.np-remove-child', // Button to remove a title field in the form
	addChildButton : '.add-new-child', // Button to add child page(s)
	newChildError : '.np-newchild-error', // Error div in new child quick edit
	cancelNewChildButton : '.np-cancel-newchild', // Cancel button in new child quick edit
	newBeforeButton : 'data-insert-before', // Add new post(s) before a post
	newAfterButton : 'data-insert-after', // Add new post(s) after a post

	// Clone
	cloneButton : '.clone-post', // Button to clone a post
	confirmClone : '[data-confirm-clone]', // Button in modal to confirm clone
	cloneModal : 'np-clone-modal', // Modal with clone options
	cloneQuantity : '[data-clone-quantity]', // Quantity to Clone
	cloneStatus : '[data-clone-status]', // Clone Status
	cloneAuthor : '[data-clone-author]', // Clone Author

	// Tabs
	tabButtonParent : '[data-np-tabs]', // Tab Parent
	tabButton : '[data-np-tab]', // Tab Link
	tabContent : '[data-np-tab-pane]', // Tab Pane

	// Thumbnails
	thumbnailContainer : '.np-thumbnail', // Container for Thumbnail
	thumbnailContainerLink : '.np-thumbnail.link', // Link Thumbnail Container

	// Manual Sync Buttons
	manualMenuSync : '[data-np-manual-menu-sync]', // Button for Triggering Manual Menu Sync
	manualOrderSync : '[data-np-manual-order-sync]', // Button for Triggering Manual Order Sync

}


// CSS Classes
NestedPages.cssClasses = {
	noborder : 'no-border'
}


// JS Data
NestedPages.jsData = {
	ajaxurl : ajaxurl,
	nonce : nestedpages.np_nonce,
	allPostTypes : nestedpages.post_types, // Localized data with all post types
	syncmenu : 'nosync', // Whether to sync the menu
	posttype : '', // current Screen's post type
	nestable : true, // boolean - whether post type is nestable
	sortable : true, // boolean - whether post type is sortable
	hierarchical : true, // boolean - whether post type is hierarchical
	expandText : nestedpages.expand_text, // Expand all button text
	collapseText : nestedpages.collapse_text, // Collapse all button text
	showHiddenText : nestedpages.show_hidden, // Show Hidden Pages Link Text
	hideHiddenText : nestedpages.hide_hidden, // Hide Hidden Pages Link Text
	quickEditText : nestedpages.quick_edit, // Quick Edit Button Text
	hiddenText : nestedpages.hidden, // Localized "Hidden"
	titleText : nestedpages.title, // Localized "Title"
}


// Form Actions
NestedPages.formActions = {
	syncToggles : 'npnestToggle',
	syncNesting : 'npsort',
	syncMenu : 'npsyncMenu',
	newPage : 'npnewChild',
	newBeforeAfter : 'npnewBeforeAfter',
	quickEditLink : 'npquickEditLink',
	getTaxonomies : 'npgetTaxonomies',
	quickEditPost : 'npquickEdit',
	clonePost : 'npclonePost',
	search : 'npmenuSearch',
	newMenuItem : 'npnewMenuItem',
	manualMenuSync : 'npmanualMenuSync',
	postSearch: 'nppostSearch',
	wpmlTranslations : 'npWpmlTranslations',
	resetSettings : 'npresetSettings',
	resetUserPrefs : 'npresetUserPreferences',
	resetAdminMenuSettings : 'npresetAdminMenuSettings',
	trashWithChildren : 'nptrashWithChildren'
}


/**
* Primary Nested Pages Class
*/
NestedPages.Factory = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.dropdowns = new NestedPages.Dropdowns;
	plugin.modals = new NestedPages.Modals;
	plugin.checkAll = new NestedPages.CheckAll;
	plugin.bulkActions = new NestedPages.BulkActions;
	plugin.menuToggle = new NestedPages.MenuToggle;
	plugin.pageToggle = new NestedPages.PageToggle;
	plugin.nesting = new NestedPages.Nesting;
	plugin.syncMenuSetting = new NestedPages.SyncMenuSetting;
	plugin.newPage = new NestedPages.NewPost;
	plugin.quickEditLink = new NestedPages.QuickEditLink;
	plugin.quickEditPost = new NestedPages.QuickEditPost;
	plugin.clone = new NestedPages.Clone;
	plugin.tabs = new NestedPages.Tabs;
	plugin.menuLinks = new NestedPages.MenuLinks;
	plugin.hiddenItemCount = new NestedPages.HiddenItemCount;
	plugin.confirmDelete = new NestedPages.ConfirmDelete;
	plugin.manualSync = new NestedPages.ManualSync;
	plugin.postSearch = new NestedPages.PostSearch;
	plugin.postMove = new NestedPages.MovePost;
	plugin.wpml = new NestedPages.Wpml;
	plugin.trashWithChildren = new NestedPages.TrashWithChildren;

	plugin.init = function()
	{
		if ( nestedpages.settings_page ) return;
		plugin.bindEvents();
		plugin.setPostType();
		plugin.setMenuSync();
		plugin.setNestable();
		plugin.setSortable();
		plugin.formatter.updateSubMenuToggle();
		plugin.formatter.setBorders();
		plugin.formatter.setNestedMargins();
		plugin.nesting.initializeSortable();
	}


	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.quickEditOverlay, function(e){
			plugin.formatter.removeQuickEdit();
			plugin.newPage.cancelNewPage();
		});
		$(document).ready(function(){
			plugin.formatter.hideAjaxError();
			plugin.formatter.sizeLinkThumbnails();
		});
	}


	// Set whether or not post type is nestable
	plugin.setNestable = function()
	{
		var nestable = false;
		$.each(NestedPages.jsData.allPostTypes, function(i, v){
			if ( v.name !== NestedPages.jsData.posttype ) return;
			if ( v.hierarchical === true ) nestable = true;
			if ( v.disable_nesting === true ) nestable = false;
		});
		NestedPages.jsData.nestable = nestable;
	}


	// Set whether or not post type is sortable
	plugin.setSortable = function()
	{
		var sortable = true;
		$.each(NestedPages.jsData.allPostTypes, function(i, v){
			if ( v.name !== NestedPages.jsData.posttype ) return;
			if ( typeof v.disable_sorting === 'undefined' || v.disable_sorting === '' ) return;
			if ( v.disable_sorting === "true" ) sortable = false;
		});
		NestedPages.jsData.sortable = sortable;
	}


	// Set the Screen's Post Type
	plugin.setPostType = function()
	{
		NestedPages.jsData.posttype = nestedpages.current_post_type;
		if ( typeof NestedPages.jsData.posttype === 'undefined' || NestedPages.jsData.posttype === '' ){
			NestedPages.jsData.posttype = $(NestedPages.selectors.sortable).attr('id').substring(3);
		}
		NestedPages.jsData.hierarchical = NestedPages.jsData.allPostTypes[NestedPages.jsData.posttype].hierarchical;
	}


	// Set menu sync
	plugin.setMenuSync = function()
	{
		NestedPages.jsData.syncmenu = ( nestedpages.syncmenu === '1' ) ? 'sync' : 'nosync';
	}


	return plugin.init();
}
var NestedPages = NestedPages || {};

/**
* Menu Item Selection in Modal Link Form
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MenuLinks = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.typeButton = ''; // The Link Type selected button
	plugin.post = ''; // The new post created

	plugin.formatter = new NestedPages.Formatter;

	plugin.selectors = {
		form : '[data-np-menu-item-form]', // The form element
		typeSelect : '[data-np-menu-selection]', // Link in left column to choose type of link
		accordion : '[data-np-menu-accordion]', // Accordion of objects
		accordionItem : '[data-np-menu-accordion-item]', // Single item in the accordion
		formPlaceholder : '.np-menu-link-object-placeholder', // Placeholder element
		formDetails : '.np-menu-link-details', // Right pane form details
		searchResults : '[data-np-search-result]', // Appended search result rows
		defaultResults : '[data-default-result]', // Default results,
		originalLink : '[data-np-original-link]', // Original Link Preview
		saveButton : '[data-np-save-link]', // The Form Submit Button
		urlInputCont : '[data-np-menu-url-cont]', // Container for URL input (only for custom links)
		errorDiv : '[data-np-error]', // The error notification
	}

	plugin.fields = {
		object : '[data-np-menu-object-input]', // The object (ex: post/category/custom)
		objectid : '[data-np-menu-objectid-input]', // ex: term id, post id
		itemType : '[data-np-menu-type-input]', // ex: post_type, taxonomy
		url : '[data-np-menu-url]', // custom url
		navigationLabel : '[data-np-menu-navigation-label]',
		titleAttribute : '[data-np-menu-title-attr]',
		cssClasses : '[data-np-menu-css-classes]',
		npStatus : '[data-np-menu-np-status]',
		linkTarget : '[data-np-menu-link-target]',
		menuTitle : '[data-np-menu-title]',
		parentPostType : '[data-np-menu-parent-post-type]'
	}

	plugin.search = new NestedPages.MenuSearch;

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.openLinkModal, function(e){
			e.preventDefault();
			plugin.postParent = $(this).attr('data-parentid');
			$(plugin.selectors.form).find('.parent_id').val($(this).attr('data-parentid'));
			plugin.openModal();
		});
		$(document).on('click', plugin.selectors.accordionItem, function(e){
			e.preventDefault();
			plugin.accordion($(this));
		});
		$(document).on('click', plugin.selectors.typeSelect, function(e){
			e.preventDefault();
			plugin.typeButton = $(this);
			plugin.setLinkType();
		});
		$(document).on('keyup', plugin.fields.navigationLabel, function(){
			plugin.updateTitle();
		});
		$(document).on('click', plugin.selectors.saveButton, function(e){
			e.preventDefault();
			plugin.submitForm();
		});
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) $(document).trigger('close-modal-manual');
		});
	}

	// Open the Modal and Clear/Populate hidden fields
	plugin.openModal = function()
	{
		$(NestedPages.selectors.linkErrorDiv).hide();
		$(NestedPages.selectors.linkModal).find('input').val('');
		$(NestedPages.selectors.linkModal).find('.parent_id').val(plugin.postParent);
		plugin.clearForm();
		$(plugin.selectors.accordion).find('ul').hide();
		$(plugin.selectors.typeSelect).removeClass('active');
		$(document).trigger('open-modal-manual', NestedPages.selectors.linkModal);
	}

	// Accordion Menu
	plugin.accordion = function(button)
	{
		plugin.clearForm();
		var submenu = $(button).siblings('ul');
		if ( $(submenu).is(':visible') ){
			$(button).removeClass('active');
			$(submenu).slideUp('fast');
			return;
		}
		$(plugin.selectors.accordionItem).removeClass('active');
		$(button).addClass('active');
		$(button).parents(plugin.selectors.accordion).find('ul').slideUp('fast');
		$(submenu).slideDown('fast');
	}

	// Set the link type
	plugin.setLinkType = function()
	{
		if ( $(plugin.typeButton).hasClass('active') ){
			plugin.clearForm();
			return;
		}
		if ( $(plugin.typeButton).hasClass('np-custom-link') ){
			$(plugin.selectors.accordionItem).removeClass('active');
			$(plugin.selectors.accordion).find('ul').slideUp('fast');
		}
		$(plugin.selectors.formPlaceholder).hide();
		plugin.populateForm();
	}

	// Populate the form
	plugin.populateForm = function()
	{
		$(plugin.selectors.saveButton).show();
		$(plugin.selectors.typeSelect).removeClass('active');
		$(plugin.typeButton).addClass('active');
		$(plugin.fields.menuTitle).text($(plugin.typeButton).text()).val($(plugin.typeButton).text());
		$(plugin.selectors.form).find('h3').find('em').text($(plugin.typeButton).attr('data-np-object-name'));
		if ( $(plugin.typeButton).attr('data-np-permalink') !== "" ){
			$(plugin.selectors.form).find(plugin.selectors.urlInputCont).hide();
			$(plugin.selectors.form).find(plugin.selectors.originalLink).html('<a href="' + $(plugin.typeButton).attr('data-np-permalink') + '">' + $(plugin.typeButton).text() + '</a>');
			$(plugin.selectors.form).find(plugin.selectors.originalLink).parent('.original-link').show();
		} else {
			$(plugin.selectors.form).find(plugin.selectors.urlInputCont).show();
			$(plugin.selectors.form).find(plugin.selectors.originalLink).parent('.original-link').hide();
		}
		$(plugin.fields.object).val($(plugin.typeButton).attr('data-np-menu-object'));
		$(plugin.fields.objectid).val($(plugin.typeButton).attr('data-np-menu-objectid'));
		$(plugin.fields.itemType).val($(plugin.typeButton).attr('data-np-menu-type'));
		$(plugin.selectors.formDetails).show();
	}

	// Clear the form
	plugin.clearForm = function()
	{
		$(plugin.selectors.form).find(plugin.selectors.errorDiv).hide();
		$(plugin.selectors.saveButton).hide();
		$(plugin.selectors.formDetails).hide();
		$(plugin.selectors.formPlaceholder).show();
		$(plugin.selectors.form).find('input').not('.parent_id').not('.parent-post-type').val('');
		$(plugin.selectors.form).find(plugin.fields.linkTarget).val('_blank');
		$(plugin.selectors.form).find('input[type="checkbox"]').attr('checked', false);
		$(plugin.selectors.typeSelect).removeClass('active');
		plugin.search.toggleLoading(false);
		$(plugin.selectors.searchResults).remove();
		$(plugin.selectors.defaultResults).show();
	}

	// Update the title text
	plugin.updateTitle = function()
	{
		var value = $(plugin.fields.navigationLabel).val();
		var title = $(plugin.selectors.form).find('h3').find('span');
		if ( value !== "" ){
			$(plugin.fields.menuTitle).val(value);
			$(title).text(value);
		} else {
			$(plugin.fields.menuTitle).val($(plugin.typeButton).text());
			$(title).text($(plugin.typeButton).text());
		}
	}

	// Submit the Form
	plugin.submitForm = function()
	{
		plugin.toggleLoading(true);
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data: $(plugin.selectors.form).serialize() + '&action=' + NestedPages.formActions.newMenuItem + '&nonce=' + NestedPages.jsData.nonce + '&post_type=' + NestedPages.jsData.posttype + '&syncmenu=' + NestedPages.jsData.syncmenu,
			success : function(data){
				plugin.toggleLoading(false);
				if ( data.status === 'error' ){
					$(plugin.selectors.form).find(plugin.selectors.errorDiv).text(data.message).show();
					return;
				}
				plugin.post = data.post_data;
				plugin.createRow();
			},
			error : function(data){
				console.log(data);
			}
		});
	}

	// Create the nested pages row for the new link
	plugin.createRow = function()
	{
		var html = '<li id="menuItem_' + plugin.post.id + '" class="page-row published';
		html += '">'

		html += '<div class="row"><div class="child-toggle"><div class="child-toggle-spacer"></div></div><div class="row-inner">';
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-sub-menu"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M19 15l-6 6-1.42-1.42L15.17 16H4V4h2v10h9.17l-3.59-3.58L13 9l6 6z" class="arrow" /></svg>';
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>';
		html += '<a href="' + plugin.post.np_link_content + '" class="page-link page-title" target="_blank"><span class="title">' + plugin.post.menuTitle + ' <svg class="link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="icon" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg></span>';

		// Quick Edit Button
		html += '</a><div class="action-buttons"><a href="#" class="np-btn np-quick-edit-redirect" ';
		html +=	'data-id="' + plugin.post.id + '"'; 
		html += 'data-parentid="' + plugin.post.parent_id + '"';
		html += 'data-title="' + plugin.post.menuTitle + '" ';
		html += 'data-url="' + plugin.post.url + '" ';
		html += 'data-status="publish" ';
		html += 'data-np-status="show" ';
		html += 'data-navstatus="show" ';
		html += 'data-navcss="' + plugin.post.cssClasses + '" ';
		html += 'data-navtitleattr="' + plugin.post.titleAttribute + '" ';
		html += 'data-nav-type="' + plugin.post.menuType + '" ';
		html += 'data-nav-object="' + plugin.post.objectType + '" ';
		html += 'data-nav-object-id="' + plugin.post.objectId + '" ';
		html += 'data-nav-original-link="' + plugin.post.original_link + '" ';
		html += 'data-nav-original-title="' + plugin.post.original_title + '" ';
		html += 'data-linktarget="' + plugin.post.link_target + '">';
		html += NestedPages.jsData.quickEditText;
		html += '</a>';

		// Delete Link
		html += '<a href="' + plugin.post.delete_link + '" class="np-btn np-btn-trash"><i class="np-icon-remove"></i></a>';

		html += '</div></div></div></li>';

		if ( plugin.post.parent_id === "0" ){
			$('.nplist:first li:first').after(html);
		} else {
			plugin.appendChildRow(html);
		}

		$(document).trigger('close-modal-manual');

		plugin.row = $('#menuItem_' + plugin.post.id).find('.row');
		plugin.formatter.flashRow(plugin.row);
	}

	// Append a new child link to the appropriate menu
	plugin.appendChildRow = function(html)
	{
		var parent_row = $('#menuItem_' + plugin.post.parent_id);
		if ( $(parent_row).children('ol').length === 0 ){
			html = '<ol class="sortable nplist" style="display:block;">' + html + '</ol>';
			$(parent_row).append(html);
		} else {
			$(parent_row).find('ol:first').prepend(html);
		}
		plugin.formatter.updateSubMenuToggle();
	}

	// Toggle Loading
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(plugin.selectors.form).find(plugin.selectors.errorDiv).hide();
			$(plugin.selectors.form).find(NestedPages.selectors.quickEditLoadingIndicator).show();
			$(plugin.selectors.saveButton).attr('disabled', 'disabled');
			return;
		}
		$(plugin.selectors.form).find(NestedPages.selectors.quickEditLoadingIndicator).hide();
		$(plugin.selectors.saveButton).attr('disabled', false);
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Menu Item Search in Modal Link Form
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MenuSearch = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		searchForms : '*[data-np-menu-search]', // Search form selector
		defaultResults : '[data-default-result]', // Default results list items
		loadingIndicator : '.np-menu-search-loading', // loading indicator
		noResults : '.np-menu-search-noresults', // No results
		searchType : 'data-search-type', // The search object type (post_type, taxonomy)
		searchObject : 'data-search-object', // The object to search (post, category, etc)
		searchResults : '[data-np-search-result]', // Appended search result rows
	}

	plugin.activeForm = ''; // The active form
	plugin.results = ''; // Search results
	plugin.defaultResults = ''; // The default, loaded results
	plugin.searchType = ''; // The type of search (post_type, taxonomy)
	plugin.searchObject = ''; // The object being searched (post, category, post_tag, etc…)

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('keyup', plugin.selectors.searchForms, function(){
			plugin.activeForm = $(this);
			$(plugin.selectors.searchResults).remove();
			plugin.performSearch();
		});
	}


	// Perform the search
	plugin.performSearch = function()
	{
		plugin.defaultResults = $(plugin.activeForm).parent('li').siblings(plugin.selectors.defaultResults);
		if ( $(plugin.activeForm).val().length > 2 ){
			$(plugin.defaultResults).hide();
			plugin.toggleLoading(true);
			plugin.query();
			return;
		}
		plugin.toggleLoading(false);
		$(plugin.defaultResults).show();
	}


	// Query Search
	plugin.query = function()
	{
		plugin.searchType = $(plugin.activeForm).attr(plugin.selectors.searchType);
		plugin.searchObject = $(plugin.activeForm).attr(plugin.selectors.searchObject);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.search,
				nonce : NestedPages.jsData.nonce,
				term : $(plugin.activeForm).val(),
				searchType : plugin.searchType,
				searchObject : plugin.searchObject,
			},
			success: function(data){
				console.log(data);
				if ( data.results ){
					plugin.results = data.results;
					plugin.toggleLoading(false);
					if ( plugin.searchType === 'post_type' ){
						plugin.appendPosts();
					} else {
						plugin.appendTaxonomies();
					}
				} else {
					plugin.toggleLoading(false);
					$(plugin.activeForm).siblings(plugin.selectors.noResults).show();
				}
			}
		});
	}


	// Append post type results
	plugin.appendPosts = function()
	{
		var html = "";
		$.each(plugin.results, function(i, v){
			html += '<li data-np-search-result><a href="#" data-np-menu-object="' + plugin.searchObject + '" data-np-menu-type="post_type" data-np-menu-objectid="' + v.ID + '" data-np-permalink="' + v.permalink + '" data-np-object-name="' + v.singular_name + '" data-np-menu-selection>' + v.post_title + '</a></li>';
		});
		$(html).insertAfter($(plugin.activeForm).parent('li'));
		plugin.toggleLoading(false);
	}


	// Append taxonomy results
	plugin.appendTaxonomies = function()
	{
		var html = "";
		$.each(plugin.results, function(i, v){
			html += '<li data-np-search-result><a href="#" data-np-menu-object="' + plugin.searchObject + '" data-np-menu-type="post_type" data-np-menu-objectid="' + v.term_id + '" data-np-permalink="' + v.permalink + '" data-np-object-name="' + v.taxonomy + '" data-np-menu-selection>' + v.name + '</a></li>';
		});
		$(html).insertAfter($(plugin.activeForm).parent('li'));
		plugin.toggleLoading(false);
	}


	// Toggle the loading indicator
	plugin.toggleLoading = function(loading)
	{
		var loadingIndicator = $(plugin.activeForm).siblings(plugin.selectors.loadingIndicator);
		$(plugin.selectors.noResults).hide();
		if ( loading ){
			$(loadingIndicator).show();
			return;
		}
		$(loadingIndicator).hide();
	}

	return plugin.init();
}
/**
* Empty Trash Functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
jQuery(document).ready(function(){
	new NestedPagesTrash;
});

var NestedPagesTrash = function()
{
	var plugin = this;
	var $ = jQuery;

	// DOM Selectors
	plugin.trashButton = '.np-empty-trash'; // Trash Link
	plugin.confirmButton = '.np-trash-confirm'; // Confirm button in modal
	plugin.warningModal = '#np-trash-modal'; // Modal with empty confirmation
	plugin.errorAlert = '#np-error'; // Alert Error Notification
	plugin.loadingIndicator = '#nested-loading'; // Loading Indication
	plugin.trashLinks = '.np-trash-links';
	plugin.postType = $('#np-trash-posttype').val();

	// JS Data
	plugin.nonce = nestedpages.np_nonce;
	plugin.formAction = 'npEmptyTrash';

	plugin.bindEvents = function(){
		$(document).on('click', plugin.confirmButton, function(e){
			e.preventDefault();
			plugin.confirmEmpty();
		});
	}

	// Confirm Trash Empty
	plugin.confirmEmpty = function(){
		plugin.loading(true);
		$(document).trigger('close-modal-manual');
		$(plugin.errorAlert).hide();
		plugin.emptyTrash();
	}

	// Empty the Trash
	plugin.emptyTrash = function(){
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : plugin.formAction,
				nonce : plugin.nonce,
				posttype : plugin.postType
			},
			success: function(data){
				plugin.loading(false);
				if (data.status === 'error'){
					$(plugin.errorAlert).text(data.message).show();
				} else {
					$(plugin.trashLinks).hide();
					$('.notice-dismiss').click();
				}
			}
		});
	}

	// Loading Indication
	plugin.loading = function(loading){
		if ( loading ){
			$(plugin.loadingIndicator).show();
			return;
		}
		$(plugin.loadingIndicator).hide();
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Confirm deletion of links
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.ConfirmDelete = function()
{
	var plugin = this;
	var $ = jQuery;

	// JS Data
	plugin.deleteLink = ''; // Link for deleting the item

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.linkDeleteButton, function(e){
			e.preventDefault();
			plugin.confirmSingle($(this));
		});
		$(document).on('click', NestedPages.selectors.linkDeleteConfirmationButton, function(e){
			e.preventDefault();
			if ( !$(this).hasClass('bulk') ){
				plugin.deleteSingle();
				return;
			}
			plugin.deleteMultiple();
		});
		$(document).on('submit', NestedPages.selectors.bulkActionsForm, function(e){
			plugin.confirmMultiple(e);
		});
	}

	// Confirm a single link deletion
	plugin.confirmSingle = function(button)
	{
		plugin.deleteLink = $(button).attr('href');
		$(NestedPages.selectors.linkDeleteConfirmationModalText).text(nestedpages.link_delete_confirmation_singular);
		$(NestedPages.selectors.linkDeleteConfirmationButton).text(nestedpages.delete).removeClass('bulk');
		$(document).trigger('open-modal-manual', NestedPages.selectors.linkDeleteConfirmationModal);
	}

	// Confirm Multiple link deletion
	plugin.confirmMultiple = function(event)
	{
		if ( $('select[name="np_bulk_action"]').val() !== 'trash' ) return;
		var linkCount = $(NestedPages.selectors.bulkActionRedirectIds).val();
		if ( linkCount === '' ) return;
		event.preventDefault();
		$(NestedPages.selectors.linkDeleteConfirmationModalText).text(nestedpages.link_delete_confirmation);
		$(NestedPages.selectors.linkDeleteConfirmationButton).text(nestedpages.trash_delete_links).addClass('bulk');
		$(document).trigger('open-modal-manual', [NestedPages.selectors.linkDeleteConfirmationModal]);
	}

	// Submit the form to delete multiple
	plugin.deleteMultiple = function()
	{
		$(NestedPages.selectors.bulkActionsForm)[0].submit();
	}

	// Delete the single
	plugin.deleteSingle = function()
	{
		window.location.replace(plugin.deleteLink);
	}

	return plugin.bindEvents();
}

var NestedPages = NestedPages || {};

/**
* Manual Sync functionality for nested view
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.ManualSync = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.activeBtn = '';

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.manualMenuSync, function(e){
			e.preventDefault();
			plugin.activeBtn = $(this);
			plugin.syncMenu();
		});
		$(document).on('click', NestedPages.selectors.manualOrderSync, function(e){
			e.preventDefault();
			plugin.activeBtn = $(this);
			plugin.syncOrder();
		});
	}

	plugin.syncMenu = function()
	{
		plugin.loading(true);

		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.manualMenuSync,
				nonce : NestedPages.jsData.nonce,
				post_type : NestedPages.jsData.posttype,
				syncmenu : 'sync'
			},
			success: function(data){
				if (data.status === 'error'){
					$(NestedPages.selectors.errorDiv).text(data.message).show();
					$(NestedPages.selectors.loadingIndicator).hide();
				} else {
					plugin.loading(false);
				}
			}
		});
	}

	plugin.syncOrder = function()
	{
		plugin.loading(true);
		var nestingClass = new NestedPages.Nesting;
		nestingClass.syncNesting(true, plugin.loading(false));
	}

	plugin.loading = function(loading)
	{
		if ( loading ){
			$(plugin.activeBtn).addClass('disabled');
			$(NestedPages.selectors.loadingIndicator).show();
			return;
		}
		$(plugin.activeBtn).removeClass('disabled');
		$(NestedPages.selectors.loadingIndicator).hide();
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Perform an AJAX search for posts by type
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.PostSearch = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		input : 'data-nestedpages-post-search',
		form : 'data-nestedpages-post-search-form',
		loadingIndicator : 'data-nestedpages-loading',
		noResults : 'data-nestedpages-no-results',
		results: 'data-nestedpages-search-results'
	}

	plugin.changed = false;
	plugin.activeInput = ''; // The active input
	plugin.results = ''; // Search results
	plugin.defaultResults = ''; // The default, loaded results
	plugin.postType = ''; // The type of search (post_type, taxonomy)
	plugin.activeForm = '';

	plugin.bindEvents = function()
	{
		$('['+ plugin.selectors.input + ']').on('input', function(){
			plugin.activeInput = $(this);
			plugin.setOptions();
			if ( !plugin.changed ) plugin.setDefaultResults();
			if ( $(this).val() === '' ) {
				$(plugin.activeForm).find('[' + plugin.selectors.noResults + ']').hide();
				plugin.showDefaultResults();
				return;
			}
			plugin.query();
		});
	}

	/**
	* Set the default results 
	*/
	plugin.setDefaultResults = function()
	{
		plugin.defaultResults = $(plugin.activeForm).find('[' + plugin.selectors.results + ']').html();
		plugin.changed = true;
	}

	/**
	* Show the default results 
	*/
	plugin.showDefaultResults = function()
	{
		$(plugin.activeForm).find('[' + plugin.selectors.results + ']').html(plugin.defaultResults);
	}

	/**
	* Set the options
	*/
	plugin.setOptions = function()
	{
		plugin.postType = $(plugin.activeInput).attr(plugin.selectors.input);
		plugin.activeForm = $(plugin.activeInput).parents('[' + plugin.selectors.form + ']');
	}

	// Perform the search
	plugin.performSearch = function()
	{
		plugin.defaultResults = $(plugin.activeForm).parent('li').siblings(plugin.selectors.defaultResults);
		if ( $(plugin.activeForm).val().length > 2 ){
			$(plugin.defaultResults).hide();
			plugin.toggleLoading(true);
			plugin.query();
			return;
		}
		plugin.toggleLoading(false);
		$(plugin.defaultResults).show();
	}


	// Query Search
	plugin.query = function()
	{
		$(plugin.activeForm).find('[' + plugin.selectors.results + ']').empty();
		plugin.toggleLoading(true);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.postSearch,
				nonce : NestedPages.jsData.nonce,
				term : $(plugin.activeInput).val(),
				postType : plugin.postType
			},
			success: function(data){
				if ( data.results ){
					plugin.results = data.results;
					plugin.loadResults();
					plugin.toggleLoading(false);
				} else {
					plugin.toggleLoading(false);
					$(plugin.activeForm).find('[' + plugin.selectors.noResults + ']').show();
				}
			}
		});
	}

	// Load the results into view
	plugin.loadResults = function()
	{
		var html = "<ul>";
		$.each(plugin.results, function(i, v){
			html += '<li><a href="#" data-assignment-page-id="' + v.ID + '" data-assignment-page-title="' + v.post_title + '">' + v.post_title + '</a></li>';
		});
		html += '</ul>';
		$(plugin.activeForm).find('[' + plugin.selectors.results + ']').html(html);
		plugin.toggleLoading(false);
	}

	// Toggle the loading indicator
	plugin.toggleLoading = function(loading)
	{
		var loadingIndicator = $(plugin.activeForm).find('[' + plugin.selectors.loadingIndicator + ']');
		var noResults = $(plugin.activeForm).find('[' + plugin.selectors.noResults + ']');
		$(noResults).hide();
		if ( loading ){
			$(loadingIndicator).show();
			return;
		}
		$(loadingIndicator).hide();
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Move a Post Up or Down in the list
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MovePost = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.nesting = new NestedPages.Nesting;
	plugin.activeRow;

	plugin.selectors = {
		moveToTop : 'data-push-to-top',
		moveToBottom : 'data-push-to-bottom'
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){
			plugin.disableTopOnFirst();
			plugin.disableBottomOnLast();
		});
		$(document).on('click', '[' + plugin.selectors.moveToTop + ']', function(e){
			e.preventDefault();
			if ( $(this).hasClass('disabled') ) return;
			plugin.activeRow = $(this).closest(NestedPages.selectors.rows);
			plugin.moveToTop();
		});
		$(document).on('click', '[' + plugin.selectors.moveToBottom + ']', function(e){
			e.preventDefault();
			if ( $(this).hasClass('disabled') ) return;
			plugin.activeRow = $(this).closest(NestedPages.selectors.rows);
			plugin.moveToBottom();
		});
	}

	/**
	* Move a post to the top of its list
	*/
	plugin.moveToTop = function()
	{
		var parent = $(plugin.activeRow).parent(NestedPages.selectors.lists);
		var first = $(parent).find(NestedPages.selectors.rows).first();
		$(plugin.activeRow).insertBefore(first);
		plugin.formatter.setBorders();
		$(document).click(); // Close Dropdowns
		plugin.nesting.syncNesting();
		plugin.disableTopOnFirst();
		plugin.disableBottomOnLast();
	}

	/**
	* Move a post to the bottom of its list
	*/
	plugin.moveToBottom = function()
	{
		var parent = $(plugin.activeRow).parent(NestedPages.selectors.lists);
		var last = $(parent).children(NestedPages.selectors.rows).last();
		$(plugin.activeRow).insertAfter(last);
		plugin.formatter.setBorders();
		$(document).click(); // Close Dropdowns
		plugin.nesting.syncNesting();
		plugin.disableTopOnFirst();
		plugin.disableBottomOnLast();
	}

	plugin.disableTopOnFirst = function()
	{
		var lists = $(NestedPages.selectors.lists);
		$.each(lists, function(){
			$(this).find('[' + plugin.selectors.moveToTop + ']').removeClass('disabled');
			var first = $(this).find(NestedPages.selectors.rows).first();
			$(first).find('[' + plugin.selectors.moveToTop + ']').addClass('disabled');
		});
	}

	plugin.disableBottomOnLast = function()
	{
		var lists = $(NestedPages.selectors.lists);
		$.each(lists, function(){
			$(this).find('[' + plugin.selectors.moveToBottom + ']').removeClass('disabled');
			var last = $(this).find(NestedPages.selectors.rows).last();
			$(last).find('[' + plugin.selectors.moveToBottom + ']').addClass('disabled');
		});
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* Trash post with all children
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.TrashWithChildren = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.post_id = ''; // The parent/source post ID

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.trashWithChildrenButton, function(e){
			e.preventDefault();
			plugin.post_id = $(this).attr('data-post-id');
			plugin.trash();
		});
	}

	// Trash the posts
	plugin.trash = function()
	{
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data : {
				action : NestedPages.formActions.trashWithChildren,
				nonce : NestedPages.jsData.nonce,
				post_id : plugin.post_id,
				screen : nestedpages.current_page
			},
			success : function(data){
				window.location.replace(data.redirect);
			}, error : function(data){
				console.log(data);
			}
		});
	}

	return plugin.bindEvents();
}
var NestedPages = NestedPages || {};

/**
* WPML functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Wpml = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.button = null; // The quick edit button with all the data-attributes for the post
	plugin.postData = null; // Object containing post data
	plugin.modal = null; // The modal element
	plugin.parent_li = null; // The post's nested pages list element
	plugin.formatter = new NestedPages.Formatter;

	plugin.selectors = {
		translationsBtn : 'data-nestedpages-translations',
		modal : 'data-np-wpml-translations-modal',
		title : 'data-wmpl-translation-title',
		table : 'data-np-wpml-translations-modal-table'
	}

	plugin.bindEvents = function()
	{
		if ( !nestedpages.wpml ) return;
		$(document).on('click', '[' + plugin.selectors.translationsBtn + ']', function(e){
			e.preventDefault();
			plugin.createTranslationsModal($(this));
		});
	}

	/**
	* Create the translations modal
	*/
	plugin.createTranslationsModal = function(button)
	{
		plugin.parent_li = $(button).parents('.action-buttons').closest(NestedPages.selectors.row).parent('li');
		plugin.button = $(plugin.parent_li).find(NestedPages.selectors.quickEditOpen);
		plugin.postData = {
			id : $(plugin.button).attr('data-id'),
			title : $(plugin.button).attr('data-title'),
			slug : $(plugin.button).attr('data-slug')
		}
		plugin.modal = $('[' + plugin.selectors.modal + ']').clone();

		if ( $(plugin.parent_li).children('ol').length > 0 ){
			var child_ol = $(plugin.parent_li).children('ol');
			$(plugin.modal).insertBefore(child_ol);
		} else {
			$(plugin.modal).appendTo(plugin.parent_li);
		}
		$(plugin.modal).find('[' + plugin.selectors.title + ']').text(plugin.postData.title);
		plugin.formatter.showQuickEdit();
		$(plugin.modal).show();
		plugin.getTranslationData();
	}

	/**
	* Get the Translation Data for the Post
	*/
	plugin.getTranslationData = function()
	{
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data : {
				action : NestedPages.formActions.wpmlTranslations,
				post_id : plugin.postData.id,
				nonce : NestedPages.jsData.nonce
			},
			success: function(data){
				console.log(data);
				console.log(plugin.postData.id);
				if ( data.status === 'success' ){
					plugin.populateModal(data.translations);
				} else {
					$(plugin.modal).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
					plugin.toggleLoading(false);
				}
			}
		});
	}

	/**
	* Open the Modal
	*/
	plugin.populateModal = function(translations)
	{
		var html = '<tbody>';
		$.each(translations, function(i, v){
			var translation = translations[i];
			html += '<tr>';
			html += '<td><img src="' + translation.country_flag_url + '" alt="' + translation.translated_name + '" /> ' + translation.translated_name + '</td>';
			html += '<td>';
			if ( translation.has_translation && translation.edit_link ){
				html += '<a href="' + translation.edit_link + '">' + translation.translation.post_title + ' (' + nestedpages.edit + ')</a>';
			} else {
				html += '<a href="' + translation.add_link + '" class="np-btn">+ ' + nestedpages.add_translation + '</a>';
			}
			html += '</td>';
			html += '</tr>';
		});
		html += '</tbody>';
		$(plugin.modal).find('[' + plugin.selectors.table + ']').html(html);
		plugin.toggleLoading(false);
	}

	/**
	* Toggle the Loading State
	*/
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(plugin.modal).addClass('loading');
			return;
		}
		$(plugin.modal).removeClass('loading');
	}


	return plugin.bindEvents();
}