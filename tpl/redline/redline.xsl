<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:output method="xml"
		doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
		indent="yes" />

	<!-- Aqui ponemos el contenido del template -->
	<!-- TEMPLATE COPY ALL ELEMENTS WITHOUT TEMPLATE -->
	<xsl:template match="node()">
		<xsl:element name="{local-name()}">
			<xsl:apply-templates select="@*|node()|text()" />
		</xsl:element>
	</xsl:template>

	<xsl:template match="@*|text()" priority="3">
		<xsl:copy-of select="." />
	</xsl:template>

	<!-- TEMPLATE html -->
	<xsl:template match="html">
		<html xml:lang="{//html/page/@lang}"
			lang="{//html/page/@lang}">
			<xsl:apply-templates />
		</html>
	</xsl:template>

	<!-- TEMPLATE OTHERS -->
	<xsl:template
		match="subs|contact|text|fbutton|mnu_oferta|mnu_about|mnu_sell|mnu_subs|mnu_bguide|htmlform|news_main|resum|mnu_inews|footer">
		<xsl:apply-templates />
	</xsl:template>

	<!-- TEMPLATE EMPTIES -->
	<xsl:template
		match="subs_title|titlespecial|titlesubs|titleisearch|title_home">
	</xsl:template>

	<!-- TEMPLATE BODY -->
	<xsl:template match="page">
	<style type="text/css">
	td.ipgborder {width:18px;background-image: url(<xsl:value-of select="//html/page/@path" />/tpl/redline/images/sleft.jpg);background-repeat:repeat-y;}
	td.dpgborder {width:18px;background-image: url(<xsl:value-of select="//html/page/@path" />/tpl/redline/images/sright.jpg);background-repeat:repeat-y;}
	td.piecentral {font: normal 11px arial;text-align:left;background-image: url(<xsl:value-of select="//html/page/@path" />/tpl/redline/images/footer2.jpg);background-repeat:repeat-x;background-position:left top;padding: 0.2cm 0.2cm}
	td.tpprop {text-align:center;padding: 0cm 0cm 0cm 0cm;vertical-align:middle;background-image: url(<xsl:value-of select="//html/page/@path" />/tpl/redline/images/spbg.jpg);background-repeat:repeat-x;background-position:left top;}
	td.tppropin {padding:0cm 0.2cm 0cm 0cm;background-image: url(<xsl:value-of select="//html/page/@path" />/tpl/redline/images/spbg.jpg);background-repeat:repeat-x;background-position:left top;}
	</style>
	
		<link rel="StyleSheet" href="{//html/page/@path}/tpl/redline/style/style.css"
		/>
		<body>
			<xsl:if test="boolean(@onload)">
				<xsl:attribute name="onload">
      <xsl:value-of select="@onload" />
      </xsl:attribute>
			</xsl:if>
			<div align="center">
				<table class="pagina">
					<tr>
						<td class="ipgborder"></td>
						<td>
							<xsl:apply-templates select="headers" />
							<table class="tablas">
								<tr>
									<xsl:apply-templates select="bleft" />
									<xsl:apply-templates
										select="central" />
									<xsl:apply-templates
										select="bright" />
								</tr>
							</table>
							<xsl:apply-templates select="footers" />
						</td>
						<td class="dpgborder"></td>
					</tr>
				</table>
			</div>
		</body>
	</xsl:template>

	<!-- TEMPLATE BLEFT -->
	<xsl:template match="bleft">
		<xsl:choose>
			<xsl:when
				test="not(boolean(/html/page/central/loginform))">
				<td class="bleft">
					<xsl:for-each select="*">
						<table class="bloci">
							<tr class="hbloci">
								<td class="hbloci">
									<xsl:value-of select="@title" />
								</td>
							</tr>
							<tr class="bloci">
								<td class="bloci">
									<xsl:apply-templates
										select="current()" mode="col" />
								</td>
							</tr>
						</table>
					</xsl:for-each>
				</td>
			</xsl:when>
			<xsl:otherwise>
				<td class="bleft">
					<xsl:apply-templates />
				</td>

			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- TEMPLATE CENTRAL -->
	<xsl:template match="central">
		<td class="pcentral">
			<xsl:choose>
				<xsl:when
					test="boolean(content/home)or boolean(loginform)">
					<xsl:apply-templates />
				</xsl:when>

				<xsl:when test="boolean(immo)">
					<xsl:apply-templates select="//page/msg/position" />
					<xsl:apply-templates select="pgtitle" />
					<table>
						<tr>
							<xsl:if test="boolean(pgtitle/options)">
								<td width="35%">
									<xsl:apply-templates
										select="pgtitle/options" />
									<!--xsl:apply-templates select="imggal"/-->
								</td>
							</xsl:if>
							<td style="padding: 0cm 0.4cm 0cm 0.5cm">
								<xsl:apply-templates
									select="immo|imggal" />
								<xsl:if
									test="boolean(activities|equip|services|observ|capacity)">
									<table>
										<xsl:apply-templates
											select="activities|equip|services|observ|capacity" />
									</table>
								</xsl:if>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<xsl:apply-templates select="thumb" />
								<xsl:apply-templates select="immocal" />
							</td>
						</tr>
					</table>

				</xsl:when>

				<xsl:otherwise>
					<xsl:apply-templates select="//page/msg/position" />
					<xsl:if
						test="not(boolean(//page/central/pgtitle))">
						<xsl:apply-templates select="//page/msg/alerts" />
					</xsl:if>
					<xsl:apply-templates />
				</xsl:otherwise>
			</xsl:choose>

		</td>
	</xsl:template>

	<!-- TEMPLATE HOME -->
	<xsl:template match="home">
		<div style="margin:0.2cm 0.2cm 0.2cm 0.2cm ">
		<xsl:apply-templates />
		</div>
	</xsl:template>

	<!-- TEMPLATE BRIGHT -->
	<xsl:template match="bright">
		<td class="bright">
			<xsl:apply-templates select="langs" />
			<xsl:apply-templates select="tp_property" />
			<xsl:for-each select="mnu_subs|mnu_about|mnu_bguide">
				<table class="blocd">
					<tr class="hblocd">
						<td class="hblocd">
							<xsl:value-of select="@title" />
						</td>
					</tr>
					<tr class="blocd">
						<td class="blocd">
							<xsl:apply-templates select="current()" />
						</td>
					</tr>
				</table>
			</xsl:for-each>
			<xsl:apply-templates
				select="immosearch|offices|content|mnu" />
		</td>
	</xsl:template>

	<!-- TEMPLATE MENU -->

	<xsl:template match="mnu">
		<table class="blocd">
			<tr class="hblocd">
				<td class="hblocd">
					<xsl:value-of select="@title" />
				</td>
			</tr>
			<tr class="blocd">
				<td class="blocd">
					<table>
						<tr class="mnu_item">
							<xsl:for-each select="*"><!-- *[not(name()='ofis')][not(name()='bguide') ] -->
								<td class="mnu_itimg">
									<img class="mnu"
										src="{//html/page/@path}/tpl/redline/images/arrow.jpg" alt="" />
								</td>
								<td class="mnu_item">
									<a class="mnu" href="{@href}">
										<xsl:value-of select="." />
									</a>
								</td>
							</xsl:for-each>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE MOPTIONS -->

	<xsl:template match="moptions">
		<table>
			<tr class="mnu_item">
				<xsl:for-each select="*[not(name()='ofis')]">
					<!-- *[not(name()='ofis')][not(name()='bguide') ] -->
					<td class="mnu_itimg">
						<img class="mnu"
							src="{//html/page/@path}/tpl/redline/images/arrow.jpg" alt="" />
					</td>
					<td class="mnu_item">
						<a class="mnu" href="{@href}">
							<xsl:value-of select="." />
						</a>
					</td>
				</xsl:for-each>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="moptions" mode="col">
		<table>
			<xsl:for-each select="*"><!-- *[not(name()='ofis')][not(name()='bguide') ] -->
				<tr class="mnu_item">
					<td class="mnu_itimg">
						<img class="mnu"
							src="{//html/page/@path}/tpl/redline/images/arrow.jpg" alt="" />
					</td>
					<td class="mnu_item">
						<a class="mnu" href="{@href}">
							<xsl:value-of select="." />
						</a>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	<!-- TEMPLATE LOGOUT -->

	<xsl:template match="logout">
		<td
			style="text-align:right;padding:0cm 0.2cm 0.2cm 0cm;vertical-align:bottom;">
			<a class="mnu" href="{item/@href}">
				<xsl:value-of select="." />
			</a>
		</td>
	</xsl:template>


	<!-- TEMPLATE HEADERS -->

	<xsl:template match="headers">
		<table class="head">
			<tr class="head">
				<td class="head">
					<a class="head" href="">
						<img class="head"
							src="{//html/page/@path}/tpl/redline/images/logo_{//page/@lang}.jpg" alt="" />
					</a>
				</td>
				<td style="font: Bold 24px arial;color:#C54528">
					Tel. (xx) xxx xx xx xx
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="*[not(name()='logout')]" />
	</xsl:template>

	<!-- TEMPLATE LANGS -->

	<xsl:template match="langs">
	<xsl:if test="count(item) > 1"> 
		<table class="langs">
			<tr>
				<td class="langs">
					<xsl:for-each select="item">
						<a href="{@href}">
							<xsl:value-of select="." />
						</a>
						<xsl:if test="(position()&#60;last())">
							|
						</xsl:if>
					</xsl:for-each>
				</td>
			</tr>
		</table>
		</xsl:if> 
	</xsl:template>

	<!-- TEMPLATE MSG -->
	<xsl:template match="msg">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="position">
		<table class="msg_position">
			<tr>
				<td class="msg_position">

					<xsl:for-each select="pos">
						<xsl:text> >> </xsl:text>
						<xsl:choose>
							<xsl:when test="boolean(@href)">
								<a class="msg_position"
									href="{@href}">
									<xsl:value-of select="." />
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="." />
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</td>
				<xsl:if test="boolean(/html/page/logout)">
				<td class="logout">
						<xsl:apply-templates select="/html/page/logout" />
				</td>
				</xsl:if>	
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE RESULTS BY CITIES -->

	<xsl:template match="cities">

		<table
			style="margin:0cm 0cm 0.2cm 0.2cm;width:97%;background-image:url({//html/page/@path}/tpl/redline/images/bg.jpg);background-repeat:repeat-x;">
			<tr>
				<td>
					<img src="{//html/page/@path}/tpl/redline/images/upleft.jpg" alt="" />
				</td>
				<td></td>
				<td style="text-align:right;">
					<img src="{//html/page/@path}/tpl/redline/images/upright.jpg" alt="" />
				</td>
			</tr>
			<tr>
				<td
					style="text-align:left;background-image:url({//html/page/@path}/tpl/redline/images/vleft.jpg);background-repeat:repeat-y;background-position:top left">
					<img src="{//html/page/@path}/tpl/redline/images/bleft.jpg" alt="" />
				</td>
				<td>

					<table>
						<!--<tr class="hbox"><td class="hbox"><xsl:value-of select="title"/></td></tr>-->
						<tr class="box">
							<td class="box">
								<xsl:for-each select="item">
									<a class="box" href="{@href}">
										<xsl:value-of select="." />
										(
										<xsl:value-of select="@num" />
										)
									</a>
									<br />
								</xsl:for-each>
							</td>
						</tr>
					</table>

				</td>
				<td
					style="text-align:right;background-image:url({//html/page/@path}/tpl/redline/images/vright.jpg);background-repeat:repeat-y;background-position:top right">
					<img src="{//html/page/@path}/tpl/redline/images/bright.jpg" alt="" />
				</td>
			</tr>
			<tr
				style="background-image:url({//html/page/@path}/tpl/redline/images/bttom.jpg);background-repeat:repeat-x;background-position:bottom left">
				<td style="text-align:left">
					<img src="{//html/page/@path}/tpl/redline/images/doleft.jpg" alt="" />
				</td>
				<td></td>
				<td style="text-align:right">
					<img src="{//html/page/@path}/tpl/redline/images/doright.jpg" alt="" />
				</td>
			</tr>
		</table>

	</xsl:template>

	<!-- TEMPLATE RECOMENDED -->

	<xsl:template match="recom">
		<table
			style="margin:0cm 0cm 0.2cm 0.2cm;width:97%;background-image:url({//html/page/@path}/tpl/redline/images/bg.jpg);background-repeat:repeat-x;">
			<tr>
				<td>
					<img src="{//html/page/@path}/tpl/redline/images/upleft.jpg" alt="" />
				</td>
				<td></td>
				<td style="text-align:right;">
					<img src="{//html/page/@path}/tpl/redline/images/upright.jpg" alt="" />
				</td>
			</tr>
			<tr>
				<td
					style="text-align:left;background-image:url({//html/page/@path}/tpl/redline/images/vleft.jpg);background-repeat:repeat-y;background-position:top left">
					<img src="{//html/page/@path}/tpl/redline/images/bleft.jpg" alt="" />
				</td>
				<td>
					<table class="recom">
						<xsl:apply-templates
							select="item[position() mod 3 = 1]" mode="row3" />
					</table>
				</td>
				<td
					style="text-align:right;background-image:url({//html/page/@path}/tpl/redline/images/vright.jpg);background-repeat:repeat-y;background-position:top right">
					<img src="{//html/page/@path}/tpl/redline/images/bright.jpg" alt="" />
				</td>
			</tr>
			<tr
				style="background-image:url({//html/page/@path}/tpl/redline/images/bttom.jpg);background-repeat:repeat-x;background-position:bottom left">
				<td style="text-align:left">
					<img src="{//html/page/@path}/tpl/redline/images/doleft.jpg" alt="" />
				</td>
				<td></td>
				<td style="text-align:right">
					<img src="{//html/page/@path}/tpl/redline/images/doright.jpg" alt="" />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE RECOM/ITEM -->
	<xsl:template match="recom/item">
		<td class="recom">
			<xsl:for-each select="img">
				<a href="{../@href}">
					<img class="recom" src="{@src}" alt="" />
				</a>
				<br />
			</xsl:for-each>
			<a href="{@href}">
				<xsl:value-of select="refe" />
			</a>
			<br />
			<b>
				<xsl:value-of select="transaction" />
			</b>
			<br />
			<xsl:value-of select="city" />
			<xsl:if test="boolean(zone)">
				<br />
				<xsl:value-of select="zone" />
			</xsl:if>
			<br />
			<b>
				<xsl:value-of select="price/label" />
				<xsl:value-of select="price/valprc" />
			</b>
		</td>
	</xsl:template>

	<!-- TEMPLATE NEWS -->

	<xsl:template match="news">
		<xsl:apply-templates />
	</xsl:template>

	<!-- TEMPLATE NOTICIA PORTADA -->

	<xsl:template match="news/new">
		<table>
			<tr class="noti_port">
				<td class="noti_port">
					<xsl:if test="boolean(imgnew)">
						<table class="not_img_pot">
							<tr>
								<td class="not_img_port">
									<xsl:for-each select="imgnew">
										<img class="not_img_port"
											src="{@src}" alt="" />
										<br />
									</xsl:for-each>
								</td>
							</tr>
						</table>
					</xsl:if>
					<span class="noti_fecha">
						<xsl:value-of select="date" />
					</span>
					<br />
					<xsl:value-of select="title" />
					<br />
					<a href="{more/@href}">
						<xsl:value-of select="more/." />
					</a>
					<br />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE NOTICIA -->
	<xsl:template match="new">
		<table class="noticia">
			<tr class="hnoticia">
				<td class="hnoticia">
					<xsl:value-of select="hnew" />
				</td>
			</tr>
			<tr>
				<td class="noticia">

					<xsl:if test="boolean(imgnew)">
						<table class="noticia_img">
							<tr>
								<td class="noticia_img">
									<xsl:for-each select="imgnew">
										<img class="noticia_img"
											src="{@src}" alt="" />
										<br />
									</xsl:for-each>
								</td>
							</tr>
						</table>
					</xsl:if>
					<div class="noti_fecha">
						<xsl:value-of select="date" />
					</div>
					<div class="titnoti">
						<xsl:value-of select="title" />
					</div>
					<div class="resum_noti">
						<xsl:apply-templates select="resum" />
					</div>
					<div class="txt_noti">
						<xsl:apply-templates select="text" />
					</div>
					<a href="{more/@href}">
						<xsl:value-of select="more/." />
					</a>
					<br />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE ADVERTS -->

	<xsl:template match="adverts|list">
		<table class="lista_advert">
			<xsl:if test="boolean(found)">
				<tr>
					<td class="list_advert_found" colspan="2">
						<xsl:value-of select="found" />
					</td>
				</tr>
			</xsl:if>
			<xsl:apply-templates select="lst_info" />
			<xsl:for-each select="advert">
				<tr class="linea_advert0">
					<td class="lista_advert" colspan="2">
						<xsl:apply-templates select="current()" />
					</td>
				</tr>
			</xsl:for-each>
			<xsl:apply-templates select="list_items" />
			<xsl:apply-templates select="lst_info" />
		</table>
	</xsl:template>

	<xsl:template match="lst_info">
		<tr>
			<td class="list_advert_pages">
				<xsl:value-of select="label" />
				&#160;
				<xsl:value-of select="current" />
				&#160;
				<xsl:value-of select="total" />
			</td>
			<td class="list_advert_links">
				<xsl:if test="boolean(pags/prev)">
					<a class="list_page" href="{pags/prev/@href}">
						&#160;&lt;&lt;&#160;
					</a>
				</xsl:if>
				<xsl:for-each select="pags/pag">
					<xsl:choose>
						<xsl:when test="boolean(@href)">
							<a href="{@href}">
								<xsl:value-of select="." />
							</a>
						</xsl:when>
						<xsl:otherwise>
							<span class="list_advert_page">
								<xsl:value-of select="." />
							</span>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="not(position()=last())">
						<xsl:text> - </xsl:text>
					</xsl:if>
				</xsl:for-each>
				<xsl:if test="boolean(pags/next)">
					<a class="list_page" href="{pags/next/@href}">
						<xsl:text>&#160;>>&#160;</xsl:text>
					</a>
				</xsl:if>
			</td>
		</tr>
	</xsl:template>

	<!-- LIST ITEMS -->
	<xsl:template match="list_items">
		<tr>
			<td colspan="2" style="text-align:center">
				<table style="width:100%">
					<tr>
						<xsl:apply-templates select="cols/*" />
					</tr>
					<xsl:apply-templates select="row" />
				</table>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="cols/*">
		<td class="col">
			<xsl:value-of select="." />
		</td>
	</xsl:template>

	<xsl:template match="row">
		<!--<tr class="linea" onmouseover="this.className='linea_over'" onmouseout="this.className='linea'">-->
		<tr class="linea">
			<xsl:apply-templates select="item" />
		</tr>
	</xsl:template>

	<!-- TEMPLATE ITEM-->

	<xsl:template match="item">
		<xsl:for-each select="*">
			<td class="linea">
				<xsl:choose>
					<xsl:when
						test="name()='img_front' and current()!=''">
						<a href="{parent::item/@href}">
							<xsl:element name="img">
								<xsl:attribute name="class">thumb_gallery</xsl:attribute>
								<xsl:attribute name="src">padmin&#47;galeria&#47;<xsl:value-of
										select="." />
								</xsl:attribute>
								<xsl:attribute name="alt"></xsl:attribute>
							</xsl:element>
						</a>
					</xsl:when>

					<xsl:when test="boolean(parent::item/@href)">
						<a class="linea" href="{parent::item/@href}">
							<xsl:value-of select="." />
						</a>
					</xsl:when>

					<xsl:otherwise>
						<xsl:value-of select="." />
					</xsl:otherwise>
				</xsl:choose>
			</td>
		</xsl:for-each>
	</xsl:template>

	<!-- TEMPLATE ADVERT -->

	<xsl:template match="advert">
		<table class="advert">
			<tr>
				<xsl:if test="boolean(img)">
					<td class="advert_img">
						<a href="{@href}">
							<img class="img_advert_immo"
								src="{img/@src}" alt="" />
						</a>
					</td>
				</xsl:if>
				<td class="advert_txt">
					<div class="tit_adpob">
						<xsl:choose>
							<xsl:when test="boolean(@href)">
								<a href="{@href}">
									<xsl:value-of select="refe" />
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="refe" />
							</xsl:otherwise>
						</xsl:choose>
						,
						<xsl:value-of select="city" />
						<xsl:if test="boolean(zone)">
							-
							<xsl:value-of select="zone" />
						</xsl:if>
					</div>
					<div class="tit_adtp">
						<xsl:value-of select="transaction" />
					</div>
					<div class="adtxt">
						<xsl:value-of select="text" />
					</div>
					<div class="adprecio">
						<xsl:value-of select="price/label" />
						<xsl:value-of select="price/valprc" />
					</div>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE FOOTERS -->

	<xsl:template match="footers">
		<table class="piecentral">
			<tr>
				<td class="piecentral">
					<xsl:apply-templates />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE PGTITLE -->

	<xsl:template match="pgtitle">
		<table class="pgtitle">
			<tr style="font:bold 2px arial">
				<td>
					<img src="{//html/page/@path}/tpl/redline/images/pgt_01.jpg" alt="" />
				</td>
				<td></td>
				<td></td>
				<td style="text-align:right;">
					<img src="{//html/page/@path}/tpl/redline/images/pgt_03.jpg" alt="" />
				</td>
			</tr>
			<tr style="font:bold 5px arial">
				<td></td>
				<td class="pgtitle_label">
					<xsl:value-of select="title" />
				</td>
				<td class="pgtitle_options">
					<table class="pgoptions">
						<xsl:if test="boolean(options/back)">
							<tr>
								<td class="pgoptions">
									<a class="pgtitle"
										href="{options/back/@href}">
										<img class="pgtitle"
											src="{//html/page/@path}/tpl/redline/images/aleft.jpg" alt="" />
										<xsl:text> </xsl:text>
										<xsl:value-of
											select="options/back" />
									</a>
								</td>
							</tr>
						</xsl:if>


					</table>
				</td>
				<td></td>
			</tr>
			<tr style="font:bold 2px arial">
				<td>
					<img src="{//html/page/@path}/tpl/redline/images/pgt_06.jpg" alt="" />
				</td>
				<td></td>
				<td></td>
				<td style="text-align:right;">
					<img src="{//html/page/@path}/tpl/redline/images/pgt_07.jpg" alt="" />
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="//page/msg/alerts" />
	</xsl:template>

	<!-- TEMPLATE PGTITLE/OPTIONS -->

	<xsl:template match="pgtitle/options">
		<table style="margin:0cm 0cm 0cm 0.4cm;">
			<xsl:for-each select="item">
				<tr>
					<td>
						<a href="{@href}" target="{@target}"
							onclick="{@onclick}">
							<img class="pgtitle"
								src="{//html/page/@path}/tpl/redline/images/arrow.jpg" alt="" />
						</a>
					</td>
					<td style="padding:0cm 0cm 0cm 0.2cm">
						<a class="pgoptions" href="{@href}"
							target="{@target}" onclick="{@onclick}">
							<xsl:text> </xsl:text>
							<xsl:value-of select="." />
						</a>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	<!-- TEMPLATE TP_PROPERTY -->

	<xsl:template match="tp_property">
		<table class="tpprop">
			<tr class="tpprop">
				<td class="tppropin">
					<img src="{//html/page/@path}/tpl/redline/images/spleft.jpg" alt="" />
				</td>
				<xsl:for-each select="item">
					<td class="tpprop">
						<a class="tpprop" href="{@href}">
							<!--<img src="{//html/page/@path}/tpl/redline/images/tp{@id}.gif" alt="" /><br/>-->
							<xsl:value-of select="." />
							(
							<xsl:value-of select="@num" />
							)
						</a>
					</td>
					<xsl:if test="not(position()=last())">
						<td class="tppropin"></td>
					</xsl:if>
				</xsl:for-each>
				<td>
					<img src="{//html/page/@path}/tpl/redline/images/spright.jpg" alt="" />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE SPECIAL -->

	<xsl:template match="special">
		<table class="special">

			<!--<tr class="hbox"><td class="hbox">
				<xsl:value-of select="title"/>
				</td></tr>-->

			<tr class="special">
				<td class="special_text">
					<a href="{advert/@href}">
						<img
							style="float:left;margin: 0cm 0.2cm 0.2cm 0cm" width="200px"
							src="{thumb/@dir}/images/{thumb/@file}" alt="" />
					</a>
					<div class="tit_adpob">
						<xsl:choose>
							<xsl:when test="boolean(advert/@href)">
								<a href="{advert/@href}">
									<xsl:value-of select="advert/refe" />
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="advert/refe" />
							</xsl:otherwise>
						</xsl:choose>
						,
						<xsl:value-of select="advert/city" />
						<xsl:if test="boolean(advert/zone)">
							-
							<xsl:value-of select="advert/zone" />
						</xsl:if>
					</div>
					<div class="tit_adtp">
						<xsl:value-of select="advert/transaction" />
					</div>
					<div class="adtxt">
						<xsl:value-of select="advert/text" />
					</div>
					<div class="adprecio">
						<xsl:value-of select="advert/price/label" />
						<xsl:value-of select="advert/price/valprc" />
					</div>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE IMMO -->

	<xsl:template match="immo">
		<table style="margin:0cm 0.3cm 0cm 0.3cm">
			<tr>
				<td>
					<xsl:apply-templates select="advert" />
				</td>
			</tr>
		</table>
		<xsl:if
			test="boolean(activities|equip|services|observ|capacity)">
			<table>
				<xsl:apply-templates
					select="activities|equip|services|observ|capacity" />
			</table>
		</xsl:if>
	</xsl:template>

	<!-- TEMPLATE PROPERTIES HOLIDAY LODGING -->

	<xsl:template match="equip|services|activities|observ|capacity">
		<tr>
			<td class="extras_title">
				<xsl:value-of select="@title" />
				:
			</td>
			<td class="extras_txt">
				<xsl:value-of select="." />
				.
			</td>
		</tr>
	</xsl:template>

	<!-- TEMPLATE THUMB -->

	<xsl:template match="thumb">
		<script type="text/javascript"
			src="{//html/page/@path}/tpl/reos/jscripts/lightbox.js">
			<xsl:text> </xsl:text>
		</script>
		<link rel="StyleSheet" href="{//html/page/@path}/tpl/redline/style/lightbox.css"
			type="text/css" />
		<table class="thumb_gallery">
			<xsl:apply-templates select="item[position() mod 4 = 1]"
				mode="row4" />
		</table>
	</xsl:template>

	<xsl:template match="item" mode="row4">
		<tr>
			<xsl:apply-templates
				select=". | following-sibling::item[position() &lt; 4]" />
		</tr>
	</xsl:template>

	<xsl:template match="item" mode="row3">
		<tr>
			<xsl:apply-templates
				select=". | following-sibling::item[position() &lt; 3]" />
		</tr>
	</xsl:template>

	<!-- TEMPLATE THUMB ITEM -->
	<xsl:template match="thumb/item">
		<td class="thumb_gallery">
			<xsl:choose>
				<xsl:when test="boolean(@href)">
					<a href="{@href}">
						<img class="thumb_gallery"
							src="{@dir}/thumbnails/{@src}" alt="" />
					</a>
				</xsl:when>
				<xsl:when test="boolean(@pop)">
					<a href="{@dir}/images/{@src}" rel="lightbox">
						<img class="thumb_gallery"
							src="{@dir}/thumbnails/{@src}" alt="" />
					</a>
					<xsl:if test="boolean(@del)">
						<br />
						<a href="{@del}" onclick="{@delclick}">
							<xsl:value-of select="@deltxt" />
						</a>
					</xsl:if>
					<xsl:choose>
						<xsl:when
							test="boolean(@front) and boolean(@fronttxt)">
							<br />
							<a href="{@front}">
								<xsl:value-of select="@fronttxt" />
							</a>
							..
						</xsl:when>
						<xsl:otherwise>
							<br />
							<xsl:value-of select="@fronttxt" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<img class="thumb_gallery"
						src="{@dir}/thumbnails/{@src}" alt="" />
				</xsl:otherwise>
			</xsl:choose>
		</td>
	</xsl:template>



	<!-- TEMPLATE IMAGE GALLERY -->

	<xsl:template match="imggal">
		<table class="img_gallery">
			<xsl:for-each select="item">
				<tr>
					<td class="img_gallery">
						<img class="imggal" src="{@src}" alt="" />
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	<!-- TEMPLATE IMMOSEARCH -->

	<xsl:template match="immosearch">
		<xsl:apply-templates />
	</xsl:template>

	<!-- TEMPLATE CONTENT GENERAL -->
	<xsl:template match="content">
		<div class="about">
			<xsl:apply-templates select="//page/msg/alerts" />
			<xsl:apply-templates />
		</div>
	</xsl:template>

	<!-- TEMPLATE LOGINFORM -->
	<xsl:template match="loginform">
		<div class="about">
			<xsl:apply-templates />
			<div class="margins">
				<xsl:for-each select="logrem">
					<a href="{@href}">
						<xsl:value-of select="." />
					</a>
					<xsl:if test="(position()&#60;last())"> | </xsl:if>
				</xsl:for-each>
			</div>
		</div>
	</xsl:template>

	<!-- TEMPLATE LOGREM -->
	<xsl:template match="logrem"></xsl:template>

	<!-- TEMPLATE COMMENT|CONFIRM -->
	<xsl:template match="comment|confirm">
		<table class="comment">
			<tr>
				<td class="comment">
					<xsl:apply-templates />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE ALERTS-->
	<xsl:template match="alerts">
		<table class="msg_alerts">
			<xsl:for-each select="msg">
				<tr>
					<td class="msg_alerts">
						<xsl:value-of select="." />
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	<!-- TEMPLATE HIPOTECA -->
	<xsl:template match="hipotec">
		<xsl:apply-templates select="formdef" />
		<xsl:if test="boolean(gst)">
			<table class="calc_div">
				<tr>
					<xsl:for-each select="gst">
						<td class="calc_div">
							<xsl:apply-templates select="current()" />
						</td>
					</xsl:for-each>
				</tr>
				<tr>
					<td colspan="2" class="total_div">
						<xsl:value-of select="total/label" />
						<xsl:value-of select="total/value" />
					</td>
				</tr>
			</table>
		</xsl:if>
	</xsl:template>

	<!-- TEMPLATE GST HIPOTECA-->
	<xsl:template match="gst">
		<table class="calc">
			<tr>
				<td colspan="2" class="hcalc">
					<xsl:value-of select="title" />
				</td>
			</tr>
			<xsl:for-each select="item">
				<tr>
					<td class="label">
						<xsl:value-of select="label" />
					</td>
					<td class="values">
						<xsl:value-of select="value" />
					</td>
				</tr>
			</xsl:for-each>
			<tr>
				<td class="tlabel">
					<xsl:value-of select="total/label" />
				</td>
				<td class="tvalues">
					<xsl:value-of select="total/value" />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE OFFICES -->
	<xsl:template match="offices">
		<table class="oficinas">
			<xsl:for-each select="row">
				<tr class="oficinas">
					<td class="oficinas">
						<xsl:if test="boolean(name_org)">
							<span class="tit_oficina">
								<xsl:value-of select="name_org" />
							</span>
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_address1)">
							<xsl:value-of select="txt_address1" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_zone)">
							<xsl:value-of select="txt_zone" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_cp)">
							<xsl:value-of select="txt_cp" />
							<xsl:text> </xsl:text>
						</xsl:if>
						<xsl:if test="boolean(txt_poblacion)">
							<xsl:value-of select="txt_poblacion" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_provincia)">
							<xsl:value-of select="txt_provincia" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_telf1)">
							<xsl:value-of select="txt_telf1/@label" />
							:
							<xsl:value-of select="txt_telf1" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_fax)">
							<xsl:value-of select="txt_fax/@label" />
							:
							<xsl:value-of select="txt_fax" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_email1)">
							<xsl:value-of select="txt_email1/@label" />
							:
							<xsl:value-of select="txt_email1" />
							<br />
						</xsl:if>
						<xsl:if test="boolean(txt_web)">
							<xsl:value-of select="txt_web" />
							<br />
						</xsl:if>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	<!-- TEMPLATE GRID -->
	<xsl:template match="grid">
		<table>
			<tr>
				<xsl:for-each select="hrow/*">
					<td>
						<xsl:value-of select="." />
					</td>
				</xsl:for-each>
			</tr>
			<xsl:for-each select="row">
				<tr>
					<xsl:for-each select="*">
						<td>
							<xsl:value-of select="." />
						</td>
					</xsl:for-each>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	<!-- TEMPLATE FORMDEF -->
	<xsl:template match="formdef">
		<table
			style="margin:0.2cm 0cm 0.2cm 0.2cm;width:95%;background-image:url({//html/page/@path}/tpl/redline/images/bg.jpg);background-repeat:repeat-x;">
			<tr>
				<td>
					<img src="{//html/page/@path}/tpl/redline/images/upleft.jpg" alt="" />
				</td>
				<td></td>
				<td style="text-align:right;">
					<img src="{//html/page/@path}/tpl/redline/images/upright.jpg" alt="" />
				</td>
			</tr>
			<tr>
				<td
					style="text-align:left;background-image:url({//html/page/@path}/tpl/redline/images/vleft.jpg);background-repeat:repeat-y;background-position:top left">
					<img src="{//html/page/@path}/tpl/redline/images/bleft.jpg" alt="" />
				</td>
				<td>
					<!--<table class="forms_border"><tr><td class="forms_border">-->
					<xsl:apply-templates select="htmlform" />
					<!--</td></tr>
						<tr><td class="form_button">-->
					<div style="text-align:right">
						<a class="boton"
							href="javascript:document.getElementById('{@name}').submit();">
							<img class="boton"
								src="{//html/page/@path}/tpl/redline/images/form_butt.gif" alt="" />
							<xsl:apply-templates select="fbutton" />
						</a>
					</div>
					<!--</td></tr>
						</table>-->

				</td>
				<td
					style="text-align:right;background-image:url({//html/page/@path}/tpl/redline/images/vright.jpg);background-repeat:repeat-y;background-position:top right">
					<img src="{//html/page/@path}/tpl/redline/images/bright.jpg" alt="" />
				</td>
			</tr>
			<tr
				style="background-image:url({//html/page/@path}/tpl/redline/images/bttom.jpg);background-repeat:repeat-x;background-position:bottom left">
				<td style="text-align:left">
					<img src="{//html/page/@path}/tpl/redline/images/doleft.jpg" alt="" />
				</td>
				<td></td>
				<td style="text-align:right">
					<img src="{//html/page/@path}/tpl/redline/images/doright.jpg" alt="" />
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- TEMPLATE HTMLFORM -->
	<xsl:template match="xmlform">

		<table>
			<xsl:for-each
				select="tp_propiedad|tp_servicio|txt_poblacion|txt_zona|precio_max|data">
				<tr>
					<td
						style="text-align:left;font: bold 12px arial;color:black">
						<xsl:value-of select="@title" />
					</td>
				</tr>
				<tr>
					<td style="text-align:left;">
						<xsl:apply-templates />
					</td>
				</tr>
			</xsl:for-each>
			<tr
				style="text-align:left;font: bold 12px arial;color:black">
				<td>
					<xsl:value-of select="order_by/@title" />
				</td>
			</tr>
			<tr style="text-align:left">
				<td>
					<xsl:apply-templates select="order_by" />
				</td>
			</tr>
		</table>

	</xsl:template>


	<!-- TEMPLATE ADDIMG -->
	<xsl:template match="addimg">
		<xsl:apply-templates />

		<div style="padding:0cm 1.5cm 0.2cm 0cm;text-align:right;">
			<xsl:for-each select="//pgtitle/options/item">
				<a class="pgoptions" href="{@href}" target="{@target}"
					onclick="{@onclick}">
					<img style="vertical-align:middle;"
						src="{//html/page/@path}/tpl/redline/images/form_butt.gif" alt="" />
					<xsl:text> </xsl:text>
					<xsl:value-of select="." />
				</a>
				<br />
			</xsl:for-each>
		</div>
	</xsl:template>

	<!-- TEMPLATE VERPOBS -->
	<xsl:template match="verpobs">
		<xsl:if test="boolean(prefered)">
			<div class="prefered">
				<xsl:value-of select="prefered" />
				.
			</div>
		</xsl:if>
		<xsl:apply-templates select="*[not(name()='prefered')]" />
	</xsl:template>

	<!-- TEMPLATE GENERALS -->
	<xsl:template
		match="htmlform|footer|fbutton|mnu_about|mnu_bguide|order_by">
		<xsl:apply-templates />
	</xsl:template>

</xsl:stylesheet>
