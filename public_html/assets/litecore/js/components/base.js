window.waitFor = (objectName, callback, attempts=100) => {

	if (typeof(window[objectName]) !== 'undefined') {
		callback(window[objectName]);
	} else {
		if (attempts) {
			setTimeout(() => {
				waitFor(objectName, callback, --attempts);
			}, 50);
		}
	}
};

waitFor('jQuery', ($) => {

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

});