//Define layers
var LISTTopographic = new L.tileLayer("https://services.thelist.tas.gov.au/arcgis/rest/services/Basemaps/Topographic/ImageServer/tile/{z}/{y}/{x}", {
    attribution: "Topographic Basemap from <a href=http://www.thelist.tas.gov.au>the LIST</a> &copy State of Tasmania",
    maxZoom: 20,
    maxNativeZoom: 18
});

var LISTAerial = new L.tileLayer("https://services.thelist.tas.gov.au/arcgis/rest/services/Basemaps/Orthophoto/ImageServer/tile/{z}/{y}/{x}", {
    attribution: "Base Imagery from <a href=http://www.thelist.tas.gov.au>the LIST</a> &copy State of Tasmania",
    maxZoom: 20,
    maxNativeZoom: 19
});