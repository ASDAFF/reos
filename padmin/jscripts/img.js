//Forma de Uso:
// <a href=\"javascript:CargarFoto('$dir_fotos/$file','550','400')\">[texto o imagen]</a>

function CargarFoto(img, ancho, alto){
  derecha=(screen.width-ancho)/2;
  arriba=(screen.height-alto)/2;
  string="toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width="+ancho+",height="+alto+",left="+derecha+",top="+arriba+"";
  top.consoleRef=window.open('','mywin',string);
  top.consoleRef.document.writeln(
  '<html><head><title>Image</title></head>'
   +'<body style="margin: 0cm 0cm 0cm 0cm;background-color:white" onLoad="self.focus()">'
   +'<div style="text-align:center"><img src="'+img+'" alt=""/></div>'
   +'</body></html>');
   top.consoleRef.document.close();
}
