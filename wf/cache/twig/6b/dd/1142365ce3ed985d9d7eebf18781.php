<?php

/* site_layout.html */
class __TwigTemplate_6bdd1142365ce3ed985d9d7eebf18781 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"ru\">

<head>
\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
\t<meta http-equiv=\"Pragma\" content=\"no-cache\" />
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=8\" />

\t<title>Web2Face</title>

\t<meta name=\"author\" content=\"\" />
\t<meta name=\"robots\" content=\"index,follow\" />
</head>

<body>
\t<div id=\"wrap\">
\t\t<div id=\"header\">
\t\t\t
\t\t</div>
\t\t<div id=\"content\">
\t\t\t";
        // line 21
        $this->displayBlock('content', $context, $blocks);
        // line 24
        echo "\t\t</div><!-- #content -->
\t\t\t
\t\t<div id=\"footer\">

\t\t</div><!-- #footer -->
\t</div><!-- #wrap -->
</body>
</html>";
    }

    // line 21
    public function block_content($context, array $blocks = array())
    {
        // line 22
        echo "
\t\t\t";
    }

    public function getTemplateName()
    {
        return "site_layout.html";
    }

    public function isTraitable()
    {
        return false;
    }
}
