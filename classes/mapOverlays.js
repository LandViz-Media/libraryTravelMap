/*
var countriesMap = L.geoJson(countries, {
    style: {
        color: '#000',
        weight: 0.5,
        opacity: 1,
        fillColor: '#fff',
        fillOpacity: 1
    }
})
*/

var graticuleOutline =  L.graticule({
    sphere: true,
    style: {
        color: '#777',
        opacity: 1,
        fillColor: '#ccf',
        fillOpacity: 0,
        weight: 2
    }
});

var graticule45 =  L.graticule({
    sphere: false,
    interval: 45,
    style: {
        color: '#777',
        weight: 1,
        opacity: 0.5
    }
});

//show prime meridiean and Equator
var graticule180 =  L.graticule({
	sphere: false,
	interval: 180,
	style: {
	    color: '#777',
	    weight: 2,
	    opacity: 1,
	    fillOpacity: 0,
	}
});


/*

// Specify bold red lines instead of thin grey lines
L.graticule({
	interval: 42,
    style: {
        color: '#f00',
        weight: 1
    }
}).addTo(map);
*/

