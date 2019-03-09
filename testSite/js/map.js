    require(["esri/Map", "esri/views/MapView"], function(Map, MapView) {
    var map = new Map({
        basemap: "streets",  //For full list of pre-defined basemaps, navigate to http://arcg.is/1JVo6Wd
    });

    var view = new MapView({
        container: "map",
        map: map,
        center: [15, 65], // longitude, latitude
        zoom: 4
    });
    });