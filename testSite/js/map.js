require(["esri/Map", "esri/views/MapView", "esri/layers/GraphicsLayer", "esri/Graphic",
"esri/geometry/Point",  "esri/geometry/Circle", "esri/geometry/SpatialReference"], 
function(Map, MapView, GraphicsLayer, Graphic, Point, Circle, SpatialReference) {
  var map = new Map({
    basemap: "topo",  //For full list of pre-defined basemaps, navigate to http://arcg.is/1JVo6Wd
  });

  var view = new MapView({
    container: "map",
    map: map,
    center: [document.getElementById("Longitude").innerHTML, document.getElementById("Latitude").innerHTML], // longitude, latitude
    zoom: 12,
  });

  // create a point
  var point = {
    type: "point", 
    latitude: document.getElementById("Latitude").innerHTML,
    longitude: document.getElementById("Longitude").innerHTML
  };

  // create a circle with point and radius
  var circle = new Circle({
    center: point,
    radius: 8000,
  });
  
    // Create a symbol for drawing the point
  var markerSymbol = {
    type: "simple-marker",  // autocasts as new SimpleMarkerSymbol()
    color: [255, 51, 51]
  };

    // Create a graphic and add the geometry and symbol to it
  var graphicA = new Graphic({
    geometry: point,
    symbol: markerSymbol
  });

  // Create a symbol for drawing the point
  var fillMarkerSymbol = {
    type: "simple-fill",  // autocasts as new SimpleMarkerSymbol()
    color: [255, 51, 51, 0.5],
    style: "solid",
    outline: {
      color: "black",
      width: 1
    }
  };

  var graphicB = new Graphic({
    geometry: circle,
    symbol: fillMarkerSymbol
  });

  // Add graphic when GraphicsLayer is constructed
  var layer = new GraphicsLayer({
    graphics: [graphicA, graphicB]
  });

  // Add GraphicsLayer to map
  map.add(layer);
});