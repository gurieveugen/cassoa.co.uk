$(function() {
	if (window.PIE) {
		$('.slider .slide-1 a, .form-enter-postcode input[type="submit"], .form-enter-postcode input, #your-profile input[type="text"], #your-profile input[type="password"], #your-profile input[type="email"], #your-profile textarea, #your-profile input[type="submit"], .loginform input, .jqTransformSelectWrapper ').each(function() {
			PIE.attach(this);
		});
	}
});