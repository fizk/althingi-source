<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <nav role="navigation">
            <ul>
                <xsl:for-each select="/root/resource">
                    <li><a href="#{path}"><xsl:value-of select="path"/></a></li>
                </xsl:for-each>
            </ul>
        </nav>

        <xsl:for-each select="/root/resource">
        <article id="{path}" class="docs">
            <header class="docs__header">
                <h2><xsl:value-of select="path"/></h2>
            </header>
            <xsl:if test="param">
            <aside>
                <table class="docs__parameters">
                    <tbody>
                        <xsl:for-each select="param">
                            <tr>
                                <th><xsl:value-of select="@name"/></th>
                                <td><xsl:value-of select="@value"/></td>
                                <td><xsl:value-of select="."/></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </aside>
            </xsl:if>
            <xsl:for-each select="method">
            <section class="docs__method docs__method--{@verb}">
                <h3><xsl:value-of select="@verb"/></h3>
                <p><xsl:value-of select="description"/></p>
                <h4>Request:</h4>
                <xsl:if test="request/headers">
                <table class="docs__table">
                    <caption  class="docs__table-description">HTTP Headers</caption>
                    <thead class="docs__table-header">
                        <tr>
                            <td>name</td>
                            <td>value</td>
                            <td>description</td>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="request/headers/param">
                        <tr>
                            <th><xsl:value-of select="@name"/></th>
                            <td><xsl:value-of select="@value"/></td>
                            <td><xsl:value-of select="."/></td>
                        </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
                </xsl:if>
                <xsl:if test="request/query">
                <table class="docs__table">
                    <caption>Query Params</caption>
                    <thead>
                        <tr>
                            <td>name</td>
                            <td>value</td>
                            <td>description</td>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="request/query/param">
                            <tr>
                                <th><xsl:value-of select="@name"/></th>
                                <td><xsl:value-of select="@value"/></td>
                                <td><xsl:value-of select="."/></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
                </xsl:if>
                <xsl:if test="request/input">
                <table class="docs__table">
                    <caption>Input values</caption>
                    <thead>
                        <tr>
                            <td>name</td>
                            <td>value</td>
                            <td>description</td>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="request/input/param">
                            <tr>
                                <th><xsl:value-of select="@name"/></th>
                                <td><xsl:value-of select="@value"/></td>
                                <td><xsl:value-of select="."/></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
                </xsl:if>
                <h4>Response:</h4>
                <xsl:if test="response/headers">
                <table class="docs__table">
                    <caption>HTTP Headers</caption>
                    <thead>
                        <tr>
                            <td>name</td>
                            <td>value</td>
                            <td>description</td>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="response/headers/param">
                            <tr>
                                <th><xsl:value-of select="@name"/></th>
                                <td><xsl:value-of select="@value"/></td>
                                <td><xsl:value-of select="."/></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
                </xsl:if>
                <xsl:if test="response/status">
                    <table class="docs__table">
                        <caption>Notable response codes</caption>
                        <thead>
                            <tr>
                                <td>code</td>
                                <td>description</td>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="response/status/code">
                            <tr>
                                <th><xsl:value-of select="@value"/></th>
                                <td><xsl:value-of select="."/></td>
                            </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>

                </xsl:if>
                <xsl:if test="response/output">
                <pre><xsl:value-of select="response/output"/></pre>
                </xsl:if>
            </section>
            </xsl:for-each>

        </article>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
