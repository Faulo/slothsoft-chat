<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sfm="http://schema.slothsoft.net/farah/module">

	<xsl:import href="farah://slothsoft@farah/xsl/module" />

	<xsl:template match="/*">
		<div>
			<xsl:apply-templates select="sfm:error" mode="sfm:html" />
			<xsl:for-each select="*[@name='fetch']">
				<form data-chat-id="form" data-chat-last-id="{range/@last-id}" data-chat-database="{range/@db-table}">
					<ul data-chat-id="list" class="messageLog">
						<xsl:apply-templates select="range/p" />
					</ul>
					<input type="text" data-chat-id="input" autocomplete="off" disabled="disabled" class="myParagraph" />
				</form>
			</xsl:for-each>
		</div>
	</xsl:template>

	<xsl:template match="range" priority="1">
		<xsl:apply-templates select="p" />
	</xsl:template>

	<xsl:template match="p">
		<li style="color:rgb({@color});" data-client="{@client}">
			<time datetime="{@time-utc}">
				<xsl:value-of select="@time-string" />
			</time>
			<span>
				<xsl:copy-of select="node()" />
			</span>
		</li>
	</xsl:template>
</xsl:stylesheet>