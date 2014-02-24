var eXecutr = eXecutr || {};

eXecutr.WeeklyReview = function() {

	var init = function() {
		/* Handle item completion by AJAX on checkboxes */
		$("form.completion-form input:checkbox").change(function() {
			completeAction(this);
		});

		initCompleteOnDate();
	},

	initCompleteOnDate = function() {
		$(".complete-item > .datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			showAnim: '',
			firstDay: 1,
			onSelect: function(date) {
				completeAction($(this).siblings('input:checkbox'), date);
			}
		});
	},

	completeAction = function(actionCheckbox, date) {
		var form = $(actionCheckbox).closest("form");

		var args = { itemId: $(actionCheckbox).attr("value") };
		if (undefined !== date) {
			args.date = date;
		}

		$.post(
			$(form).attr("action"),
			args,
			function(data) {
				$(actionCheckbox).closest("tr").addClass("success")
					.fadeOut(300, function() {
						/* Remove just the completed item */
						$(this).remove();
					});
			}
		);
	}

	return {
        init: init
    }

}();
