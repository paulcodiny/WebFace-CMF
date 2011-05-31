<?php

/* wf_default_index.html */
class __TwigTemplate_1636fdbfb92e859193a3b687c86ceeca extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = (isset($context['base_layout']) ? $context['base_layout'] : null);
            if (!$this->parent instanceof Twig_Template) {
                $this->parent = $this->env->loadTemplate($this->parent);
            }
        }

        return $this->parent;
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "\t";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context['widgets']) ? $context['widgets'] : null));
        foreach ($context['_seq'] as $context['_key'] => $context['widget']) {
            // line 5
            echo "\t\t";
            echo twig_escape_filter($this->env, (isset($context['widget']) ? $context['widget'] : null), "html");
            echo "
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['widget'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
    }

    public function getTemplateName()
    {
        return "wf_default_index.html";
    }

    public function isTraitable()
    {
        return false;
    }
}
