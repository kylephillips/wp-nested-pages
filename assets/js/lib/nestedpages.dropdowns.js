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