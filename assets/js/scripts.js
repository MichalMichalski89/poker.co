// innitialise map

	const map = L.map('map', {
		center: [51.54915256159877, -0.6180077023071624],
		zoom: 10,
		maxZoom: 20,
		minZoom: 10,
		layers: L.tileLayer('https://api.mapbox.com/styles/v1/michalmichalski89/cl2f0cxyd001314nzqn9j3ccp/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoibWljaGFsbWljaGFsc2tpODkiLCJhIjoiY2t5eGpqMHhnMGF6aTJvbXY5NjFkMzFxbyJ9.gD3VvJp3YAd73_BOzxMCXA', {

		//maxBoundsViscosity: 0.1
	})
});

	// disallow dragging out of map
	const southWest = L.latLng(-89.98155760646617, -Infinity),
		northEast = L.latLng(89.99346179538875, Infinity);
	const bounds = L.latLngBounds(southWest, northEast);
	map.setMaxBounds(bounds);