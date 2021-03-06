<?xml version="1.0" encoding="utf-8"?>
<!--********************************************************************************
 * CruiseControl, a Continuous Integration Toolkit
 * Copyright (c) 2001, ThoughtWorks, Inc.
 * 200 E. Randolph, 25th Floor
 * Chicago, IL 60601 USA
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     + Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *
 *     + Redistributions in binary form must reproduce the above
 *       copyright notice, this list of conditions and the following
 *       disclaimer in the documentation and/or other materials provided
 *       with the distribution.
 *
 *     + Neither the name of ThoughtWorks, Inc., CruiseControl, nor the
 *       names of its contributors may be used to endorse or promote
 *       products derived from this software without specific prior
 *       written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE REGENTS OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>
    <xsl:param name="viewcvs.url"/>
    <xsl:variable name="project" select="/cruisecontrol/info/property[@name='projectname']/@value"/>
    <xsl:param name="cvsmodule" select="concat($project, '/java/')"/>
    <xsl:key name="source" match="error" use="@source"/>

    <xsl:template match="/">
    <xsl:if test="count(cruisecontrol/padawan/file) = 0">    
        <h3>PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances</h3>

            No <b>PADAWAN</b> Errors or Warnings to display.
        </xsl:if>
        <xsl:apply-templates select="cruisecontrol/padawan"/>
    </xsl:template>

    <xsl:template match="padawan[file/error]">
        <xsl:apply-templates select="." mode="summary"/>
        <xsl:apply-templates select="." mode="check-summary"/>

        <!--<table class="section">-->
        <table class="result" align="center">
          <xsl:for-each select="file[error]">
            <xsl:sort data-type="number" order="descending" select="count(error)"/>
              <xsl:apply-templates select="."/>
          </xsl:for-each>
        </table>
    </xsl:template>

    <xsl:template match="padawan" mode="summary">

<!--            <h3>Padawan
            <span class="label">(Files:
                <xsl:value-of select="count(file[error])"/> 
            | Errors: 
                <xsl:value-of select="count(file/error[@severity='error'])"/>
            | Warnings:
                <xsl:value-of select="count(file/error)"/>
             )</span>
            </h3>-->
<h2>Padawan</h2>
<table class="result" align="center">
  <tr class="oddrow">
    <td>
            ( Files: <xsl:value-of select="count(file[error])"/>
            | Errors: <xsl:value-of select="count(file/error[@severity='error'])"/>
            | Warnings: <xsl:value-of select="count(file/error)"/> )
    </td>
  </tr>
</table>
    </xsl:template>

    <xsl:template match="padawan" mode="check-summary">
        <!--<p/>-->
        <xsl:comment>
      </xsl:comment>
    </xsl:template>

    <xsl:template match="file">
    <xsl:variable name="filename" select="replace(@name, '.*/spool/.*?/', '')" />
        <xsl:variable name="javaclass">

          <xsl:call-template name="javaname">
            <xsl:with-param name="filename" select="$filename"/>
          </xsl:call-template>
        </xsl:variable>
        <tr>
          <td class="fileheader" colspan="3">
            <xsl:value-of select="$filename"/>
            (<xsl:value-of select="count(error[@severity='error'])"/>

            / <xsl:value-of select="count(error)"/>)
          </td>
        </tr>
        <xsl:for-each select="error">
          <tr>
            <xsl:if test="position() mod 2 = 0">
              <xsl:attribute name="class">checkstyle-oddrow-<xsl:value-of select="@severity"/></xsl:attribute>
            </xsl:if>

            <td class="severity-{@severity}"> 
            </td>
            <td class="line {@severity}" align="right">
              <xsl:call-template name="viewcvs">
                <xsl:with-param name="file" select="$filename"/>
                <xsl:with-param name="line" select="@line"/>
              </xsl:call-template>
            </td>
            <td class="checkstyle-{@severity} {@severity}"><xsl:value-of select="@message"/></td>

          </tr>        
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="viewcvs">
      <xsl:param name="file"/>
      <xsl:param name="line"/>
      <xsl:choose>
        <xsl:when test="not($viewcvs.url)">
          <xsl:value-of select="$line"/>

        </xsl:when>
        <xsl:otherwise>
          <a>
            <xsl:attribute name="href">
              <xsl:value-of select="concat($viewcvs.url, $cvsmodule)"/>
              <xsl:value-of select="substring-after($file, $cvsmodule)"/>
              <xsl:text>?annotate=HEAD#</xsl:text>
              <xsl:value-of select="$line"/>

            </xsl:attribute>
            <xsl:value-of select="$line"/>
          </a>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:template>

    <xsl:template name="javaname">
      <xsl:param name="filename"/>

      <xsl:variable name="javafile" select="translate(substring-after($filename, $cvsmodule), '/', '.')"/>
      <xsl:choose>
        <xsl:when test="substring($javafile, string-length($javafile) - 4) = '.java'">
          <xsl:value-of select="substring-before($javafile, '.java')"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$javafile"/>
        </xsl:otherwise>
      </xsl:choose>

    </xsl:template>
</xsl:stylesheet>
