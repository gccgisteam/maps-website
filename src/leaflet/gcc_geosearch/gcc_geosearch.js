//Author: Alex Leith, with help from Google and others.
//Date 15/4/2013
//Simply adds a Google Places API autocomplete search box to a leaflet map.

//Places Search New
//Create control
L.Control.searchControl = L.Control.extend({
    options: {
        position: 'topcenter',
        type: 'default'
    },

    initialize: function (options) {
        this._config = {};
        L.Util.extend(this.options, options);
    },

    onAdd: function(map) {
        var type = this.options.type;

        var $controlContainer = $(map._controlContainer);

        if ($controlContainer.children('.leaflet-top.leaflet-center').length == 0) {
            $controlContainer.append('<div class="leaflet-top leaflet-center"></div>');
            map._controlCorners.topcenter = $controlContainer.children('.leaflet-top.leaflet-center').first()[0];
        }
        var container = this._div = L.DomUtil.create('div', 'leaflet-control-geosearch');
        var searchbox = document.createElement('input');
            searchbox.id = 'geosearchinput';
            searchbox.type = 'text';
            searchbox.placeholder = "Search here..." ;
        
        //var inner = this._div.innerHTML = '<input type="text" id="geosearchinput" value="Search here..." />'
        L.DomEvent
            .disableClickPropagation(this._div);

        $(container).append(searchbox);

        //Functions to either disable (onmouseover) or enable (onmouseout) the map's dragging
        function controlEnter(e) {
            map.dragging.disable();
        }
        function controlLeave() {
            map.dragging.enable();
        }
        
        var GCC_Bounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(-42.9063,147.1335),
            new google.maps.LatLng(-42.7167, 147.3444));

        var options = {
          bounds: GCC_Bounds
        };

        var input = searchbox;
        var autocomplete = new google.maps.places.Autocomplete(searchbox, options);
        var leafMarker
        google.maps.event.addListener(autocomplete, 'place_changed', function() {     
            input.className = '';
            var place = autocomplete.getPlace();
            if (!place.geometry) {
            input.className = 'notfound';
            return;
            }
            if(leafMarker){
                map.removeLayer(leafMarker);
            }
            var leafLocation = new L.LatLng(place.geometry.location.lat(),place.geometry.location.lng())
            map.setView(leafLocation, 18)
            //map.panTo(leafLocation);
            if(type === 'pin') {
                leafMarker = L.marker(leafLocation, {title: place.formatted_address}).bindPopup(place.formatted_address).addTo(map);
            } else {
                leafMarker = L.circleMarker(leafLocation, {title: place.formatted_address}).bindPopup(place.formatted_address).addTo(map);        
            }
        }); 

        var inputTags = document.getElementsByTagName("input")
        for (var i = 0; i < inputTags.length; i++) {
            inputTags[i].onmouseover = controlEnter;
            inputTags[i].onmouseout = controlLeave;
        }
        
        return this._div;
    }
});

L.control.searchControl = function (options) {
    return new L.Control.searchControl(options);
};