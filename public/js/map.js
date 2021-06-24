require(["esri/Map", "esri/views/MapView", "esri/layers/GraphicsLayer", "esri/Graphic",
"esri/geometry/Point",  "esri/geometry/Circle"],

function (Map, MapView, GraphicsLayer, Graphic, Point, Circle) {

  let map = new Map({
  basemap: "topo",  //For full list of pre-defined basemaps, navigate to http://arcg.is/1JVo6Wd
  });

  let latitude = document.getElementById("map-latitude").value;
  let longitude = document.getElementById("map-longitude").value;

  // create a point
  let point = {
    type: "point",
    latitude: latitude,
    longitude: longitude
  };

  // create a circle with point and radius
  let circle = new Circle({
    center: point,
    radius: getUncertainty(latitude, longitude),
    geodesic: true
  });

  // Create a symbol for drawing the point
  let markerSymbol = {
    type: "simple-marker",  // autocasts as new SimpleMarkerSymbol()
    color: [255, 51, 51]
  };

  // Create a graphic and add the geometry and symbol to it
  let graphicA = new Graphic({
    geometry: point,
    symbol: markerSymbol
  });

  // Create a symbol for drawing the point
  let fillMarkerSymbol = {
    type: "simple-fill",  // auto casts as new SimpleMarkerSymbol()
    color: [255, 51, 51, 0.5],
    style: "solid",
    outline: {
      color: "black",
      width: 1
    }
  };

  let graphicB = new Graphic({
    geometry: circle,
    symbol: fillMarkerSymbol
  });

  let view = new MapView({
    container: "viewDiv",
    map: map,
    center: [
      longitude,
      latitude
    ] // longitude, latitude
  });

  if(circle.radius > 2000){
    view.extent = circle.extent;
  } else {
    view.zoom = 13;
  }


  // Add graphic when GraphicsLayer is constructed
  let layer = new GraphicsLayer({
    graphics: [graphicA, graphicB]
  });

  // Add GraphicsLayer to map
  map.add(layer);


  //calculate uncertainty from lat and long data
  function getUncertainty(lat, long){
  if(typeof lat == "string" && typeof long == "string"){
    var latprecision = lat.trim().substr(lat.trim().indexOf(".")+1).length;
    var longprecision = long.trim().substr(long.trim().indexOf(".")+1).length;
    if(lat.trim().indexOf(".")<0){
      latprecision = 0;
    }
    if (long.trim().indexOf(".")<0){
      longprecision = 0;
    }
    if(latprecision < 6 || longprecision < 6){
      if(latprecision < longprecision){
        return Math.round((111320*Math.cos(parseFloat(lat.trim())*Math.PI/180))/Math.pow(10,latprecision));
      }
      else {
        return Math.round((111320*Math.cos(parseFloat(lat.trim())*Math.PI/180))/Math.pow(10,longprecision));
      }
    }
  }
  return 1;
  }
});