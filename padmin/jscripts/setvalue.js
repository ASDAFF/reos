/**
*Inicializa un valor de un campo respecto de otro campo.
*por ejemplo : SetValue(this.form.listbox,this.form.textbox,"1;8,30#2;4,3#3;4,5")
*@param FormObject Campo origen
*@param FormObject Campo destino
*@param String Relación entre los valores de origen i los valores que quieres que salgan en destino
*@param Char Carater separador de valores
*vls del ejemplo tiene codigo;valor#código;valor...
*El Caracter # es el separador.
**/
function SetValue(fieldfrom,fieldto,vls,chr) {
var cds=vls.split(chr);
var result=new Array(cds.length);
var tmp;
for (var i=0;i<cds.length;i++)
    {
    tmp=cds[i].split(';');
    result[tmp[0]]=tmp[1];
    }
fieldto.value=result[fieldfrom.value];
}
