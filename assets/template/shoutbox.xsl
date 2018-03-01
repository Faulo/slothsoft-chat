<?xml version="1.0" encoding="UTF-8"?><xsl:stylesheet version="1.0"	xmlns="http://www.w3.org/1999/xhtml"	xmlns:svg="http://www.w3.org/2000/svg"	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"	xmlns:sfm="http://schema.slothsoft.net/farah/module"> 	 	<xsl:import href="farah://slothsoft@farah/xsl/module"/>		<xsl:template match="/*">		<xsl:variable name="chatNode" select="*[@name='chat']"/>		<xsl:variable name="chatName" select="$chatNode//@data-chat-database"/>		<fieldset data-chat-id="shoutbox">			<legend data-dict="" data-dict-ns="chat"><xsl:value-of select="concat('chat/title/', $chatName)"/></legend>			<small data-dict="" data-dict-ns="chat"><xsl:value-of select="concat('chat/description/', $chatName)"/></small>			<xsl:copy-of select="$chatNode/node()"/>			<xsl:apply-templates select="sfm:error" mode="sfm:html"/>		</fieldset>	</xsl:template></xsl:stylesheet>