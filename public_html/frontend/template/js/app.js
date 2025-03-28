/*!
 * LiteCore v1.0.0 - Lightweight website core framework built with PHP, jQuery and HTML.
 * @link https://www.litecore.dev/
 * @license CC-BY-ND-4.0
 * @author T. Almroth
 */

+waitFor('jQuery', ($) => {

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