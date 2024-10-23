
// Stylesheet Loader
$.loadStylesheet = function(url, options) {

	options = $.extend(options || {}, {
		rel: 'stylesheet',
		href: url,
		cache: true
	});

	$('<link>', options).appendTo('head');
}

// JavaScript Loader
$.loadScript = function(url, options) {

	options = $.extend(options || {}, {
		method: 'GET',
		dataType: 'script',
		cache: true
	});

	return jQuery.ajax(url, options);
};

// Escape HTML
function escapeHTML(string) {
	let entityMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;'
	};
	return String(string).replace(/[&<>"'\/]/g, function (s) {
		return entityMap[s];
	});
};

// Money Formatting
Number.prototype.toMoney = function(use_html = false) {
	var n = this,
		c = _env.session.currency.decimals,
		d = _env.session.language.decimal_point,
		t = _env.session.language.thousands_separator,
		p = _env.session.currency.prefix,
		x = _env.session.currency.suffix,
		u = _env.session.currency.code,
		s = n < 0 ? '-' : '',
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '',
		f = n - i,
		j = (j = i.length) > 3 ? j % 3 : 0;

	return s + p + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(f).toFixed(c).slice(2) : '') + x;
}

// Keep-alive
let keepAlive = setInterval(function(){
	$.get({
		url: window._env.platform.path + 'ajax/keep_alive',
		cache: false
	});
}, 60e3);

// Alerts
$('body').on('click', '.alert .close', function(e){
	e.preventDefault();
	$(this).closest('.alert').fadeOut('fast', function(){$(this).remove()});
});

// Form required asterix
$(':input[required]').closest('.form-group').addClass('required');

// Detect scroll direction
let lastScrollTop = 0;
$(document).on('scroll', function(){
	var scrollTop = $(this).scrollTop();
	if (scrollTop > lastScrollTop) {
		$('body').addClass('scrolling-down');
	} else {
		$('body').removeClass('scrolling-down');
	}
	lastScrollTop = (scrollTop < 0) ? 0 : scrollTop;
});

// Scroll Up
$(window).scroll(function(){
	if ($(this).scrollTop() > 100) {
		$('#scroll-up').fadeIn();
	} else {
		$('#scroll-up').fadeOut();
	}
});

$('#scroll-up').click(function(){
	$('html, body').animate({scrollTop: 0}, 1000, 'swing');
	return false;
});
