<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes"/>

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

</xsl:stylesheet>
