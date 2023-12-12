(function ($) {
	'use strict';
	window.addEventListener("load", function (event) {

		if ($('.ljk_main_wrap').length) {
			toggleOptions();
			$('#ljk_redirect_to').change(function () {
				toggleOptions();
			});
		}


	});

	function toggleOptions() {
		$('#custom_page').hide();
		$('#custom_post').hide();
		$('#custom_url').hide();
		var set = $('#ljk_redirect_to').val();
		switch (set) {
			case 'page':
				$('#custom_page').show();
				break;
			case 'post':
				$('#custom_post').show();
				break;
			case 'link':
				$('#custom_url').show();
				break;
		}
	}

})(jQuery);
