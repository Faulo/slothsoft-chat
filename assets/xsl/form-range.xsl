<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sfm="http://schema.slothsoft.net/farah/module">

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