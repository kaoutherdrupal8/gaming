<?php

/* modules/contrib/audiofield/templates/audioplayer--audiojs.html.twig */
class __TwigTemplate_9b05416c68a764cd087b3f16def23ee5b3567510b37046135f9cbfa570a7e4fe extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $tags = array("if" => 18, "for" => 35);
        $filters = array();
        $functions = array();

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                array('if', 'for'),
                array(),
                array()
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 15
        echo "<div class=\"audiofield\">
  <div id=\"";
        // line 16
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["settings"] ?? null), "id", array()), "html", null, true));
        echo "\" class=\"audiofield-audiojs-frame\">
    <div class=\"audiofield-audiojs\">
      <audio preload=\"";
        // line 18
        if (($this->getAttribute(($context["settings"] ?? null), "audio_player_lazyload", array()) == 1)) {
            echo "none";
        } else {
            echo "auto";
        }
        echo "\" ";
        if (($this->getAttribute(($context["settings"] ?? null), "audio_player_autoplay", array()) == 1)) {
            echo " autoplay=\"autoplay\" ";
        }
        echo "></audio>
      <div class=\"play-pauseZ\">
        <p class=\"playZ\"></p>
        <p class=\"pauseZ\"></p>
        <p class=\"loadingZ\"></p>
        <p class=\"errorZ\"></p>
      </div>
      <div class=\"scrubberZ\">
        <div class=\"progressZ\"></div>
        <div class=\"loadedZ\"></div>
      </div>
      <div class=\"timeZ\">
        <em class=\"playedZ\">00:00</em>/<strong class=\"durationZ\">00:00</strong>
      </div>
      <div class=\"error-messageZ\"></div>
    </div>
    <ol>
      ";
        // line 35
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["files"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 36
            echo "        <li><a href=\"#\" data-src=\"";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["file"], "url", array()), "html", null, true));
            echo "\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["file"], "description", array()), "html", null, true));
            echo "</a></li>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "    </ol>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/contrib/audiofield/templates/audioplayer--audiojs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 38,  83 => 36,  79 => 35,  51 => 18,  46 => 16,  43 => 15,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/contrib/audiofield/templates/audioplayer--audiojs.html.twig", "C:\\wamp64\\www\\game\\modules\\contrib\\audiofield\\templates\\audioplayer--audiojs.html.twig");
    }
}
