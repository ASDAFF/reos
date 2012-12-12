/**
*This is a JavaScript library that will allow you to easily add some basic DHTML
*drop-down picker functionality to your Notes forms.
*
*HTML Example:
*
*Start : <input name="fieldname">
*<input type=button value="select" onclick="displayPicker('name','fieldname','sourcefieldname', 'Url');">
*
*That will cause the picker to be displayed beneath the Start field and
*any  that is chosen will up the value of that field. If you'd rather have the
*picker display beneath the button that was clicked, you can code the button
*like this:
*
*<input type=button value="select" onclick="displayPicker('Start', this);">
*
**/
var PickerObj = new Object();
var FdName = '';

function displayPicker(PickerDivID, FieldName, PickerUrl) {

  var targetField = document.getElementsByName (FieldName).item(0);
  FdName = FieldName;
  //var displayBelowThisObject = targetField;

  var displayBelowThisObject = document.getElementsByName (PickerDivID+"_node").item(0);

  //PickerUrl = Url;
  //PickerField = Field;
  //PickerDivID=PickerName;

  var tmp = new Object;
  tmp["div"] = PickerDivID + "_div";
  tmp["ifr"] = PickerDivID;
  tmp["field"] = FieldName;
  PickerObj[PickerDivID]=tmp;

  var x = displayBelowThisObject.offsetLeft;
  var y = displayBelowThisObject.offsetTop + displayBelowThisObject.offsetHeight ;

  // deal with elements inside tables and such
  var parent = displayBelowThisObject;
  while (parent.offsetParent) {
    parent = parent.offsetParent;
    x += parent.offsetLeft;
    y += parent.offsetTop ;
  }
  drawPicker(PickerDivID,targetField, x, y, PickerUrl);
}
/**
*Draw the picker object (which is just a table with calendar elements) at the
*specified x and y coordinates, using the targetField object as the input tag
*that will ultimately be populated with a .
*
*This function will normally be called by the displayPicker function.
**/
function drawPicker(PickerDivID,targetField, x, y, PickerUrl) {

  // the picker table will be drawn inside of a <div> with an ID defined by the
  // global PickerDivID variable. If such a div doesn't yet exist on the HTML
  // document we're working with, add one.

  if (!document.getElementById(PickerDivID+ "_div")) {
    // don't use innerHTML to up the body, because it can cause global variables
    // that are currently pointing to objects on the page to have bad references
    //document.body.innerHTML += "<div id='" + PickerDivID + "' class='dpDiv'></div>";
    var newNode = document.createElement("div");
    newNode.setAttribute("id", PickerDivID+ "_div");
    newNode.setAttribute("class", "pDiv");
    newNode.setAttribute("style", "visibility: hidden;");
    document.body.appendChild(newNode);
    // move the picker div to the proper x,y coordinate and toggle the visiblity
    var pickerDiv = document.getElementById(PickerDivID+ "_div");
    pickerDiv.style.position = "absolute";
    pickerDiv.style.left = x + "px";
    pickerDiv.style.top = y + "px";
    if (pickerDiv.style.visibility == "visible") pickerDiv.style.visibility = "hidden";
       else pickerDiv.style.visibility = "visible";
    if (pickerDiv.style.display == "block") pickerDiv.style.display = "none";
       else pickerDiv.style.display = "block";
    pickerDiv.style.zIndex = 10000;
    // draw the picker table
    refreshPicker(PickerDivID, targetField.name, PickerUrl);
  } else {
    ClosePicker(PickerDivID);
  }
}
/**
*This is the function that actually draws the picker calendar.
**/
function refreshPicker(PickerDivID,FieldName, PickerUrl) {
  // the picker will be drawn as a table
  // you can customize the table elements with a global CSS style sheet,
  // or by hardcoding style and formatting elements below
  // Line end must be "\r\n";

  var html = "<table class=\"pTable\"><tr><td class=\"ptd\"><iframe id=\""+PickerDivID+"\" style=\"width:650px;height:300px\" frameborder=\"0\" scrolling=\"yes\" src=\"" + PickerUrl + "\">";
  html +="</iframe></td></tr><tr><td align=\"right\">\r\n";
  html += "<button class=\"pButton\" onClick=\"ClosePicker('" + PickerDivID + "');\">cerrar</button>\r\n</td></tr></table>";
  //html += "<button class=\"pButton\" onClick=\"upField('" + PickerDivID + "','" + FieldName + "','" + PickerField + "');\">cerrar</button>\r\n</td></tr></table>";
  document.getElementById(PickerDivID+ "_div").innerHTML = html;
}
/**
* Busca Objetos
**/
function getObj(name) {

  if (document.getElementById)
  {
        this.obj = document.getElementById(name);
        this.style = document.getElementById(name).style;
  }
  else if (document.all)
  {
        this.obj = document.all[name];
        this.style = document.all[name].style;
  }
  else if (document.layers)
  {
           this.obj = document.layers[name];
           this.style = document.layers[name];
  }
}
/**
*Cierra el picker
**/
function ClosePicker(PickerDivID) {
    document.body.removeChild(document.getElementById(PickerDivID+ "_div"));
}
/**
* Actualiza el campo seleccionado.
**/
function UpdateField(name, pkvalue, pktext) {
  var tmp= PickerObj[name];
  var targetField = document.getElementsByName (tmp.field).item(0);

    var theiframe = new getObj(name).obj;
    //targetField.value = theiframe.contentWindow.document.getElementsByName ("pkfield").item(0).value;
    targetField.value = pkvalue;
    //document.getElementsByName (FieldName+"_vl").item(0).value=theiframe.contentWindow.document.getElementsByName (SourceField+"_vl").item(0).value;
    replaceSpan(name,pktext);
  ClosePicker(name);
  //targetField.focus();
}
/**
* Cambia el valor de
**/
function replaceSpan(PickerDivID,newval){
    var newInput = document.createElement("input");
    newInput.setAttribute('type', 'hidden');
    newInput.setAttribute("name", FdName+ "_vl");
    newInput.setAttribute("id", PickerDivID+ "_vl");
    newInput.value=newval;
    var para = document.getElementById(PickerDivID+"_node");
    var spanElm = document.getElementById(PickerDivID+"_vl");
    var replaced = para.replaceChild(newInput,spanElm);
    
    var newSpan = document.createElement("span");
    newSpan.setAttribute("id", PickerDivID+ "_span");
    var newText = document.createTextNode(newval);
    newSpan.appendChild(newText); 
    spanElm = document.getElementById(PickerDivID+"_span");
    replaced = para.replaceChild(newSpan,spanElm);
}


