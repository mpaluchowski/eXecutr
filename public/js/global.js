var eXecutr = eXecutr || {};

eXecutr.Global = function() {

	var init = function() {

		/* Apply datepicker */
		$('input.datepicker').applyDatePicker();

		initItemCatching();
		initForms();
		initParentAutocomplete();
		initParentInsertion();
		initRecurrenceBlock();
		initListAdditions();

		hideRecurrenceBlock();

	},

	initForms = function() {
		/* Prevent completion forms from ever submitting the regular way */
		$("form.completion-form").submit(function ( e ){
			e.preventDefault();
		});

		/* Focus the first input field in a form */
		$("form.edition-form :input:visible:enabled:first").focus();
		/* Enable form submission when pressing Ctrl+Enter */
		$( document ).on( "keydown", "form.edition-form :input", "ctrl+return", function(e) {
			e.preventDefault();
			$( "button.default-button[type=submit]", $( this ).closest( "form" )).click();
		});
	},

	initRecurrenceBlock = function() {
		$( document )
			/* Changing recurrence type changes its options panel */
			.on( 'change', "#repeats-type", function() {
				hideRecurrenceBlock();
				$("#" + $(this).val() + "-block").show();
			})

			/* Enabling the recurernce deadline edition */
			.on( 'change', "#recurrence-options :radio[name^=endDateTrigger]", function() {
				var datePicker = $(".datepicker", $(this).closest("div"));
				if($(datePicker).attr("disabled") == 'disabled') {
					$(datePicker).removeAttr("disabled").focus();
				} else {
					$(datePicker).attr("disabled", "disabled").val("");
				}
			});
	},

	hideRecurrenceBlock = function() {
		var recurrenceBlock = $("#recurrence-options > div.recurrence-block");
		if (recurrenceBlock.length !== 0)
			recurrenceBlock.hide();
	},

	initItemCatching = function() {
		/* Init floater loading on item catching */
		$('#add-items-menu a').click(function(e) {
			e.preventDefault();
			catchItem(this);
		});

		/* Global keyboard shortcuts for the floater menu */
		$('#add-items-menu a').each(function() {
			var link = this;
			$(document).bind("keypress", $(link).attr('data-key'), function(e) {
				catchItem(link);
			});
		});
	},

	catchItem = function(targetLink) {
		/* Close floater if open, only then open new floater as callback */
		if ($('#floater').length !== 0)
			closeFloater(loadFloater, targetLink);
		else
			loadFloater(targetLink);
	},

	loadFloater = function(targetLink) {
		$.get($(targetLink).attr("href"), function(data) {
			/* Add the returned form to the DOM in a floater */
			var floater = $('<div id="floater"></div>').html(data);
			$('body').append(floater);
			$(floater).hide().css({'top' : $("#header-mainmenu").outerHeight() + 'px'});

			initFloater(floater);
			
		}, "html");
	},

	initFloater = function(floater) {
		/* Enable closing the form with Esc */
		$('#floater, #floater :input').bind("keydown", "esc", closeFloater);
		
		hideRecurrenceBlock();
		initParentAutocomplete();

		$("input.datepicker", floater).applyDatePicker();
		
		/* Show the form and focus in first field */
		$(floater).slideDown(150);
		$(":input:visible:enabled:first", floater).focus();
		
		/* Handle submission by AJAX */
		$("button[type=submit]", floater).click(function(e) {
			e.preventDefault();
			var self = this;
			var form = $(this).closest("form");

			/* Need to append button value, since serializeArray() doesn't do that */
			var formData = $( form ).serializeArray();
			formData.push({ name: self.name, value: self.value });

			$.post($(form).attr('action'), formData, function(data) {
				/* Confirm submission, clear form for next entry */
				$(form).clearForm(true);
				$('#item-parents > li').remove();
				$(":input:visible:enabled:first", floater).focus();
				$(self).addClass('submitted', 400, function() {
					$(self).removeClass('submitted', 400);
				});
				/* If there was a next action planned, load next form */
				if ( data && '' !== data ) {
					// $("#inbox-notification").html(data);
					$( floater ).html( data );
					initFloater( floater );
				}
			});
		});
	},

	closeFloater = function(callback, callbackData) {
		$('#floater').slideUp(150, function() {
			$(this).remove();
			if (undefined !== callback && $.isFunction(callback))
				callback(callbackData);
		});
	},

	initParentAutocomplete = function() {
		/* No parent selection here */
		if ($('#parent-name').length === 0)
			return;

		var parentRemover = $('<a href="#">x</a>');
		$(document).on('click', '#item-parents a', function(e){
			e.preventDefault();
			$(this).closest('li').remove();
		});

		$('#parent-name').autocomplete({
			source : $( '#parent-name' ).attr( 'data-autocomplete' ),
			minLength : 3,
			focus: function( event, ui ) {
				$( "#parent-name" ).val( ui.item.title );
				return false;
			},
			select : function(event, ui) {
				$('#item-parents').append(
					$('<li>' + ui.item.title + '<input type="hidden" name="parentIds[]" value="' + ui.item.id + '"></li>').append($(parentRemover).clone(true))
				);

				/* Set item category to match selected parent's */
				var categorySelect = $("#category");
				if (ui.item.categoryId != 0 && $(categorySelect).val() == 0) {
					$(categorySelect).val(ui.item.categoryId);
				}

				/* Set item's deadlineto parent's, if exists and not set otherwise */
				var deadlineField = $("#deadline");
				if (ui.item.deadline !== '' && $(deadlineField).val() === '') {
					$(deadlineField).val(ui.item.deadline);
				}

				return false;
			},
			close : function(event, ui) {
				$( "#parent-name" ).val("");
			}
		}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.data( "ui-autocomplete-item", item )
				.append( "<a>" + item.title + " (" + item.type + ")" )
				.appendTo( ul );
		};
	},

	initParentInsertion = function() {
		/* Hitting Insert when in the #parentName field triggers creating a new parent */
		$( document ).on( "keydown", "#parent-name", "insert", function(e) {
			var parentNameField = this;
			$.post($( parentNameField ).attr( "data-url" ), function(data) {
				/* Add the parent creation form */
				var createForm = $('<tbody id="create-form" class="extension"></tbody>');
				$(parentNameField).closest("tbody").after(createForm);
				$(createForm).hide().html(data).show();
				$(":input:visible:enabled:first", createForm).focus();
				
				$("input.datepicker", createForm).applyDatePicker();
				
				/* Move parent selection into the form */
				var parentNameRow = $(parentNameField).closest("tr");
				$('#new-project-title', createForm).closest("tr").after(parentNameRow);
				
				/* Remove exising parent IDs */
				$('#item-parents > li').remove();
				
				/* Re-bind the post-autocomplete event of the parent selection */
				$(parentNameField).bind("autocompleteselect", function(e, ui) {
					/* Set project and item categories to match selected parent's */
					var categorySelect = $("#new-project-category");
					if (ui.item.categoryId != 0 && $(categorySelect).val() == 0) {
						$(categorySelect).val(ui.item.categoryId);
					}
					
					/* Set item's deadlineto parent's, if exists and not set otherwise */
					var deadlineField = $("#new-project-deadline");
					if (ui.item.deadline !== '' && $(deadlineField).val() === '') {
						$(deadlineField).val(ui.item.deadline);
					}
				});
			});
		});
	},

	initListAdditions = function() {
		$( document )
			/* Add list items inline */
			.on( "keydown", "#list-item-name", "return", function(e) {
				e.preventDefault();
				addListItem(this);
			})

			/* Removing list items via the 'x' remover */
			.on('click', '#list-items a', function(e) {
				e.preventDefault();
				$(this).closest('li').slideUp(100, function() {
					$(newItem).remove()
				});
			});
	}

	addListItem = function(itemField) {
		if ($(itemField).val() == '')
			return;

		$('<li></li>').append($(itemField).val())
			.append('<input type="hidden" name="listItems[]" value="' + $(itemField).val() + '" />')
			.append('<a href="#">x</a>')
			.appendTo('#list-items')

		$(itemField).clearForm();
	}

	return {
        init: init
    }

}();

$.fn.applyDatePicker = function() {
	$(this).datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		showAnim: '',
		numberOfMonths: 2,
		firstDay: 1});
};

$.fn.clearForm = function(setDefaults) {
	if (undefined === setDefaults)
		setDefaults = false;

	return this.each(function() {
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')
			return $(':input',this).clearForm(setDefaults);
		if (type == 'text' || type == 'password' || tag == 'textarea')
			this.value = setDefaults && undefined !== $(this).attr('data-default')
				? $(this).attr('data-default')
				: '';
		else if (type == 'checkbox' || type == 'radio')
			this.checked = setDefaults && undefined !== $(this).attr('data-default')
				? 'checked' === $(this).attr('data-default')
				: false;
		else if (tag == 'select') {
			if (setDefaults && undefined !== $(this).attr('data-default'))
				$(this).val($(this).attr('data-default'));
			else
				this.selectedIndex = -1;
		}
	});
};

