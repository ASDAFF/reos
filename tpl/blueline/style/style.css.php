<?php
header("Content-type: text/css");
$path=$_GET['path'];
?>

/*************************************** Parte general ******************************/

/* Casi negre #737373
   Gris mes fosc #A7A6A6
   Gris fosc #ADADAD
   Gris Clar #EBEDF2
   Gris mes clar #e5e5e5
   Gris clarisim #f8f8f8
   blau titols #0871A2  */
  

p {font: normal 14px arial;color:#737373}
ul {font: normal 14px arial;color:#737373}
li {font: normal 14px arial;color:#737373}
body {background-color:#E3E3E3;margin: 0cm 0cm 0cm 0cm} /*arriba derecha abajo izquierda*/
table{border-collapse: collapse;margin: 0cm 0cm 0cm 0cm;}
td {border-collapse: collapse;padding: 0cm 0cm 0cm 0cm;vertical-align:top;}

table.pagina {width:1003px;border-collapse: collapse;margin: 0cm 0cm 0cm 0cm;text-align:left}
td.ipgborder {width:18px;background-image: url(<?php echo $path;?>/tpl/blueline/images/iborder.jpg);background-repeat:repeat-y;}
td.dpgborder {width:18px;background-image: url(<?php echo $path;?>/tpl/blueline/images/dborder.jpg);background-repeat:repeat-y;}
table.tablas {width:100%; background-color:white;border-collapse: collapse;margin: 0cm 0cm 0cm 0cm}
td.bleft {padding: 0cm 0cm 0cm 0cm; width:135px;height: 430px ;vertical-align:top;}
td.bright {padding: 0cm 0cm 0cm 0cm; width:135px;vertical-align:top;}
td.pcentral {height:430px;padding: 0cm 0.2cm 0cm 0cm; vertical-align:top;}
hr {color:#e5e5e5;height:1px}
img {border:none}

a:link {color: #4677B9}
a:visited {color: #4677B9}
a:hover {color: #4677B9}
a:active {color: #4677B9}

span.tithome {margin: 0.2cm 0.2cm 0cm 0.2cm ;font: normal 20px arial;color:#4874BF;}
div.about {margin: 0.2cm 0.2cm 0.2cm 0.4cm ;font: normal 14px arial;color:black}

img.boton {vertical-align:middle}
a.boton:link {font: bold 12px arial;color: #4874BF;text-decoration: none}
a.boton:visited {font: bold 12px arial;color: #4874BF;text-decoration: none}
a.boton:hover {font: bold 12px arial;color: #4874BF;text-decoration: none}
a.boton:active {font: bold 12px arial;color: #4874BF;text-decoration: none}

div.gmap {margin: 0cm 0cm 0cm 0.2cm;width: 668px; height: 300px}

/*************************************************************************************/
/* caja Pie de pagina */

a.piecentral:link {font: bold 12px arial;color: #4874BF}
a.piecentral:visited {font: bold 12px arial;color: #4874BF}
a.piecentral:hover {font: bold 12px arial;color: #4874BF}
a.piecentral:active {font: bold 12px arial;color: #4874BF}
table.piecentral {height:57px;background-color:#FFFFFF;width:100% ; border:none;}
td.piecentral {font: normal 12px arial;text-align:left;
               background-image: url(<?php echo $path;?>/tpl/blueline/images/bgpie.jpg);background-repeat:repeat-x;
               padding: 0.4cm 0cm 0cm 0.2cm}

/*************************************************************************************/
/* Caja Menu pagina */

a.mnu:link {color:#4677B9;text-decoration: underline}
a.mnu:visited {color:#4677B9;text-decoration: underline}          /*arriba derecha abajo izquierda*/
a.mnu:hover {color:#4677B9;text-decoration: underline}
a.mnu:active {color:#4677B9;text-decoration: underline}

table.moptions {background-color:transparent;margin: 0px 0px 5px 0px}
td.mnu_item {background-color:transparent;padding: 0.1cm 0cm 0.1cm 0.1cm;text-align:left;font: normal 12px arial;color: black;}
tr.mnu_item {vertical-align:top}
td.mnu_itimg {background-color:transparent;padding: 0.15cm 0cm 0cm 0.2cm;text-align:right;font: normal 12px arial;color: black;}

img.mnu {border:none}

/*************************************************************************************/
/* Tabla barra */

table.barra {width:100%;height:30px;margin: 0cm 0cm 0cm 0cm ;border-collapse: collapse;border:none;
             background-color:white;
             background-image:url(<?php echo $path;?>/tpl/blueline/images/bgtopmnu.jpg);background-repeat: repeat-x ; }
td.barrad {font:bold 12px arial;color:#000000;text-align:right;padding: 5px 0.4cm 0cm 0cm;
          vertical-align:top}
td.barrai {font:bold 12px arial;color:#ffffff;text-align:left;padding: 5px 0.4cm 0cm 0.2cm;
          vertical-align:top}
          
a.barrai:link {font: bold 12px arial;color: #4B5464;text-decoration: none;}
a.barrai:visited {font: bold 12px arial;color: #4B5464;text-decoration: none}
a.barrai:hover {font: bold 12px arial;color: #4B5464;text-decoration: none}
a.barrai:active {font: bold 12px arial;color: #4B5464;text-decoration: none}
          
img.barra {border:none; vertical-align:text-top}
img.mnusep {border:none;margin:0px 5px 0px 5px;vertical-align:text-top}

/*************************************************************************************/
/* Box */

table.box {margin: 0.2cm 0.2cm 0.2cm 0.2cm;width:97%; border:1px dotted #4677B9; border-collapse: collapse}

tr.hbox {font: bold 14px arial;color:#4677B9;text-align:center;background-color:white;}
td.hbox {padding: 0.2cm 0cm 0cm 0.2cm; }
tr.box {font: bold 10px arial;color:black;text-align:left;background-color:white;}
td.box {text-align:center;padding: 0.2cm 0.2cm 0.2cm 0.2cm;vertical-align:top;width:15%}

a.box:link {font: bold 14px arial;color:#4677B9;text-decoration: underline}
a.box:visited {font: bold 14px arial;color:#4677B9;text-decoration: underline}          /*arriba derecha abajo izquierda*/
a.box:hover {font: bold 14px arial;color:#4677B9;text-decoration: underline}
a.box:active {font: bold 14px arial;color:#4677B9;text-decoration: underline}

/*************************************************************************************/
/* Tabla recomendados */

table.recom {width:100%;background-image: url(<?php echo $path;?>/tpl/blueline/images/bgnot.jpg);}
tr.recom { font:normal 12px arial;color:#000000;text-align:center; vertical-align:top;}
td.recom {border-bottom: 1px solid #6D7582;font:normal 12px arial;color:#000000; vertical-align:top;padding: 0.2cm 0cm 0cm 0cm}
img.recom {margin: 0cm 0cm 0cm 0cm ;float:top}

/*************************************************************************************/
/* Caja cabecera  */

table.head {width:100%; height:81px; background-color:white; border-collapse: collapse;
            background-image: url(<?php echo $path;?>/tpl/blueline/images/bghead.jpg);background-repeat: repeat-x ;}
tr.head {}
td.head {padding: 0cm 0cm 0cm 0cm;text-align:left}
img.head {border:none;vertical-align:top}

/*************************************************************************************/
/* Caja pgtitle  */

table.pgtitle {background-color:white;width:97%;border-collapse: collapse;border:none;
               margin: 0.2cm 0.2cm 0.2cm 0.2cm;}
td.pgtitle_label {padding: 0.1cm 0.2cm 0.1cm 0.2cm;font: bold 24px arial ;color:#4874BF;}
td.pgtitle_options {padding: 0.1cm 0.2cm 0.1cm 0.2cm;}
table.pgoptions {float:right;border-collapse: collapse;border:none; margin: 0cm 0cm 0cm 0cm;}
td.pgoptions {padding: 0cm 0cm 0cm 0cm;text-align:left}

/*************************************************************************************/
/* Noticias*/

td.noti_port {padding: 0.2cm 0.2cm 0.2cm 0.2cm;text-align:left;
              font: normal 11px arial;color: black;
             background-image: url(<?php echo $path;?>/tpl/blueline/images/bgnot.jpg);}
tr.noti_port {}
span.noti_fecha {font: bold 11px arial;color: black}

table.noticia {border-collapse: collapse;border: 1px solid #e5e5e5; margin: 0.2cm 0.2cm 0.2cm 0.2cm}
td.hnoticia {border-bottom: 1px solid #e5e5e5;padding: 0cm 0cm 0cm 0.2cm}
tr.hnoticia {text-align:center;font: bold 12px arial;color:#75864C;}
td.noticia {vertical-align:top;padding: 0.2cm 0.2cm 0.2cm 0.2cm}

table.noticia_img {border-collapse: collapse; margin: 0cm 0.2cm 0cm 0cm; float:left}
td.noticia_img {text-align:center;padding: 0cm 0.02cm 0cm 0cm}
img.noticia_img {margin: 0cm 0cm 0.2cm 0cm}

div.titnoti {margin: 0.2cm 0cm 0cm 0cm;font: bold 20px arial;color: #4874BF}
div.noti_fecha {font: bold 11px arial;color: #787878}
div.resum_noti {margin: 0.2cm 0cm 0cm 0cm;font: bold 14px arial;color: #787878}
div.txt_noti {margin: 0.2cm 0cm 0cm 0cm;font: normal 14px arial;color: black}

/*************************************************************************************/
/* Lista advert*/

table.lista_advert {width: 97%;border-collapse: collapse;margin: 0.2cm 0.2cm 0.2cm 0.2cm}
td.lista_advert{padding: 0.2cm 0.2cm 0.2cm 0.2cm;border-bottom: solid 5px #E7E9EB;border-top: solid 5px #E7E9EB;}
tr.linea_advert0 {font: normal 14px arial;color:#737373;}

td.list_advert_found {padding: 0cm 0cm 0cm 0cm;font: bold 12px arial;color:#737373}

td.list_advert_pages {text-align:left;padding: 0cm 0cm 0cm 0cm;font: normal 12px arial;color:#737373}
td.list_advert_links {text-align:right;padding: 0cm 0cm 0cm 0cm;font: normal 12px arial;color:#737373}

span.list_advert_page {margin: 0cm 0.1cm 0cm 0.1cm;font: bold 12px arial;color:#4874BF}

a.list_page:link {color: #4874BF;text-decoration: none;font: bold 14px arial;}
a.list_page:visited {color: #4874BF;text-decoration: none;font: bold 14px arial;}
a.list_page:hover {color: #4874BF;text-decoration: none;font: bold 14px arial;}
a.list_page:active {color: #4874BF;text-decoration: none;font: bold 14px arial;}

/************************************************************************************/
/* Advert */

table.advert{border-collapse: collapse;margin: 0cm 0cm 0cm 0cm;width:100%;}
td.advert_txt {border-collapse: collapse;padding: 0cm 0cm 0cm 0cm;vertical-align:top;width:100%;}
td.advert_img {border-collapse: collapse;padding: 0cm 0cm 0cm 0cm;vertical-align:top;}

img.img_advert_immo {float: left;margin: 0cm 0.2cm 0cm 0cm}
div.tit_adpob {font: bold 12px arial ;color:black;padding: 0cm 0cm 0.2cm 0cm;}
div.tit_adtp {font: bold 12px arial ;color:black;padding: 0cm 0cm 0.2cm 0cm;}
div.adtxt {font: normal 12px arial ;color:black;padding: 0cm 0cm 0.2cm 0cm;}
div.adprecio {text-align:right;font: bold 14px arial ;color:#FF2B01;padding: 0cm 1cm 0cm 0cm;}

td.linea{text-align:left;padding: 0.2cm 0.2cm 0.2cm 0.2cm;vertical-align:middle; border:dotted 1px #B2B1B1;}

a.linea:link {color: #4874BF;text-decoration: none;font: bold 14px arial;}
a.linea:visited {color: #4874BF;text-decoration: none;font: bold 14px arial;}
a.linea:hover {color: #4874BF;text-decoration: underline;font: bold 14px arial;}
a.linea:active {color: #4874BF;text-decoration: none;font: bold 14px arial;}

td.col {font:bold 12px arial;color:#4874BF;text-align:center}



/************************************************************************************/
/* caja clase forms*/

table.form_title {border-collapse: collapse;margin: 0.2cm 0.2cm 0cm 0.2cm}
table.forms_border {text-align:center;width:97%;background-color:#E4E7ED;border-collapse: collapse;border: 1px solid #C8CBD1; margin: 0cm 0.2cm 0.2cm 0.2cm}
table.forms {border-collapse: collapse; margin: 0cm 0cm 0cm 0cm}
input.field_textbox{font: normal 12px arial;color:#737373;border: solid 1px #e5e5e5;padding: 0cm 0cm 0cm 0.1cm;}
input.field_checkbox{font: normal 12px arial;color:#737373;border: solid 1px #e5e5e5;padding: 0cm 0cm 0cm 0.1cm;}
fieldset.field_checkbox{color:#737373;}
select.field_listbox{font: normal 12px arial;color:#737373;border: solid 1px #e5e5e5;padding: 0cm 0cm 0cm 0.1cm;}
td.field_title{text-align:right;font: normal 12px arial;color:#737373;padding: 0.1cm 0.1cm 0cm 0cm}
td.field_value{font: bold 12px arial;color:black;padding: 0.1cm 0cm 0cm 0cm;text-align:left}
td.form_button {text-align:right;padding: 0cm 0.2cm 0.2cm 0cm}   /*arriba derecha abajo izquierda*/
td.forms_border{padding: 0.2cm 0.2cm 0cm 0.2cm}
form.forms {border-collapse: collapse;margin: 0cm 0cm 0cm 0cm}
div.field_text {padding: 0cm 0cm 0cm 0.1cm;border: solid 1px #e5e5e5;background-color:white}

td.form_link {text-align:center;background-color:#f8f8f8;padding: 0.1cm 0.2cm 0cm 0.2cm;text-align:center;border: 1px solid #e5e5e5;border-bottom:none}
td.form_title {font: bold 11px arial ;color:#787878;padding: 0cm 0.2cm 0cm 0cm;text-align:left;vertical-align:bottom}
a.form_link:link {color: #75864C;text-decoration: none}
a.form_link:visited {color: #75864C;text-decoration: none}
a.form_link:hover {color: #0871A2;text-decoration: none}
a.form_link:active {color: #737373;text-decoration: none}

/************************************************************************************/
/* caja clase gallery*/

table.thumb_gallery {width:98%;border-collapse: collapse;text-align:center; margin: 0cm 0cm 0.2cm 0.2cm}
td.thumb_gallery {vertical-align:Bottom;padding: 0.2cm 0cm 0.2cm 0cm;text-align:center}
td.title_gallery {border: 1px solid #e5e5e5;border-bottom:none;background-color:#f8f8f8;font: bold 14px arial ;color:#787878;padding: 0cm 0cm 0cm 0.2cm;}
img.thumb_gallery {border:solid 6px #e5e5e5;}

table.img_gallery {width:97%;border-collapse: collapse;text-align:center; margin: 0cm 0.2cm 0.2cm 0.2cm}
td.img_gallery {text-align:center;padding: 0.2cm 0.2cm 0.2cm 0.2cm}
td.img_title {font: bold 14px arial ;color:#787878;padding: 0cm 0cm 0cm 0.2cm;}
img.imggal {border:solid 6px #e5e5e5;}

                                                                    /*arriba derecha abajo izquierda*/
/************************************************************************************/
/* caja messages*/

table.msg_alerts {width:100%;border-collapse: collapse;border-bottom: 1px solid #e5e5e5; margin: 0cm 0cm 0.2cm 0cm}
td.msg_alerts {color: red; font: bold 12px arial;vertical-align:top;padding: 0cm 0cm 0cm 0.2cm}  /*arriba derecha abajo izquierda*/

table.msg_position {width:100%;border-collapse: collapse;border-bottom: 1px dotted #e5e5e5; margin: 0cm 0cm 0.2cm 0cm}
td.msg_position {color: black; font: bold 12px arial;vertical-align:top;padding: 0.2cm 0cm 0.2cm 0.2cm}  /*arriba derecha abajo izquierda*/


/*************************************************************************************/
/* Tabla oficinas */

table.oficinas {width:97%;margin: 0.2cm 0.2cm 0cm 0.2cm ;border-collapse: collapse;border:none; background-color:white;}
tr.oficinas {font: normal 12px arial;color:black;text-align:left; padding: 0cm 0cm 0cm 0cm}
td.oficinas {border-bottom: 1px dotted #e5e5e5;padding: 0.2cm 0.2cm 0.2cm 0.2cm}
img.oficinas {margin: 0cm 0cm 0cm 0cm ;float:top}
span.tit_oficina {font: bold 14px arial;color:black;}

/*************************************************************************************/
/* Tabla comment */

table.comment {width:97%;margin: 0cm 0.2cm 0.2cm 0.2cm ;border-collapse: collapse;border:none; background-color:white;}
td.comment {font: normal 12px arial;color:#737373;padding: 0cm 0.2cm 0cm 0.2cm;vertical-align:top}

/*************************************************************************************/
/* Tabla extras */

td.extras_title {width:100px;font: bold 12px arial;color:#737373;padding: 0.2cm 0.2cm 0.2cm 0.2cm;vertical-align:top}
td.extras_txt {font: normal 12px arial;color:#737373;padding: 0.2cm 0.2cm 0.2cm 0.2cm;vertical-align:top}


/*************************************************************************************/
/* Tabla calculos hipoteca */                                   /*arriba derecha abajo izquierda*/

table.calc_div {width:97%;border-collapse: collapse;margin: 0cm 0cm 0cm 0.2cm;border: solid 1px #C6E091}
td.calc_div {border-collapse: collapse;padding: 0cm 0cm 0cm 0.2cm;vertical-align:top;}
td.total_div {text-align:right;font: bold 12px arial;border-collapse: collapse;padding: 0cm 0.2cm 0cm 0cm;vertical-align:top;}

table.calc {width:97%;margin: 0cm 0cm 0cm 0cm ;border-collapse: collapse;border:none; background-color:white;}
td.hcalc {width:100%;background-color:#E4F0CA; text-align:center; color: #787878; font: bold 12px arial}
td.label  {border-bottom: solid 1px #C6E091;font: normal 12px arial;color:#737373;padding: 0cm 0.2cm 0cm 0.2cm;vertical-align:top}
td.tlabel  {border-bottom: solid 1px #C6E091;font: bold 12px arial;color:#737373;padding: 0cm 0.2cm 0cm 0.2cm;vertical-align:top}
td.values {width:30%;border-bottom: solid 1px #C6E091;text-align:right; font: normal 12px arial;color:#737373;padding: 0cm 0.2cm 0cm 0.2cm;vertical-align:bottom}
td.tvalues {width:30%;border-bottom: solid 1px #C6E091;text-align:right; font: bold 12px arial;color:#737373;padding: 0cm 0.2cm 0cm 0.2cm;vertical-align:bottom}

/******************************************************************************/
/* Tabla blocks left right */  

table.bloci {width:93%;border-collapse: collapse; margin: 0.2cm 0cm 0.5cm 0.1cm;}
td.hbloci {border: solid 1px #6D7582;padding: 0cm 0cm 0cm 0.2cm;
           vertical-align:middle;height:21px;background-image: url(<?php echo $path;?>/tpl/blueline/images/bghbox.jpg);background-repeat: repeat-x ;}
tr.hbloci {text-align:center;font: bold 12px arial;color:white;}
tr.bloci {}
td.bloci {background-color:#D3E4F5;border: solid 1px #6D7582;}

table.blocd {width:97%;margin: 0.2cm 0.1cm 0cm 0cm ;border-collapse: collapse;}
tr.hblocd {font: bold 12px arial;color:white;text-align:center; }
td.hblocd {border: solid 1px #6D7582;padding: 0cm 0.2cm 0cm 0cm;
           vertical-align:middle;height:21px;background-image: url(<?php echo $path;?>/tpl/blueline/images/bghbox.jpg);background-repeat: repeat-x ;}
tr.blocd {}
td.blocd {background-color:#D3E4F5;border: solid 1px #6D7582;}

/******************************************************************************/
/* Tabla Calendar */
table.calendar {width:93%;border-collapse: collapse; margin: 0.2cm 0cm 0.2cm 1.2cm;}
td.calendar {padding: 0cm 0cm 0cm 0cm;text-align:right}
td.calendarHeader{text-align:center;font:bold 16px arial;color:#787878}

table.month {width:150px;margin: 0.2cm 0.1cm 0.2cm 0.1cm;}
td.monthhd {text-align:center;font:bold 12px arial;color:#787878}
td.dayname {width:21px;font:bold 11px arial;text-align:center;background-color:#4874BF;color:white}

td.today {text-align:center;font:bold 11px arial;}
td.day {text-align:center;font:bold 11px arial;}
td.day1{text-align:center;background-color:#F4F3C8;font:bold 11px arial;color:#4874BF;}
td.day2{text-align:center;background-color:#D9E7F5;font:bold 11px arial;color:#4874BF;}
td.day3{text-align:center;background-color:#B4F3AD;font:bold 11px arial;color:#4874BF;}
td.day4{text-align:center;background-color:#F5EF97;font:bold 11px arial;color:#4874BF;}

td.bday  {text-align:center;color:#FD0101;font:bold 11px arial;}
td.bday1 {text-align:center;background-color:#F4F3C8;color:#FD0101;font:bold 11px arial;}
td.bday2 {text-align:center;background-color:#D9E7F5;color:#FD0101;font:bold 11px arial;}
td.bday3 {text-align:center;background-color:#B4F3AD;color:#FD0101;font:bold 11px arial;}
td.bday4 {text-align:center;background-color:#F5EF97;color:#FD0101;font:bold 11px arial;}

td.legend {text-align:right;}

table.legend{margin: 0.2cm 0cm 0.2cm 0.2cm;}
td.legend_color{width:120px}
td.callabel {font:bold 10px arial;text-align:left;padding: 0cm 0.2cm 0cm 0.2cm}

/**** LOGOUT ***/

td.logout {text-align:right;color: black; font: bold 12px arial;vertical-align:top;padding: 0.2cm 0.2cm 0.2cm 0.2cm}  /*arriba derecha abajo izquierda*/
div.logout {}

/******************************************/
/* LOGINFORM */
div.loginform {margin: 0.2cm 0.2cm 0.2cm 0.2cm ;width:100%;}
table.login {margin: 0cm 0.2cm 0cm 0.2cm}
td.login {padding: 0.2cm 0.2cm 0.2cm 0.2cm;font: normal 14px arial;color:#737373}
td.login_button {text-align:right;padding: 0.2cm 0.2cm 0.2cm 0.2cm}
input.logininput {border: 1px solid #4874BF}
input.button {border: 1px solid #4874BF;background-color:#4874BF;color:white}
p.titlogin {text-align:left; font: bold 18px arial ;color:#4874BF; margin: 0.2cm 0cm 0cm 0.2cm}
p.txtlogin {text-align:left;margin: 0.2cm 0cm 0cm 0.2cm}
div.lostpass {padding: 0.2cm 0.2cm 0.2cm 2cm;color:#4874BF;}










