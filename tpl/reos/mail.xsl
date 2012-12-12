<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html"
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

<BODY style="margin: 0cm 0cm 0cm 0.2cm">
<style>table {
      border-collapse: collapse;
      margin: 0cm 0cm 0cm 0cm;
      background-color:transparent;
      
      }
td    {
       vertical-align: top;
       padding:0px 0px 0px 0px;
      }
tr    {
       padding:0px 0px 0px 0px;
      }
a:link {color: #0871A2}
a:visited {color: #0871A2}
a:hover {color: #0871A2}
a:active {color: #0871A2}
</style>
<TABLE align="center" style="background-color:white">
<TR>
<TD >
   <TABLE WIDTH="776">
   <xsl:apply-templates select="headers"/>
   <TR>
   <TD>
     <TABLE background="white">
     <TR><TD>
     <xsl:apply-templates select="bleft"/>
     </TD><TD>
     <xsl:apply-templates select="central"/>
     </TD></TR>
     </TABLE>
   </TD>
  </TR>
  <TR>
  <TD>
   <xsl:apply-templates select="footers"/>   
  </TD>
  </TR>
  </TABLE>
</TD>
</TR>
</TABLE>
</BODY>
</xsl:template>

<!-- TEMPLATE HEADERS-->
<xsl:template match="headers">
<TR><TD style="text-align:center;font: bold 34px arial;color:white;background-color:#174D7B">            
<xsl:value-of select="//html/head/title"/>
<xsl:value-of select="//html/pagina/central/noticia/hnoti"/>
</TD></TR>
</xsl:template>

<!-- TEMPLATE CENTRAL -->
<xsl:template match="central">
<table style="margin:0.2cm 0cm 0cm 0cm;">
<tr><td align="center">
<xsl:apply-templates select="home"/>
<xsl:apply-templates select="content"/>
<xsl:apply-templates select="noticia"/>
<xsl:apply-templates select="comment"/>
<xsl:apply-templates select="newslst"/>
<xsl:apply-templates select="gals"/>
<xsl:apply-templates select="thumb"/>
<xsl:apply-templates select="subs"/>
<xsl:apply-templates select="contact"/>
</td></tr>
</table>
</xsl:template>

<!-- TEMPLATE BLEFT -->
<xsl:template match="bleft">
<xsl:apply-templates select="noticias"/>
<xsl:apply-templates select="mnu_subs"/>
<xsl:apply-templates select="mnu_about"/>
</xsl:template>

<!-- TEMPLATE NOTICIAS -->
<xsl:template match="noticias">
<TABLE>
<TR><TD>
<div><xsl:value-of select="@title"/></div>
</TD></TR>
<TR>
<TD>
<xsl:apply-templates select="notiport"/>
</TD></TR></TABLE>
</xsl:template>

<!-- TEMPLATE NOTIPORT -->
<xsl:template match="notiport">
<div >
<strong style="color:#164C7B"><xsl:value-of select="date"/></strong><br/>
<xsl:value-of select="titnoti"/><br/>
<div align="right"><a href="{more/@href}">
<xsl:value-of select="more"/></a>
</div>
</div>
</xsl:template>

<!-- TEMPLATE CONTENT | COMMENT -->
<xsl:template match="content|comment">
<div style="font: bold 14px arial;padding:0.2cm 0.2cm 0.2cm 0.2cm;text-align:left;margin:0.2cm 0cm 0.2cm 0cm;background-color:#ADADAD" >
<xsl:apply-templates/>
</div>        
</xsl:template>

<!-- TEMPLATE NEWSLST | GALS | CONTACT -->
<xsl:template match="newslst|gals|contact">
<xsl:apply-templates/>
</xsl:template>

<!-- TEMPLATE NOTICIA -->
<xsl:template match="noticia">
<xsl:if test="boolean(imgnot)">
<table style="float:left"><tr><td>
<xsl:for-each select="imgnot">
<img style="margin:0cm 0.2cm 0.2cm 0cm" src="{@src}" alt=""/><br/>
</xsl:for-each>
</td></tr></table>
</xsl:if>
<table style="float:right;background-color:#B6D3EC"><tr><td style="padding:0.2cm 0.2cm 0.2cm 0.2cm">
<a style="font:bold 18px arial" href="http://www.aleix41.com">www.aleix41.com</a>
</td></tr></table>
<div style="text-align:left;font: bold 14px arial;color:#A7A6A6">
<xsl:value-of select="date"/>
</div>

<div style="margin:0.2cm 0cm 0cm 0cm;text-align:left;font: bold 18px arial;color:#174D7B"><xsl:value-of select="title"/></div>
<div style="margin:0.2cm 0cm 0cm 0cm;text-align:left;font: bold 14px arial"><xsl:apply-templates select="resum"/></div>
<div style="margin:0.2cm 0cm 0cm 0cm;text-align:left;font: normal 14px arial"><xsl:apply-templates select="texto"/></div>
</xsl:template>


<!-- TEMPLATE FOOTERS -->
<xsl:template match="footers">
<TABLE style="WIDTH:776px">
   <TR>
   <TD style="height:1px;WIDTH:100%;background-color:#174D7B">
   </TD>
   </TR>
   <TR>
   <TD WIDTH="776" HEIGHT="47">
   <div >
   <xsl:apply-templates select="label"/> 
   </div>
   </TD>
   </TR>
   </TABLE>
</xsl:template>
			
</xsl:stylesheet>

