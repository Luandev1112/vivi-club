/*
 * JobClass - Job Board Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

if (typeof noResultsText === 'undefined') {
	var noResultsText = 'No results';
	if (
		typeof langLayout.select2 !== 'undefined'
		&& typeof langLayout.select2.noResults !== 'undefined'
		&& typeof langLayout.select2.noResults === 'function'
	) {
		noResultsText = langLayout.select2.noResults();
	}
}
if (typeof fakeLocationsResults === 'undefined') {
	var fakeLocationsResults = '0';
}
if (typeof stateOrRegionKeyword === 'undefined') {
	var stateOrRegionKeyword = 'area';
}

if (typeof errorText === 'undefined') {
	var errorText = {
		errorFound: 'Error found'
	};
}
if (typeof isLoggedAdmin === 'undefined') {
	isLoggedAdmin = false;
}

$(document).ready(function () {
	
	noResultsText = '<div class="p-2">' + noResultsText + '</div>';
	
	$('input#locSearch').devbridgeAutocomplete({
		zIndex: 1492,
		serviceUrl: siteUrl + '/ajax/countries/' + strToLower(countryCode) + '/cities/autocomplete',
		type: 'post',
		data: {
			'city': $(this).val(),
			'_token': $('input[name=_token]').val()
		},
		minChars: 1,
		showNoSuggestionNotice: (['1', '2'].indexOf(fakeLocationsResults) === -1),
		noSuggestionNotice: noResultsText,
		onSearchStart: function () {
			/* Hide & Disable the field's Tooltip */
			var tooltipEl = $('#locSearch.tooltipHere');
			tooltipEl.tooltip('hide');
			tooltipEl.tooltip('disable');
		},
		beforeRender: function (container, suggestions) {
			var query = $('input#locSearch').val();
			
			if (typeof query !== 'undefined' && typeof suggestions !== 'undefined') {
				var suggestionsEl = $('.autocomplete-suggestions');
				if (suggestions.length <= 0) {
					if (query.substring(0, stateOrRegionKeyword.length) === stateOrRegionKeyword) {
						suggestionsEl.addClass('d-none');
					} else {
						suggestionsEl.removeClass('d-none');
					}
				} else {
					suggestionsEl.removeClass('d-none');
				}
			}
		},
		onSearchError: function (query, jqXHR, textStatus, errorThrown) {
			showErrorModal(jqXHR, errorThrown);
		},
		onSelect: function (suggestion) {
			$('#lSearch').val(suggestion.data);
			
			/* Enable the field's Tooltip */
			$('#locSearch.tooltipHere').tooltip('enable');
		}
	});
	
});

function showErrorModal(jqXHR, title) {
	/* Check local vars */
	if (typeof jqXHR.responseText === 'undefined') {
		return false;
	}
	
	var obj = jQuery.parseJSON(jqXHR.responseText);
	if (typeof obj.message === 'undefined') {
		return false;
	}
	
	/* Setup the Modal */
	if (typeof title !== 'undefined') {
		$('#errorModalTitle').html(title);
	}
	
	var message = obj.message;
	var content = '<h4 class="m-b-20 font-weight-bold">' + errorText.errorFound + ':</h4>';
	content += '<code>' + message + '</code>';
	if (isLoggedAdmin) {
		if (message.indexOf('collation') !== -1 || message.indexOf('COLLATION') !== -1) {
			content += '<br><br>';
			content += 'The database server <strong>character set</strong> and <strong>collation</strong> are not properly configured. ';
			content += 'Please visit the "Admin panel → System → System Info" for more information.';
		}
	}
	$('#errorModalBody').html(content);
	
	/* Show the Modal */
	$('#errorModal').modal('show');
}

/* Unused */
function hideNoSuggestionNotice(query, stateOrRegionKeyword) {
	if (query.substring(0, stateOrRegionKeyword.length) === stateOrRegionKeyword) {
		var suggestionsEl = $('.autocomplete-suggestions');
		var noSuggestionEl = $('.autocomplete-no-suggestion');
		if (noSuggestionEl.length > 0) {
			console.log(query);
			suggestionsEl.addClass('d-none');
		} else {
			suggestionsEl.removeClass('d-none');
		}
	}
}
