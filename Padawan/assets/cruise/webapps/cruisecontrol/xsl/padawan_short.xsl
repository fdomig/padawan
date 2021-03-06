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

    <!-- Controls whether all Padawan errors and warnings should be listed.
         Set to 'true' for hiding the warnings -->
    <xsl:param name="padawan.hide.warnings"/>

    <xsl:template match="/" mode="padawan">
        <xsl:apply-templates select="cruisecontrol/padawan" mode="padawan"/>
    </xsl:template>

    <xsl:template match="padawan[file/error]" mode="padawan">
        <table class="result" align="center">
        <xsl:apply-templates select="." mode="summary"/>
          <tbody>
          <xsl:for-each select="file[error]">
            <xsl:sort data-type="number" order="descending" select="count(error)"/>
              <xsl:apply-templates select="." mode="padawanSummary"/>
          </xsl:for-each>
          </tbody>
        </table>

    </xsl:template>

    <xsl:template match="padawan" mode="summary">
        <thead>
          <tr>
            <th colspan="2">Padawan
            ( Files: <xsl:value-of select="count(file[error])"/> 
            | Errors: <xsl:value-of select="count(file/error[@severity='error'])"/>
            | Warnings: <xsl:value-of select="count(file/error)"/> )
            </th>
            <th>Errors</th>
            <th>Warnings</th>
          </tr>
        </thead>
    </xsl:template>

    <xsl:template match="file" mode="padawanSummary">
        <xsl:variable name="filename" select="replace(@name, '.*/spool/.*?/', '')" />
        <tr>
            <td></td>
            <td>
                <a class="php-file">
                <xsl:value-of select="$filename" />
                </a>
            </td>
            <td align="center">
                <xsl:value-of select="count(error[@severity='error'])" />
            </td>
            <td align="center">
                <xsl:value-of select="count(error)" />
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
