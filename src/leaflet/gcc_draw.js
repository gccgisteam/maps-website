function updatePopup(e) {
    var type = e.layerType,
        length = 0,
        area = 0,
        layer = e.layer;

    if (type === 'marker') {
        var ll = e.layer.getLatLng();
        var j = new Proj4js.Point(ll.lng,ll.lat);  
        Proj4js.transform(src, dst, j);
  
        layer.bindPopup('<p><b>Coordinates</b><br>GDA94, MGA Zone 55: ' + Math.round(j.x*1000)/1000 + ', '+Math.round(j.y*1000)/1000 + '<br/>WGS84 lat, long: ' + Math.round(ll.lat*10000)/10000 + ', '+Math.round(ll.lng*10000)/10000 + '</p>');
    }
    if(type ==='polyline') {
        length = lineLength(e.layer._latlngs);
        layer.bindPopup('Length is: '+length);            
    }
    if (type === 'polygon' || type === 'rectangle') {
        area = polygonArea(e.layer._latlngs);
        layer.bindPopup('Area is: '+area);
    }
    drawnItems.addLayer(layer);        
    layer.openPopup();
}

function lineLength(c) 
{ 
  var length = 0;         // Accumulates area in the loop
  var numPoints = c.length
  var j = new Proj4js.Point(c[0].lng,c[0].lat);  
  Proj4js.transform(src, dst, j);
  
  for (i=1; i<numPoints; i++)
    {      
        var k = new Proj4js.Point(c[i].lng,c[i].lat);  
        Proj4js.transform(src, dst, k); 
        length = length + Math.sqrt((j.x-k.x)*(j.x-k.x) + (j.y-k.y)*(j.y-k.y)); 
        j = new Proj4js.Point(k.x,k.y);
    }
  var lengthString = '';
  if(length > 2000) {
    var lengthString = lengthString = (Math.round((length/1000)*100)/100).toString() + ' km';
  } else {
    var lengthString = lengthString = (Math.round(length*100)/100).toString() + ' m';
  }
  return  lengthString;
}

function polygonArea(c) 
{ 
  area = 0;         // Accumulates area in the loop
  var numPoints = c.length
  var j = new Proj4js.Point(c[numPoints-1].lng,c[numPoints-1].lat);  
  Proj4js.transform(src, dst, j);
  
  for (i=0; i<numPoints; i++)
    {      
        var k = new Proj4js.Point(c[i].lng,c[i].lat);  
        Proj4js.transform(src, dst, k); 
        area = area +  (j.x+k.x) * (j.y-k.y); 
        j = new Proj4js.Point(k.x,k.y);
    }
  var areaString = '';
  area = Math.abs(area);
  if (area > 10000)
  { 
    areaString = (Math.round(area/200,2)/100).toString() + ' Ha';
  }
  else
  {
    areaString = Math.round(area/2).toString() + ' m';
  }
  return  areaString;
}