// event listener to collapse menu on link click


// const menuToggle = document.getElementById('navbarSupportedContent')
// const bsCollapse = new bootstrap.Collapse(menuToggle)

// $(function(){
// 	document.querySelectorAll('#navbar-menu .page-scroll').forEach((l) => {
// 		l.addEventListener('click', function() {
// 			console.log('navbar link clicked')});
// 			$('#navbar-menu').collapse('hide');
			
// 	});
// });

$(document).keydown(function () {
	$('#navbar-menu').collapse('hide');
});




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

// Marker icons
var blueMarker = L.ExtraMarkers.icon({
	icon: 'fa-beer-mug-empty',
	markerColor: 'green',
	shape: 'square',
	prefix: 'fa'
  });
  
// Function to create a marker icon dynamically
function createMarkerIcon(color) {
  return L.ExtraMarkers.icon({
    icon: 'fa-beer-mug-empty',
    markerColor: color,
    shape: 'square',
    prefix: 'fa'
  });
}

// Fetch venue locations dynamically
fetch('../assets/php/get_venues.php')
  .then(response => response.json())
  .then(data => {
    data.forEach(venue => {
      let markerIcon = createMarkerIcon(venue.marker_color || 'blue'); // fallback to 'blue' if undefined

      L.marker([venue.lat, venue.lon], { icon: markerIcon })
        .addTo(map)
        .bindPopup(
          '<b><u>' + venue.name + '</u></b>' +
          (venue.address ? '<br> Address: <b>' + venue.address + '</b>' : '')
        );
    });
  })
  .catch(error => console.error('Error loading venue markers:', error));


var ass = [[54.559322, -5.767822], [56.1210604, -3.021240]];

// create an orange rectangle
L.rectangle(ass, {color: "#ff7800", weight: 1}).addTo(map);




// calc player efficiency
function playerEff(players, position){
	var eff  = (100/(players-1))*(players-position)
	return eff;
}

// root/index alerts

document.addEventListener("DOMContentLoaded", function () {
  // Helper to get URL query parameters
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  // Helper to show an alert message
  function showAlert(message, type = "info") {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} text-center`;
    alertDiv.role = "alert";
    alertDiv.style.position = "fixed";
    alertDiv.style.top = "20px";
    alertDiv.style.left = "50%";
    alertDiv.style.transform = "translateX(-50%)";
    alertDiv.style.zIndex = "9999";
    alertDiv.textContent = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
      alertDiv.remove();
    }, 2000);
  }

  // Check for session expired param
  if (getQueryParam("session_expired") === "1") {
    showAlert("Your session expired. Please log in again.", "warning");
  }

  // Check for successful registration param
  if (getQueryParam("registered") === "1") {
    showAlert("You registered successfully! Please log in below.", "success");
  }
});
