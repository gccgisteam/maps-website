/*
 * L.Control.GeoSearch - search for an address and zoom to it's location
 * https://github.com/smeijer/leaflet.control.geosearch
 */

L.GeoSearch = {};
L.GeoSearch.Provider = {};

// MSIE needs cors support
jQuery.support.cors = true;

L.GeoSearch.Result = function (x, y, label) {
    this.X = x;
    this.Y = y;
    this.Label = label;
};

L.Control.GeoSearch = L.Control.extend({
    options: {
        position: 'topcenter'
    },

    initialize: function (options) {

        this._config = {};
        this.setConfig(options);
    },

    setConfig: function (options) {
        this._config = {
            'country': options.country || '',
            'provider': options.provider,
            
            'searchLabel': options.searchLabel || 'search for address...',
            'notFoundMessage' : options.notFoundMessage || 'Sorry, that address could not be found.',
            'messageHideDelay': options.messageHideDelay || 3000,
            'zoomLevel': options.zoomLevel || 18
        };
    },

    onAdd: function (map) {
    	$.ajax({
            url: "https://maps.googleapis.com/maps/api/js?v=3&callback=onLoadGoogleApiCallback&sensor=false",
            dataType: "script"
        });
        var $controlContainer = $(map._controlContainer);

        if ($controlContainer.children('.leaflet-top.leaflet-center').length == 0) {
            $controlContainer.append('<div class="leaflet-top leaflet-center"></div>');
            map._controlCorners.topcenter = $controlContainer.children('.leaflet-top.leaflet-center').first()[0];
        }

        this._map = map;
        this._container = L.DomUtil.create('div', 'leaflet-control-geosearch');

        var searchbox = document.createElement('input');
        searchbox.id = 'leaflet-control-geosearch-qry';
        searchbox.type = 'text';
        searchbox.placeholder = this._config.searchLabel;
        this._searchbox = searchbox;

        L.DomEvent.disableClickPropagation(this._container);

	    var GCC_Bounds = new google.maps.LatLngBounds(
	      new google.maps.LatLng(-42.9063,147.1335),
	      new google.maps.LatLng(-42.7167, 147.3444));

	    var options = {
	      bounds: GCC_Bounds
	    };

	    var input = document.getElementById('leaflet-control-geosearch-qry');
	    var autocomplete = new google.maps.places.Autocomplete(input, options);

	    google.maps.event.addListener(autocomplete, 'place_changed', function() {          
	      input.className = '';
	      var place = autocomplete.getPlace();
	      if (!place.geometry) {
	        input.className = 'notfound';
	        return;
	      }
	      map.setZoom(18);
	      map.panTo(new L.LatLng(place.geometry.location.lat(),place.geometry.location.lng()));
	    });

        return this._container;
    }
});