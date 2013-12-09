<?php

namespace Core\Twig;


class TwitterBootstrapExtension extends \Twig_Extension{


    public function getName()
    {
        return 'twitterbootstrap.extension';
    }

    /** @var string */
    private $style;

    /** @var string */
    private $colSize = 'lg';

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('bootstrap_set_style', array($this, 'setStyle')),
            new \Twig_SimpleFunction('bootstrap_get_style', array($this, 'getStyle')),
            new \Twig_SimpleFunction('bootstrap_set_col_size', array($this, 'setColSize')),
            new \Twig_SimpleFunction('bootstrap_get_col_size', array($this, 'getColSize')),
            'checkbox_row'  => new \Twig_Function_Node(
                    'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode',
                    array('is_safe' => array('html'))
                ),
            'radio_row'  => new \Twig_Function_Node(
                    'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode',
                    array('is_safe' => array('html'))
                ),
            'global_form_errors'  => new \Twig_Function_Node(
                    'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode',
                    array('is_safe' => array('html'))
                )
        );
    }

    /**
     * Sets the style.
     *
     * @param string $style Name of the style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * Returns the style.
     *
     * @return string Name of the style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Sets the column size.
     *
     * @param string $colSize Column size (xs, sm, md or lg)
     */
    public function setColSize($colSize)
    {
        $this->colSize = $colSize;
    }

    /**
     * Returns the column size.
     *
     * @return string Column size (xs, sm, md or lg)
     */
    public function getColSize()
    {
        return $this->colSize;
    }
}