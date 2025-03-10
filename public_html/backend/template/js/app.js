/*!
 * LiteCore v1.0.0 - Lightweight website core framework built with PHP, jQuery and HTML.
 * @link https://www.litecore.dev/
 * @license CC-BY-ND-4.0
 * @author T. Almroth
 */

// Filter
waitFor('jQuery', function($){

	$('#sidebar input[name="filter"]').on({

		'input': function(){

			let query = $(this).val();

			if ($(this).val() == '') {
				$('#box-apps-menu .app').css('display', 'block');
				return;
			}

			$('#box-apps-menu .app').each(function(){
				var regex = new RegExp(''+ query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')  +'', 'ig');
				console.log()
				if (regex.test($(this).text())) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}
	});

	// AJAX Search
	let timer_ajax_search = null;
	let xhr_search = null;

	$('#search input[name="query"]').on({

		'focus': function(){
			$('#search.dropdown').addClass('open');
		},

		'blur': function(){
			$('#search.dropdown').removeClass('open');
		},

		'input': function(){

			if (xhr_search) {
				xhr_search.abort();
			}

			let search_field = this;

			if ($(this).val() == '') {
				$('#search .results').html('');
				return;
			}

			if (!$('#search .loader-wrapper').length) {
				$('#search .results').show().html([
					'<div class="loader-wrapper text-center">',
					'  <div class="loader" style="width: 48px; height: 48px;"></div>',
					'</div>'
				].join('\n'));
			}

			clearTimeout(timer_ajax_search);

			timer_ajax_search = setTimeout(function() {
				xhr_search = $.ajax({
					type: 'get',
					async: true,
					cache: false,
					url: window._env.backend.url + 'search_results.json?query=' + $(search_field).val(),
					dataType: 'json',

					beforeSend: function(jqXHR) {
						jqXHR.overrideMimeType('text/html;charset=' + $('html meta[charset]').attr('charset'));
					},

					error: function(jqXHR, textStatus, errorThrown) {
						$('#search .results').text(textStatus + ': ' + errorThrown);
					},

					success: function(json) {

						$('#search .results').html('');

						if (!$('#search input[name="query"]').val()) return;

						$.each(json, function(i, group){

							if (group.results.length) {

								$('#search .results').append(
									'<h4>'+ group.name +'</h4>' +
									'<ul class="flex flex-rows" data-group="'+ group.name +'"></ul>'
								);

								$.each(group.results, function(i, result){

									var $li = $([
										'<li class="result">',
										'  <a class="list-group-item" href="'+ result.link +'" style="border-inline-start: 3px solid '+ group.theme.color +';">',
										'    <small class="id float-end">#'+ result.id +'</small>',
										'    <div class="title">'+ result.title +'</div>',
										'    <div class="description"><small>'+ result.description +'</small></div>',
										'  </a>',
										'</li>'
									].join('\n'));

									$('#search .results ul[data-group="'+ group.name +'"]').append($li);
								});
							}
						});

						if ($('#search .results').html() == '') {
							$('#search .results').html('<p class="text-center no-results"><em>:(</em></p>');
						}
					},
				});
			}, 500);
		}
	});

});


waitFor('jQuery', function($){

	$('button[name="font_size"]').on('click', function(){
		let new_size = parseInt($(':root').css('--default-text-size').split('px')[0]) + (($(this).val() == 'increase') ? 1 : -1);
		$(':root').css('--default-text-size', new_size + 'px');
		document.cookie = 'font_size='+ new_size +';Path=<?php echo WS_DIR_APP; ?>;Max-Age=2592000';
	});

	$('input[name="dark_mode"]').on('click', function(){
		if ($(this).val() == 1) {
			document.cookie = 'dark_mode=1;Path=<?php echo WS_DIR_APP; ?>;Max-Age=2592000';
			$('html').addClass('dark-mode');
		} else {
			document.cookie = 'dark_mode=0;Path=<?php echo WS_DIR_APP; ?>;Max-Age=2592000';
			$('html').removeClass('dark-mode');
		}
	});

});