jQuery(document).ready(function($) {
	var msgs = SU.msgs;
	$('.js-su-user').on('click', function(event) {
		event.preventDefault();
		if (!$('.su-wrapper').hasClass('working')) {
			$('.su-wrapper').addClass('working');
			$('.su-wrapper').removeClass('open');

			var user_id = $(this).attr('data-user-id');
			var su_security = $('.su-wrapper').find('#su-change-user-security').val();

			$.ajax({
				url: SU.ajaxurl,
				type: 'POST',
				data: {action: 'su_change_user', user_id: user_id, su_nonce: su_security},
			})
			.done(function(data) {
				if (data.status == 'ok') {
					alert( msgs.change_success );
					window.location.reload(true);
				} else if (data.msg != '') {
					alert(data.msg);
				} else {
					alert( SU.change_error );
				}
			})
			.fail(function() {
				alert( SU.connection_error );
			});
		}
	});

	$('.su-wrapper-toggle').on('click', function(event) {
		event.preventDefault();
		$('.su-wrapper').toggleClass('open');
	});
});
