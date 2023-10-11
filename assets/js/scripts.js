// innitialise map

const map = L.map('map', {
	center: [51.54, -0.61],
	zoom: 10,	
	fullscreenControl: true,
	fullscreenControlOptions: {
	  position: 'topleft'
	}
});

var tiles = new L.tileLayer('https://api.mapbox.com/styles/v1/michalmichalski89/cl2f0fdqa001315nvd0r0nxhs/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoibWljaGFsbWljaGFsc2tpODkiLCJhIjoiY2t5eGpqMHhnMGF6aTJvbXY5NjFkMzFxbyJ9.gD3VvJp3YAd73_BOzxMCXA', {
attribution: '&copy;',
minZoom: '9'}).addTo(map);


// disallow dragging out of map
const southWest = L.latLng(51.13, 0.26),
	northEast = L.latLng(51.84, -1.62);
const bounds = L.latLngBounds(southWest, northEast);
	map.setMaxBounds(bounds);

// markers
var redMarker = L.ExtraMarkers.icon({
icon: 'fa-beer-mug-empty',
markerColor: 'green',
shape: 'square',
prefix: 'fa'
});

var greyMarker = L.ExtraMarkers.icon({
icon: 'fa-beer-mug-empty',
markerColor: 'blue',
shape: 'square',
prefix: 'fa'
});

L.marker([51.54918591996306, -0.618029159994078], {icon: redMarker}).addTo(map)
.bindPopup('<b><u>The Emperor</u></b>' + '<br> Game night: <b>Monday</b>, <b>7pm start</b>' + '<br> Address: <b>Blackpond Ln, Slough SL2 3EG</b>');
	
L.marker([51.60217722041262, -0.6334146356043768], {icon: greyMarker}).addTo(map)
.bindPopup('<b><u>The Swan</u></b>' + '<br> Game night: <b>Sunday</b>, <b>7pm start</b>' + '<br> Address: <b>Blackpond Ln, Slough SL2 3EG</b>');
