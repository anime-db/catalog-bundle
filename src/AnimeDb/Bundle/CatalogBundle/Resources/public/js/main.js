// init after document load
$(function(){

// init cap
Cap.setButton($('#cap-breaker'));
Cap.setElement($('#cap'));

// set lazyload popup loader
PopupContainer.setPopupLoader($('#b-lazyload-popup'));
PopupContainer.container = $('#popup-wrapper');

var CollectionContainer = new FormCollectionContainer();

var FormContainer = new BlockLoadHandler();

// registr form collection
FormContainer.registr(function(block) {
	block.find('.f-collection > div').each(function() {
		var collection = $(this);
		CollectionContainer.add(
			new FormCollection(
				collection,
				collection.find('.bt-add-item'),
				collection.find('.f-row:not(.f-coll-button)'),
				'.bt-remove-item',
				FormContainer
			)
		);
	});
});

// registr form image
FormContainer.registr(function(block) {
	block.find('.f-image').each(function() {
		new FormImageController($(this));
	});
});
// registr form local path
FormContainer.registr(function(block) {
	block.find('.f-local-path').each(function() {
		new FormLocalPathController($(this));
	});
});

// registr form check all
FormContainer.registr(function(block) {
	new TableCheckAllController(block.find('.f-table-check-all'));
});

// registr form text autocomplete
FormContainer.registr(function(block) {
	block.find('input[type=search]:not([data-source=""])').each(function() {
		var input = $(this);
		input.autocomplete({
			source: input.data('source'),
			minLength: 2
		});
	});
});


// apply form for document
FormContainer.notify($(document));


// init notice container
var container = $('#notice-container');
if (container.size() && (from = container.data('from'))) {
	new NoticeContainerModel(container, from);
}

// confirm delete
$('.icon-delete, .b-notice-list button[type=submit]').each(function(){
	new ConfirmDeleteModel($(this));
});

$('.bt-toggle-block').each(function() {
	new ToggleBlock($(this));
});

$('.b-update-log code').each(function() {
	new UpdateLogBlock($(this));
});

// registr form storage
$('.f-storage').each(function() {
	var storage = $(this);
	new FormStorage(storage, storage.data('source'), $(storage.data('target')));
});

// init form field refill 
var refills = $('[data-type=refill]');
var form = refills.closest('form');
refills.each(function() {
	var field = $(this);
	if (field.data('prototype')) {
		var controller = new FormRefillCollection(
			field,
			CollectionContainer.get(field.attr('id')),
			CollectionContainer
		);
	} else {
		var controller = new FormRefillSimple(field);
	}

	// add plugin links and hendler
	$(field.closest('.f-row').find('label')[0])
		.append($(field.data('plugins')))
		.find('a').each(function() {
			new FormRefill(
				form,
				$(this),
				controller,
				FormContainer,
				CollectionContainer.get('anime_db_catalog_entity_item_sources')
			);
		});
});

if(jQuery().fancybox) {
	$('[data-control="gallery"]').fancybox({
		titlePosition: 'over',
		openEffect: 'fade',
		closeEffect: 'fade',
		helpers: {
			title: null
		}
	});
}

$('.b-search .bt-toggle-block').each(function() {
	var block = $(this);
	var icon = block.find('.bt-toggle-block-icon');
	block.click(function() {
		if (icon.text() == '▾') {
			icon.text('▴');
		} else {
			icon.text('▾');
		}
	});
});

});