+waitFor('jQuery', ($) => {

	// Keep-alive
	if (_env && _env.platform && _env.platform.path) {
		let keepAlive = setInterval(function() {
			$.get({
				url: _env.platform.path + 'ajax/keep_alive.json',
				cache: false
			})
		}, 60e3)
	}

});