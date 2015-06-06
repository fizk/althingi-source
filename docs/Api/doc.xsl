<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/">
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="doc.css" />
            </head>
            <body>
                <header role="header">
                    <h1>API documentation</h1>
                </header>
                <nav role="navigation">
                    <ul>
                        <xsl:for-each select="/root/resource">
                            <li><a href="#{path}"><xsl:value-of select="path"/></a></li>
                        </xsl:for-each>
                    </ul>
                </nav>
                <main role="main">

                <xsl:for-each select="/root/resource">
                <article id="{path}">
                    <header>
                        <h2><xsl:value-of select="path"/></h2>
                    </header>
                    <xsl:if test="param">
                    <aside>
                        <table>
                            <caption>Parameters</caption>
                            <thead>
                                <tr>
                                    <td>name</td>
                                    <td>value</td>
                                    <td>description</td>
                                </tr>
                            </thead>
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
                    <section class="{@verb}">
                        <h3><xsl:value-of select="@verb"/></h3>
                        <p><xsl:value-of select="description"/></p>
                        <h4>Request:</h4>
                        <xsl:if test="request/headers">
                        <table>
                            <caption>HTTP Headers</caption>
                            <thead>
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
                        <xsl:if test="request/input">
                        <table>
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
                        <table>
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
                        <xsl:if test="response/output">
                        <h5>JSON example</h5>
                        <pre><xsl:value-of select="response/output"/></pre>
                        </xsl:if>
                    </section>
                    </xsl:for-each>
                    <footer></footer>
                </article>
                </xsl:for-each>
                </main>
                <footer></footer>
            </body>
        </html>
    </xsl:template>


</xsl:stylesheet>
