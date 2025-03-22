window.waitFor = waitFor || ((objectName, callback, attempts=100) => {
	if (typeof(window[objectName]) !== 'undefined') {
		//console.debug('waitFor('+ objectName +') arrived')
		callback(window[objectName]);
	} else {
		if (attempts) {
			setTimeout(() => {
				waitFor(objectName, callback, --attempts);
			}, 50);
		} else {
			console.warn('waitFor('+ objectName +') timed out')
		}
	}
});

+waitFor('jQuery', ($) => {

	// Stylesheet Loader
	$.loadStylesheet = function(url, options, callback, fallback) {

		options = $.extend(options || {}, {
			rel: 'stylesheet',
			href: url,
			cache: true,
			onload: callback,
			onerror: fallback
		})

		$('<link>', options).appendTo('head')
	}

	// JavaScript Loader
	$.loadScript = function(url, options, callback, fallback) {

		options = $.extend(options || {}, {
			method: 'GET',
			dataType: 'script',
			cache: true,
			onload: callback,
			onerror: fallback
		});

		return jQuery.ajax(url, options);
	};

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