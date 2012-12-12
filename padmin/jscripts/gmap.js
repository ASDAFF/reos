var bIsClick;
/**
 * Crea el control xmlhttp
 */
function CrearXmlHttp()
{ 
	/* Crea el objeto AJAX. Esta funcion es generica para cualquier utilidad de este tipo, por
	lo que se puede copiar tal como esta aqui */
	var xmlhttp = false;
	try	{
		// Creacion del objeto AJAX para navegadores no IE
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch(e) {
		try {
			// Creacion del objet AJAX para IE
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		} catch(E) {
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') xmlhttp = new XMLHttpRequest();
		}
	}
	return xmlhttp;
}
/**
 *
 */
function loadmap(Lat, Lng, Level, Url) {
	  bIsClick = false;
      if (GBrowserIsCompatible()) {
           
        var map = new GMap2(document.getElementById('map'));
                
        //map.disableDragging();
        map.addControl(new GLargeMapControl()); 
        map.addControl(new GMapTypeControl());
		 
		
		GEvent.addListener(map, 'moveend', function() {	
		  if (!bIsClick) getImmoPoints(map, Url);
		  bIsClick = false;		 
		}); 
		
		map.setCenter(new GLatLng(Lat,Lng), Level);              		
      }
}
/**
 *
 */
function getImmoPoints(map, Url){ 
    //get rid of existing markers
    map.clearOverlays();
    loadPointsFromXML(map, Url);
}
/**
 *
 */
function AddMarker(map, xpoint, ypoint, details, icon){
	var point = new GLatLng(xpoint, ypoint);
    var marker = new GMarker(point, icon);
    map.addOverlay(marker);
    GEvent.addListener(marker, "click", function() {
        bIsClick = true;
        marker.openInfoWindowHtml(details);
    });
}
/**
 *
 */
function loadPointsFromXML(map, Url){

    	var bounds = map.getBounds();
    	var southWest = bounds.getSouthWest();   	
 		var northEast = bounds.getNorthEast();
 		
	 	var maxx = northEast.lat();
	 	var minx = southWest.lat(); 
	 	var maxy = northEast.lng();
	 	var miny = southWest.lng();
	 	
	 	var level = map.getBoundsZoomLevel(bounds); 		
    	
        var request = CrearXmlHttp();
        //grab the xml
        //alert ("http://" +Url+ "pg=xpoints&level="+level+"&minx="+minx+"&maxx="+maxx+"&miny="+miny+"&maxy="+maxy+"&xout=1");
        request.open("GET", "http://" +Url+ "pg=xpoints&level="+level+"&minx="+minx+"&maxx="+maxx+"&miny="+miny+"&maxy="+maxy+"&xout=1", true);
        request.onreadystatechange = function() 
        {
            if (request.readyState == 4) 
            {
                var xmlDoc = request.responseXML;
                parseXML(map, xmlDoc, Url);
            }
        }
        request.send(null);   
}
/**
*
*/
function parseXML(map,xmlDoc, Url){

        		// Creamos nuestro icono de marcador "pequeño".
		var blueIcon = new GIcon(G_DEFAULT_ICON);
		
		var strPathImage = Url.substring(0,Url.lastIndexOf("/"));
		
		blueIcon.image = "http://"+strPathImage+"/padmin/images/map.png";
		                
		// Configuramos nuestro objeto GMarkerOptions.
		markerOptions = { icon:blueIcon };

        var markers = xmlDoc.documentElement.getElementsByTagName('row');
		
        for (var i = 0; i < markers.length; i++)  { 
              
        	var txt_x = markers[i].getElementsByTagName('txt_x')[0].childNodes[0].nodeValue;
        	var txt_y = markers[i].getElementsByTagName('txt_y')[0].childNodes[0].nodeValue;
        	//var id_immo = markers[i].getElementsByTagName('id_immo')[0].childNodes[0].nodeValue;    
        	var city = markers[i].getElementsByTagName('txt_poblacion')[0].childNodes[0].nodeValue; 
        	var trans = markers[i].getElementsByTagName('tp_servicio')[0].childNodes[0].nodeValue; 
        	var type = markers[i].getElementsByTagName('tp_propiedad')[0].childNodes[0].nodeValue;
        	var price = ''; 
        	if (markers[i].getElementsByTagName('precio').length>0)
        		price = markers[i].getElementsByTagName('precio')[0].childNodes[0].nodeValue; 
        	var tp_price = ''; 
        	if (markers[i].getElementsByTagName('tp_price').length>0)
        	 	tp_price = markers[i].getElementsByTagName('tp_price')[0].childNodes[0].nodeValue;
        	 	
        	var ref = markers[i].getElementsByTagName('ref_immo')[0].childNodes[0].nodeValue;
        	
        	var link = '<a href="http://'+ Url +  markers[i].getElementsByTagName('url')[0].childNodes[0].nodeValue+'">'+ref+'</a>'; 	    	
        	         
            AddMarker(map, txt_x, txt_y,  link+ ' ' +type+' '+trans+'<br/>'+city+'<br/>'+price+' '+tp_price, markerOptions);
        }
}



