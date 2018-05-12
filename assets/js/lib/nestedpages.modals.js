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