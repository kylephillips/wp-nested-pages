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