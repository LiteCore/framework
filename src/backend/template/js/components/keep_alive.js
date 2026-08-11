waitFor('jQuery', ($) => {

	// Keep session alive
	let timer_keep_alive = setInterval(function(){
		$.get({
			url: window._env.platform.path + 'ajax/keep_alive',
			cache: false
		});
	}, 60e3)

});