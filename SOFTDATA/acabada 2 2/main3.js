
window.addEventListener('load', function(){
	new Glider(document.querySelector('.carousel__liste'), {
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: '.carousel__indicadorese',
		arrows: {
			prev: '.carousel__anteriore',
			next: '.carousel__siguientee'
		},
		responsive: [
			{
			  // screens greater than >= 775px
			  breakpoint: 450,
			  settings: {
				// Set to `auto` and provide item width to adjust to viewport
				slidesToShow: 2,
				slidesToScroll: 2
			  }
			},{
			  // screens greater than >= 1024px
			  breakpoint: 800,
			  settings: {
				slidesToShow: 4,
				slidesToScroll: 4
			  }
			}
		]
	});
});

