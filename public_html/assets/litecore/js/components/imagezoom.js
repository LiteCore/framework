/*!
 * jQuery Plugin developed by Mario Duarte
 * https://github.com/Mario-Duarte/image-zoom-plugin/
 * Simple jQuery plugin that converts an image into a click to zoom image
 */
waitFor('jQuery', ($) => {

  $.fn.imageZoom = function (options) {

    // Default settings for the zoom level
    const settings = $.extend({
      zoom: 150
    }, options)

    // Main html template for the zoom in plugin
    const $imageObj = $([
			'<figure class="containerZoom">',
			'	<img id="imageZoom">',
			'</figure>',
		].join('\n'))

		$imageObj.css({
			'background-image': `url('${$(this).attr('src')}')`,
			'background-size': `${settings.zoom}%`,
			'background-position': '50% 50%',
			'position': 'relative',
			'width': '100%',
			'overflow': 'hidden',
			'cursor': 'zoom-in',
			'margin': 0,
		})

		$imageObj.find('img')
			.attr('src', $(this).attr('src'))
			.attr('alt', $(this).attr('alt'))
			.css({
				'transition':'opacity .5s',
				'display':'block',
				'width':'100%',
			})

    // Where all the magic happens, This will detect the position of your mouse
    // in relation to the image and pan the zoomed in background image in the same direction
    const zoomIn = (e) => {
      const zoomer = e.currentTarget
      let offsetX, offsetY

			switch (e.type) {
				case 'mousemove':
					offsetX = e.offsetX || e.clientX - $(zoomer).offset().left
					offsetY = e.offsetY || e.clientY - $(zoomer).offset().top
					break

				case 'touchmove':
					e.preventDefault(); // Prevent default touch behavior (scrolling)
					offsetX = Math.min(Math.max(0, e.originalEvent.touches[0].pageX - $(zoomer).offset().left), zoomer.offsetWidth)
					offsetY = Math.min(Math.max(0, e.originalEvent.touches[0].pageY - $(zoomer).offset().top), zoomer.offsetHeight)
					break
      }

      const x = offsetX / zoomer.offsetWidth * 100
      const y = offsetY / zoomer.offsetHeight * 100

      $(zoomer).css({
        'background-position': `${x}% ${y}%`,
      })
    }

    let newElm;

    if (this[0].nodeName === 'IMG') {
      newElm = $(this).replaceWith($imageObj)
      $(this).on({

				'click touchstart': function(e) {
					if (!("zoom" in $imageObj)) {
						$imageObj.zoom = false
					}
					if ($imageObj.zoom) {
						$imageObj.zoom = false
						$(this).removeClass('active')
					} else {
						$imageObj.zoom = true;
						$(this).addClass('active')
						$(this).find('img').css('opacity', 0)
						zoomIn(e)
					}
				},

				'mousemove touchmove': function(e) {
					$imageObj.zoom ? zoomIn(e) : null
				},

				'mouseleave touchend': function() {
					$imageObj.zoom = false
					$(this).removeClass('active')
				}
			})
    } else {
      newElm = $(this)
    }

    return newElm;
  };
})