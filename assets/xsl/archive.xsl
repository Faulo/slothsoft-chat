<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:import href="farah://slothsoft@chat/xsl/form-range" />

	<xsl:template match="*[@name='archive']">
		<div class="chat-archive">
			<ul class="messageLog">
				<xsl:apply-templates select="range" />
			</ul>
		</div>
	</xsl:template>
</xsl:stylesheet>