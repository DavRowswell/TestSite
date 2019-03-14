require(["esri/Map", "esri/views/MapView", "esri/layers/GraphicsLayer", "esri/Graphic",
"esri/geometry/Point",  "esri/geometry/Circle", "esri/geometry/SpatialReference", ], 
function(Map, MapView, GraphicsLayer, Graphic, Point, Circle, SpatialReference, Zoom) {
  var map = new Map({
    basemap: "topo",  //For full list of pre-defined basemaps, navigate to http://arcg.is/1JVo6Wd
    zoom: 3,
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
    radius: getUncertainty(document.getElementById("Latitude").innerHTML, document.getElementById("Longitude").innerHTML),
    geodesic: true
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

  var view = new MapView({
    container: "viewDiv",
    map: map,
    center: [
      document.getElementById("Longitude").innerHTML, 
      document.getElementById("Latitude").innerHTML
    ], // longitude, latitude
    extent: circle.extent
  });

  // Sets the scale of map when first initialized
  view.scale = 24000;

  // Add graphic when GraphicsLayer is constructed
  var layer = new GraphicsLayer({
    graphics: [graphicA, graphicB]
  });

  // Add GraphicsLayer to map
  map.add(layer);


  //calcualte uncertainty from lat and long data
  function getUncertainty(lat, long){
    if(typeof lat == "string" && typeof long == "string"){
      var latprecision = lat.trim().substr(lat.trim().indexOf(".")+1).length;
      var longprecision = long.trim().substr(long.trim().indexOf(".")+1).length;
      if(latprecision < 6 || longprecision < 6){
        if(latprecision < longprecision){
          return Math.round((111320*Math.cos(parseFloat(lat.trim())))/Math.pow(10,latprecision));
        }
        else {
          return Math.round((111320*Math.cos(parseFloat(lat.trim())))/Math.pow(10,longprecision));
        }
      }
    }
    return 1;
  }
});