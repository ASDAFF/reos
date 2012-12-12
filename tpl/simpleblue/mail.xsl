<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xhtml"
version="1.0"  
doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" 
doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" 
/>

<!-- Aqui ponemos el contenido del template -->
<!-- TEMPLATE COPY ALL ELEMENTS WITHOUT TEMPLATE -->
<xsl:template match="*|@*|comment()|processing-instruction()|text()">
    <xsl:copy>
      <xsl:apply-templates select="*|@*|comment()|processing-instruction()|text()"/>
    </xsl:copy>
</xsl:template>


<!-- TEMPLATE BODY -->
<xsl:template match="pagina">
<link rel="StyleSheet" href="../tpl/simpleblue/style/pstyle.css" type="text/css"/>
<body>
      <xsl:if test="boolean(@onload)">
      <xsl:attribute name="onload">
      <xsl:value-of select="@onload"/>
      </xsl:attribute>
      </xsl:if>
      <table width="100%"><tr><td align="center">
      <table class="pagina" >
      <tr>
      <td class="ipgborder"></td>
      <td>
            <xsl:apply-templates select="headers"/>
            <table class="tablas">
                <tr>
                <xsl:apply-templates select="bleft"/>
                <xsl:apply-templates select="central"/>
                <xsl:apply-templates select="bright"/>
                </tr>
            </table>
            <xsl:apply-templates select="footers"/>
      </td>
      <td class="dpgborder"></td>
      </tr>
      </table>
      </td>
      </tr>
      </table>
</body>
</xsl:template>

<!-- TEMPLATE BLEFT -->
<xsl:template match="bleft">
<td class="bleft">
<xsl:for-each select="*">
<table class="bloci">
<tr class="hbloci"><td class="hbloci">
<xsl:value-of select="@title"/>
</td></tr>
<tr class="bloci"><td class="bloci">
<xsl:apply-templates select="current()"/>
</td></tr></table>
</xsl:for-each>
</td>
</xsl:template>

<!-- TEMPLATE CENTRAL -->
<xsl:template match="central">
<td class="pcentral">

<xsl:choose>
<xsl:when test="boolean(immo)">
<xsl:apply-templates select="position"/>
<xsl:apply-templates select="pgtitle"/>
<table><tr>
<td width="30%">
<xsl:apply-templates select="thumb" mode="a"/>
<xsl:apply-templates select="imggal"/>
</td></tr><tr>
<td>
<xsl:apply-templates select="immo"/>
<xsl:if test="boolean(activities|equip|services|observ|capacity)">
<table>
<xsl:apply-templates select="activities|equip|services|observ|capacity"/>
</table>
</xsl:if>
<xsl:apply-templates select="pgtitle/options"/>
<xsl:apply-templates select="immocal"/>
</td>
</tr>
</table>

</xsl:when>
<xsl:when test="boolean(contact)">
<xsl:apply-templates select="position"/>
<xsl:apply-templates select="pgtitle[position()=1]"/>
<table><tr>
<td width="50%">
<xsl:apply-templates select="contact"/>
</td>
<td>
<xsl:apply-templates select="offices"/>
</td>
</tr>
</table>
</xsl:when>

<xsl:otherwise>
<xsl:apply-templates/>
</xsl:otherwise>
</xsl:choose>

</td>
</xsl:template>

<!-- TEMPLATE HOME -->
<xsl:template match="home">
<span class="tithome"><xsl:value-of select="title"/></span>
<xsl:apply-templates/>
</xsl:template>

<!-- TEMPLATE BRIGHT -->
<xsl:template match="bright">
<td class="bright">
<xsl:for-each select="*">
<table class="blocd">
<tr class="hblocd"><td class="hblocd">
<xsl:value-of select="@title"/>
</td></tr>
<tr class="blocd"><td class="blocd">
<xsl:apply-templates select="current()"/>
</td></tr></table>
</xsl:for-each>

</td>
</xsl:template>

<!-- TEMPLATE MENU -->

<xsl:template match="moptions">
<table class="moptions">
<xsl:for-each select="*"> <!-- *[not(name()='ofis')][not(name()='bguide') ] -->
    <tr class="mnu_item">
    <td class="mnu_itimg"><img class="mnu" src="tpl/simpleblue/images/bullet.gif"  alt=""/></td>
    <td class="mnu_item">
    <a class="mnu" href="{@href}"><xsl:value-of select="."/></a>
    </td></tr>
</xsl:for-each>
</table>
</xsl:template>

<!-- TEMPLATE HEADERS -->

<xsl:template match="headers">
<table style="background-color:white;width:100%">
<tr >
<td>
FINQUES GIMENO
</td>
<td style="text-align:right;padding:5px 10px 0px 0px;font:bold 14px verdana;color:#8FA4D3 " >
(34) 972 36 82 10
</td>
</tr>
</table>
<xsl:if test="boolean(mnu_about/moptions)">
<table class="barra">
<tr>
<td class="barrai">
<xsl:apply-templates select="mnu_about/moptions"/>
</td>
<td class="barrad">
<xsl:apply-templates select="langs"/>
</td>
</tr>
</table>
</xsl:if>
</xsl:template>

<!-- TEMPLATE LANGS -->

<xsl:template match="langs">
<xsl:for-each select="item">
    <a href="{@href}"><xsl:value-of select="."/></a> 
    <xsl:if test="(position()&#60;last())"> | </xsl:if>
</xsl:for-each>
</xsl:template>

<!-- TEMPLATE MENU HEADER -->

<xsl:template match="headers/*/moptions">

<xsl:for-each select="*[not(name()='ofis')]"> <!-- *[not(name()='ofis')][not(name()='bguide') ] -->
    <a class="barrai" href="{@href}"><xsl:value-of select="."/></a>
    <xsl:if test="(position()&#60;last())"><img class="mnusep" src="/tpl/simpleblue/images/mnusep.jpg" alt=""/></xsl:if>
</xsl:for-each>

</xsl:template>


<!-- TEMPLATE POSITION -->

<xsl:template match="position">
<table class="msg_position"><tr><td class="msg_position">

<xsl:for-each select="pos">
    <xsl:text> >> </xsl:text>
    <xsl:choose>
    <xsl:when test="boolean(@href)">
    <a class="msg_position" href="{@href}"><xsl:value-of select="."/></a>
    </xsl:when>
    <xsl:otherwise>
    <xsl:value-of select="."/>
   </xsl:otherwise>
   </xsl:choose>
</xsl:for-each>
</td></tr>
</table>
</xsl:template>

<!-- TEMPLATE RESULTS BY POB -->

<xsl:template match="pobs">
<table class="box">
<tr class="hbox"><td class="hbox"><xsl:value-of select="title"/></td></tr>
<tr class="box"><td class="box">
<xsl:for-each select="item">
    <a class="box" href="{@href}"><xsl:value-of select="."/></a><br />
</xsl:for-each>
</td></tr></table>
</xsl:template>

<!-- TEMPLATE RECOMENDAMOS -->

<xsl:template match="recom">
<table class="recom">
<xsl:for-each select="item">
  <tr class="recom"><td class="recom">
  <xsl:for-each select="img">
      <a href="{../@href}">
      <img class="recom" src="{@src}" alt=""/>
      </a>
  </xsl:for-each>
  <a href="{@href}"><xsl:value-of select="refer"/></a>
  <br />
  <b><xsl:value-of select="tpprop"/></b>
  <br/>
  <xsl:value-of select="pob"/>
  <xsl:if test="boolean(zone)">
  <br/>
  <xsl:value-of select="zone"/>
  </xsl:if>
  <br/>
  <b>
  <xsl:value-of select="precio/label"/>
  <xsl:value-of select="precio/valprc"/><br/>
  <xsl:value-of select="precio/type"/>
  </b>
  </td></tr>
  </xsl:for-each>
</table>
</xsl:template>

<!-- TEMPLATE NOTICIAS -->

<xsl:template match="noticias">
<xsl:apply-templates/>
</xsl:template>

<!-- TEMPLATE NOTICIA PORTADA -->

<xsl:template match="notiport">
<table>
<tr class="noti_port"><td class="noti_port">
<span class="noti_fecha">
<xsl:value-of select="date"/>
</span><br />
<xsl:value-of select="titnoti"/><br />
<a href="{more/@href}"><xsl:value-of select="more/."/></a><br />
</td></tr></table>
</xsl:template>

<!-- TEMPLATE NOTICIA -->
<xsl:template match="noticia">
<table class="noticia">
<tr class="hnoticia"><td class="hnoticia"><xsl:value-of select="hnoti"/></td></tr>
<tr><td class="noticia">

<xsl:if test="boolean(imgnot)">
<table class="noticia_img"><tr><td class="noticia_img">
<xsl:for-each select="imgnot">
<img class="noticia_img" src="{@src}" alt=""/><br/>
</xsl:for-each>
</td></tr></table>
</xsl:if>
<div class="noti_fecha"><xsl:value-of select="date"/></div>
<div class="titnoti"><xsl:value-of select="title"/></div>
<div class="resum_noti"><xsl:apply-templates select="resum"/></div>
<div class="txt_noti"><xsl:apply-templates select="texto"/></div>
</td></tr></table>
</xsl:template>

<!-- TEMPLATE ADVERTS -->

<xsl:template match="adverts">
<table class="lista_advert">
<tr>
<xsl:if test="boolean(found)"> <td class="list_advert_found"><xsl:value-of select="found"/></td></xsl:if>
<xsl:apply-templates select="lst_info"/>  
</tr>
<xsl:for-each select="advert">
  <tr class="linea_advert0"><td class="lista_advert" colspan="3">
  <xsl:apply-templates select="current()"/>
  </td></tr>
</xsl:for-each>
<tr>
<td></td>
<xsl:apply-templates select="lst_info"/>  
</tr>
</table>
</xsl:template>

<!-- TEMPLATE LST_INFO -->
<xsl:template match="lst_info">

  <td class="list_advert_pages">
  <xsl:value-of select="label"/>&#160;<xsl:value-of select="current"/>&#160;<xsl:value-of select="total"/>
  </td>
  <td class="list_advert_links">
  <xsl:if test="boolean(pags/prev)"><a class="list_page" href="{pags/prev/@href}">&#160;&lt;&lt;&#160;</a></xsl:if>
    <xsl:for-each select="pags/pag">
    <xsl:choose>
    <xsl:when test="boolean(@href)">
    <a href="{@href}"><xsl:value-of select="."/></a>
    </xsl:when>
    <xsl:otherwise>
    <span class="list_advert_page"><xsl:value-of select="."/></span>
    </xsl:otherwise>
    </xsl:choose>
    <xsl:if test = "not(position()=last())" > 
       <xsl:text > - </xsl:text> 
    </xsl:if> 
    </xsl:for-each>
  <xsl:if test="boolean(pags/next)"><a class="list_page"  href="{pags/next/@href}"><xsl:text >&#160;>>&#160;</xsl:text></a></xsl:if>  
  </td>
  
</xsl:template>


<xsl:template match="equip|services|activities|observ|capacity">
<tr>
<td class="extras_title"><xsl:value-of select="@title"/> :</td><td class="extras_txt"><xsl:value-of select="."/>.</td>
</tr>
</xsl:template>


<!-- TEMPLATE ADVERT -->

<xsl:template match="advert">
<table class="advert"><tr>
<xsl:if test="boolean(img)">
<td class="advert_img">
<a href="{@href}"><img class="img_advert_immo" src="{img/@src}" alt="" /></a>
</td>
</xsl:if>
<td class="advert_txt">
<div class="tit_adtp"><xsl:value-of select="transaction"/></div>
<div class="tit_adpob">
<xsl:choose>
 <xsl:when test="boolean(@href)">
  <a href="{@href}"><xsl:value-of select="refe"/></a>
  </xsl:when>
  <xsl:otherwise>
<xsl:value-of select="refe"/>
</xsl:otherwise>
</xsl:choose>
, <xsl:value-of select="pob"/> -
<xsl:value-of select="zone"/>
</div>
<div class="adtxt"><xsl:value-of select="text"/></div>
<div class="adprecio">
<xsl:value-of select="price/label"/> 
<xsl:value-of select="price/valprc"/><br/>
<xsl:value-of select="price/type"/>
</div>
</td></tr></table>
</xsl:template>

<!-- TEMPLATE FOOTERS -->

<xsl:template match="footers">
<table class="piecentral">
        <tr><td class="piecentral">
        <a class="piecentral" href="http://validator.w3.org/check/referer">XHTML</a>
        | <xsl:value-of select="label"/>
        </td></tr>
</table>
</xsl:template>

<!-- TEMPLATE PGTITLE -->

<xsl:template match="pgtitle">
<table class="pgtitle">
<tr><td class="pgtitle_label">
<xsl:value-of select="title"/>
</td>
<td class="pgtitle_options">
    <table class="pgoptions">
        <xsl:if test="boolean(options/back)">
        <tr><td class="pgoptions">
        <a class="boton" href="{options/back/@href}"><img class="boton" src="tpl/simpleblue/images/butt_left.jpg" alt="" />
        <xsl:text> </xsl:text><xsl:value-of select="options/back"/>
        </a>
        </td></tr>
        </xsl:if>
    
        
    </table>
</td></tr></table>
</xsl:template>

<!-- TEMPLATE PGTITLE/OPTIONS -->
<xsl:template match="pgtitle/options">
<table>
<xsl:for-each select="item">
<tr>
        <td class="pgoptions">
        <a class="boton" href="{@href}" target="{@target}" onclick="{@onclick}"><img class="boton" src="tpl/simpleblue/images/butt_right.jpg" alt="" />
        <xsl:text> </xsl:text><xsl:value-of select="."/>
        </a>
        </td>
</tr>        
</xsl:for-each>
</table>
</xsl:template>

<!-- TEMPLATE SERVICIO -->

<xsl:template match="servicio">
<table class="box">
<tr class="hbox">
<td class="hbox">
<xsl:attribute name="colspan">
<xsl:value-of select="count(item)"/>
</xsl:attribute>
<xsl:value-of select="title"/>
</td></tr>
<tr class="box">
<xsl:for-each select="item">
<td class="box">
<a class="box" href="{@href}"><xsl:value-of select="."/></a>
<!-- <img src="tpl/simpleblue/images/tp{@id}.gif" alt="" /> -->
</td>
</xsl:for-each>
</tr></table>
</xsl:template>

<!-- TEMPLATE SPECIAL -->

<xsl:template match="special">
<table class="box">
<tr class="hbox">
<td class="hbox">
<xsl:value-of select="title"/>
</td></tr>
<tr class="box">
<td class="box">
<xsl:apply-templates/>
</td>
</tr></table>
</xsl:template>


<!-- TEMPLATE THUMB -->

<xsl:template match="thumb" mode="a">
<script language="javascript" type="text/javascript" src="padmin/jscripts/img.js"/>
<table class="thumb_gallery">
<xsl:for-each select="item">
<tr>
<xsl:apply-templates select="current()"/>
</tr>
</xsl:for-each>
</table>
</xsl:template>



<!-- TEMPLATE THUMB -->

<xsl:template match="thumb">
<script language="javascript" type="text/javascript" src="padmin/jscripts/img.js"/>
<table class="thumb_gallery">
<xsl:apply-templates select="item[position() mod 4 = 1]" mode="row"/>
</table>
</xsl:template>

<xsl:template match="item" mode="row">
<tr>
 <xsl:apply-templates select=". | following-sibling::item[position() &lt; 4]"/>    
</tr>
</xsl:template>

<!-- TEMPLATE THUMB ITEM -->
<xsl:template match="thumb/item">
<td class="thumb_gallery">
<xsl:choose>
    <xsl:when test="boolean(@href)">
    <a href="{@href}"><img class="thumb_gallery" src="{@dir}/thumbnails/{@src}" alt="" /></a>
    </xsl:when>
    <xsl:when test="boolean(@pop)">
    <a href="javascript:CargarFoto('{@dir}/images/{@src}','550','400')"><img class="thumb_gallery" src="{@dir}/thumbnails/{@src}" alt="" /></a>
    </xsl:when>
    <xsl:otherwise>
    <img class="thumb_gallery" src="{@dir}/thumbnails/{@src}" alt="" />
   </xsl:otherwise>
   </xsl:choose>
</td>
</xsl:template>



<!-- TEMPLATE IMAGE GALLERY -->

<xsl:template match="imggal">
<table class="img_gallery">
<xsl:for-each select="item">
<tr><td class="img_gallery">
<img class="imggal" src="{@src}" alt=""/>
</td></tr>
</xsl:for-each>
</table>
</xsl:template>

<!-- TEMPLATE IMMOSEARCH -->

<xsl:template match="immosearch">
<table class="box">
<tr class="hbox">
<td class="hbox">
<xsl:value-of select="title"/>
</td></tr>
<tr class="box">
<td class="box">
<xsl:apply-templates/>
</td></tr>
</table>
</xsl:template>

<!-- TEMPLATE CONTENT GENERAL -->
<xsl:template match="content">
<div class="about">
<xsl:apply-templates/>
</div>
</xsl:template>

<!-- TEMPLATE COMMENT -->
<xsl:template match="comment">
<table class="comment"><tr><td class="comment">
<xsl:apply-templates/>
</td></tr>
</table>
</xsl:template>

<!-- TEMPLATE ALERTS-->
<xsl:template match="alerts">
<table class="msg_alerts">
<xsl:for-each select="msg">
<tr><td class="msg_alerts">
<xsl:value-of select="."/>
</td></tr>
</xsl:for-each>
</table>
</xsl:template>

<!-- TEMPLATE HIPOTECA -->
<xsl:template match="hipotec">
<table class="calc_div"><tr>
<xsl:for-each select="gst">
<td class="calc_div"><xsl:apply-templates select="current()"/></td>
</xsl:for-each>
</tr><tr>
<td colspan="2" class="total_div"><xsl:value-of select="total/label"/><xsl:value-of select="total/value"/></td>
</tr></table>
</xsl:template>

<!-- TEMPLATE GST HIPOTECA-->
<xsl:template match="gst">
<table class="calc">
<tr><td colspan="2" class="hcalc"><xsl:value-of select="title"/></td></tr>
<xsl:for-each select="item">
<tr><td class="label"><xsl:value-of select="label"/></td>
<td class="values"><xsl:value-of select="value"/></td>
</tr>
</xsl:for-each>
<tr><td class="tlabel"><xsl:value-of select="total/label"/></td>
<td class="tvalues"><xsl:value-of select="total/value"/></td>
</tr>
</table>
</xsl:template>

<!-- TEMPLATE OFFICES -->
<xsl:template match="offices">
<script language="javascript" type="text/javascript" src="padmin/jscripts/img.js"/>
<table class="oficinas">
<xsl:for-each select="row[position()=1]">
<tr class="oficinas"><td class="oficinas">
<xsl:if test="boolean(name_org)">
<span class="tit_oficina"><xsl:value-of select="name_org"/></span><br/>
</xsl:if>
<xsl:if test="boolean(txt_address1)">
<xsl:value-of select="txt_address1"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_zone)">
<xsl:value-of select="txt_zone"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_cp)">
<xsl:value-of select="txt_cp"/><xsl:text> </xsl:text>
</xsl:if>
<xsl:if test="boolean(txt_poblacion)">
<xsl:value-of select="txt_poblacion"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_provincia)">
<xsl:value-of select="txt_provincia"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_telf1)">
<xsl:value-of select="txt_telf1/@label"/> : <xsl:value-of select="txt_telf1"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_fax)">
<xsl:value-of select="txt_fax/@label"/> : <xsl:value-of select="txt_fax"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_email1)">
<xsl:value-of select="txt_email1/@label"/> : <xsl:value-of select="txt_email1"/><br/>
</xsl:if>
<xsl:if test="boolean(txt_web)">
<xsl:value-of select="txt_web"/><br/>
</xsl:if>
</td></tr>
</xsl:for-each>
<tr>
<td style="text-align:center;padding: 10px 0px 0px 0px">
<img src="tpl/simpleblue/images/situa.jpg" alt=""/>
</td>
</tr>
<tr>
<td style="text-align:center;padding: 10px 0px 0px 0px">
<a href="javascript:CargarFoto('tpl/simpleblue/images/mlloret_big.jpg','650','400')">
<img src="tpl/simpleblue/images/mlloret_mini.jpg" alt=""/>
</a>
</td>
</tr>

</table>

</xsl:template>

<!-- TEMPLATE GRID -->
<xsl:template match="grid">
<table><tr>
<xsl:for-each select="hrow/*">
<td><xsl:value-of select="."/></td>
</xsl:for-each>
</tr>
<xsl:for-each select="row">
<tr>
<xsl:for-each select="*">
<td><xsl:value-of select="."/></td>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>
</xsl:template>

<!-- TEMPLATE FORMDEF -->
<xsl:template match="formdef">
<table class="forms_border"><tr><td class="forms_border">
<xsl:apply-templates  select="htmlform"/>
</td></tr>
<tr><td class="form_button">
<a class="boton" href="javascript:document.{@name}.submit();"><img class="boton" src="tpl/simpleblue/images/form_butt.gif" alt=""/>
<xsl:apply-templates select="fbutton"/>
</a>
</td></tr>
</table>
</xsl:template>

<!-- TEMPLATE FORMDEF -->
<xsl:template match="content/templates">
<!-- MyTemplateStorage.com Affiliate Interface. -->
<script TYPE="text/javascript" LANGUAGE="JavaScript"
SRC="http://www.mytemplatestorage.com/codes/interface.php?aff=jmarxuach&#38;cols=2&#38;rows=4&#38;skin=999&#38;sadult=off&#38;sp=0&#38;bgcolor=%23FFFFFF&#38;type=wt&#38;iw=500&#38;ih=1025&#38;category=4&#38;pr=no&#38;locale=es&#38;currency=1&#38;src=no&#38;nt=no"></script>
<!-- End Of MyTemplateStorage.com Affiliate Interface. -->
					
</xsl:template>


<!-- TEMPLATE IMMO -->
<xsl:template match="immo">
<xsl:apply-templates/>		
</xsl:template>


</xsl:stylesheet>
