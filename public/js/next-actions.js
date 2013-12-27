var eXecutr = eXecutr || {};

eXecutr.NextActions = function() {

	var init = function() {
		/* Handle item completion by AJAX on checkboxes */
		$("form.completion-form input:checkbox").change(function() {
			completeAction(this);
		});

		/* Setup showing & hiding of time and space contexts */
		$("#time-context-menu a").triggerContextMenu("tr.", "#next-actions tbody tr");
		$("#space-context-menu a").triggerContextMenu("div.", "#next-actions > form > div");

		initInboxProcessKeyShort();
	},

	initInboxProcessKeyShort = function() {
		$("#inbox-process-decisions button").each(function() {
			var button = this;
			$("#inbox-processing-form").bind("keypress", $(button).attr("data-key"), function() {
				$(button).click();
			});
		});
	},

	completeAction = function(actionCheckbox) {
		var form = $(actionCheckbox).closest("form");

		$.post(
			$(form).attr("action"),
			{
				itemId: $(actionCheckbox).attr("value")
			},
			function(data) {
				$(actionCheckbox).closest("tr").addClass("success")
					.fadeOut(300, function() {
						/* Clean obsolete elements - completed item and possibly now its empty context */
						if (0 === $(this).siblings('tr[class*=time-context-]').length) {
							var context = $(this).closest('div[class*=space-context-]');
							/* Remove context button */
							$("[data-name=" + $(context).attr("class") + "]", $('#space-context-menu'))
									.closest("li").remove();
							/* Remove whole context section */
							$(context).remove();
						} else {
							/* Remove just the completed item */
							$(this).remove();
						}
					});
			}
		);
	}

	return {
        init: init
    }

}();

/**
 * Handles single- and double-clicks on context menu items.
 * 
 * @param listItem Identitier of list item to show or hide.
 * @param allItems Identifier for all menu items to iterate through.
 */
$.fn.triggerContextMenu = function(listItem, allItems) {
	var isSingleClick;
	var menuItem = this;
	
	/* Hide or show space contexts when clicking on buttons */
	$(menuItem).click(function(event) {
		singleClick = true;
		var button = this;
		setTimeout(function() {
			/* Prevent this from execution if second click was registered */
			if (!singleClick)
				return false;
			var className = $(button).attr("data-name");
			if ($(button).hasClass('off')) {
				$(button).removeClass('off');
				$(listItem + className).removeClass('disabled').fadeIn(300, function() {
					$('div[class*=space-context-]:hidden:not(.disabled)').each(function() {
						if (0 !== $('tr[class*=time-context-]:not([style*="display: none"])', this).length) {
							$(this).show();
							$("[data-name=" + $(this).attr("class") + "]", $('#space-context-menu'))
									.removeClass("off");
						}
					});
				});
			} else {
				$(button).addClass('off');
				$(listItem + className).addClass('disabled').fadeOut(300, function() {
					$('div[class*=space-context-]:visible').each(function() {
						if (0 === $('tr[class*=time-context-]:visible', this).length) {
							$(this).hide();
							$("[data-name=" + $(this).attr("class") + "]", $('#space-context-menu'))
									.addClass("off");
						}
					});
				});
		}}, 250);
		return false;
	});
	
	/* On double click hide all contexts instead of this one */
	$(menuItem).dblclick(function() {
		singleClick = false;
		var className = $(this).attr("data-name");

		if ($(this).hasClass('off')) {
			$(this).removeClass('off');
			$(listItem + className).removeClass('disabled').fadeIn(300);
		}
		$(menuItem).filter(function(index) {
			return $(this).attr('data-name') != className;
		}).each(function(){
			if(!$(this).hasClass('off'))
				$(this).addClass('off');
		});
		$(allItems).filter(function(index){
			return $(this).attr('class') != className;
		}).addClass('disabled').fadeOut(300);
		return false;
	});
};

