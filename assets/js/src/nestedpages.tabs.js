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