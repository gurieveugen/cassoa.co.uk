var geocoder, map;
var init_ind = 0;
var def_address = 'UK';
var zoom = 6;
if (gaddresses.length > 0) {
	def_address = gaddresses[0];
	zoom = 9;
}
jQuery(document).ready(function() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		geocoder = new GClientGeocoder();
		geocoder.getLatLng(
			def_address,
			function(point) {
				if (point) {
					map.setCenter(point, zoom);
				}
			}
		);
		if (gaddresses.length > 0) {
			set_markers();
		}
	}
});
function set_markers() {
	if (init_ind < gaddresses.length) {
		var addr = gaddresses[init_ind];
		var title = mtitles[init_ind];
		geocoder.getLatLng(
			addr,
			function(point) {
				if (point) {
					var marker = new GMarker(point);
					GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(title);});
					map.addOverlay(marker);
					init_ind++;
					set_markers();
				}
			}
		);
	}
}
