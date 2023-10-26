// event listener to collapse menu on link click


// const menuToggle = document.getElementById('navbarSupportedContent')
// const bsCollapse = new bootstrap.Collapse(menuToggle)
$('document').ready(function(){


	document.querySelectorAll('#navbar-menu .page-scroll').forEach((l) => {
		l.addEventListener('click', function() {
			
			console.log('aaaaaaaaaaaa')});
	});
})





// innitialise map

const map = L.map('map', {
	center: [51.54, -0.61],
	zoom: 10,	
	scrollWheelZoom: false,
	fullscreenControl: true,
	fullscreenControlOptions: {
	  position: 'topleft'
	}
});

var tiles = new L.tileLayer('https://api.mapbox.com/styles/v1/michalmichalski89/cl2f0fdqa001315nvd0r0nxhs/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoibWljaGFsbWljaGFsc2tpODkiLCJhIjoiY2t5eGpqMHhnMGF6aTJvbXY5NjFkMzFxbyJ9.gD3VvJp3YAd73_BOzxMCXA', {
attribution: '&copy;',
minZoom: '9'}).addTo(map);


// disallow dragging out of map
const southWest = L.latLng(51.195714773153725, -0.1660487313749751),
	northEast = L.latLng(51.904867277536745, -1.1469383179205548);
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

L.marker([51.48072780946211, -0.6061361693123507], {icon: redMarker}).addTo(map)
.bindPopup('<b><u>The Corner House</u></b>' + '<br> Game night: <b>Wednesday, 7pm start</b>' + '<br> Address: <b>22 Sheet St, Windsor SL4 1BG </b>');
	
L.marker([51.60217722041262, -0.6334146356043768], {icon: greyMarker}).addTo(map)
.bindPopup('<b><u>The Swan</u></b>' + '<br> Game night: <b>TBC</b>' + '<br> Address: <b>60 London End, Beaconsfield HP9 2JD </b>');

L.marker([51.60153276104829, -0.7090311693123507], {icon: greyMarker}).addTo(map)
.bindPopup('<b><u>The Cherry Tree</u></b>' + '<br> Game night: <b>TBC</b>' + '<br> Address: <b>5 Straight Bit, Flackwell Heath, High Wycombe HP10 9LS</b>');

L.marker([51.53375035588169, -0.41654027671912314], {icon: greyMarker}).addTo(map)
.bindPopup('<b><u>Hayes RFC</u></b>' + '<br> Game night: <b>TBC</b>' + '<br> Address: <b>Kingshill Ave, Hayes UB4 8BZ </b>');

L.marker([51.45002841492482, -0.9385975846561755], {icon: greyMarker}).addTo(map)
.bindPopup('<b><u>Palmer Tavern</u></b>' + '<br> Game night: <b>Thursday, 7.30pm start</b>' + '<br> Address: <b>128 Wokingham Rd, Reading RG6 1JL</b>');


// calc player efficiency
function playerEff(players, position){
	var eff  = (100/(players-1))*(players-position)
	return eff;
}

