/*
# -- BEGIN LICENSE BLOCK ---------------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2013 Olivier Meunier & Association Dotclear
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK -----------------------------------------
*/

$(function() {
	
	// clean
	$('.remove-if-drag').remove();
	$('.hidden-if-drag').hide();
	$('.widgets, .sortable-delete').addClass('if-drag');
	
	// move
	$( ".connected, .sortable-delete" ).sortable({
		tolerance: "move",
		cursor: "move",
		axis: "y",
		dropOnEmpty: true,
		handle: ".widget-name",
		placeholder: "ui-sortable-placeholder",
		items: "li:not(.sortable-delete-placeholder)",
		connectWith: ".connected, .sortable-delete",
		start: function( event, ui ) {
			// petit décalage esthétique
			ui.item.css('left', ui.item.position().left + 20);
		},
		update: function(event, ui) {
			
			ul = $(this);
			widget = ui.item;
			field = ul.parents('.widgets');
			
			// met a zéro le décalage
			ui.item.css('left', 'auto');
			
			// signale les zones vides
			if( ul.find('li').length == 0 )
				 field.find('.empty-widgets').show();
			else field.find('.empty-widgets').hide();
			
			// remove
			if( widget.parents('ul').is('.sortable-delete') ) {
				widget.hide('slow', function() {
					$(this).remove();
				});
			}
			
			// réordonne
			if( ul.attr('id') ) {
				ul.find('li').each(function(i) {
					
					// trouve la zone de réception
					var name = ul.attr('id').split('dnd').join('');
					
					// modifie le name en conséquence
					$(this).find('*[name^=w]').each(function(){
						tab = $(this).attr('name').split('][');
						tab[0] = "w["+name;
						tab[1] = i;
						$(this).attr('name', tab.join(']['));
					});
					
					// ainssi que le champ d'ordre sans js (au cas ou)
					$(this).find('input[title=ordre]').val(i);
					
				});
			}
			
			// expand
			if(widget.find('img.expand').length == 0) {
				dotclear.postExpander(widget);
				dotclear.viewPostContent(widget, 'close');
			}
			
		}
	});
	
	// add
	$( "#widgets-ref > li" ).draggable({
		tolerance: "move",
		cursor: "move",
		connectToSortable: ".connected",
		helper: "clone",
		revert: "invalid",
		start: function( event, ui ) {
			ui.helper.css({'width': $('#widgets-ref > li').css('width')});
		}
	});
	$("li.ui-draggable, ul.ui-sortable li").css({'cursor':'move'});
});