<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sfm="http://schema.slothsoft.net/farah/module">

	<xsl:import href="farah://slothsoft@farah/xsl/module" />
	<xsl:import href="farah://slothsoft@chat/xsl/form-range" />

	<xsl:template match="/*">
		<div>
			<xsl:apply-templates select="sfm:error" mode="sfm:html" />
			<xsl:for-each select="*[@name='fetch']">
				<form data-chat-id="form" data-chat-last-id="{range/@last-id}" data-chat-database="{range/@db-table}">
					<ul data-chat-id="list" class="messageLog">
						<xsl:apply-templates select="range" />
					</ul>
					<input type="text" data-chat-id="input" autocomplete="off" disabled="disabled" class="myParagraph" />
				</form>
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:stylesheet>