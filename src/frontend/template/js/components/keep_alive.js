waitFor('jQuery', ($) => {

	// Keep-alive
	if (_env && _env.keep_alive_url) {
		setInterval(function() {
			$.get({
				url: _env.keep_alive_url,
				cache: false
			})
		}, 60e3)
	}

});