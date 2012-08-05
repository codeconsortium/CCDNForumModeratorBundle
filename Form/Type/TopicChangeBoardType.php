<?php

/*
 * This file is part of the CCDNForum ModeratorBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ModeratorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class TopicChangeBoardType extends AbstractType
{

    /**
     *
     * @access protected
     */
    protected $defaults = array();

    /**
     *
     * @access public
     * @param Array() $options
     */
    public function setDefaultValues(array $defaults = null)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     *
     * @access public
     * @param FormBuilder $builder, Array() $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('board', 'entity', array(
            'class' => 'CCDNForumForumBundle:Board',
            'query_builder' => function($repository) { return $repository->createQueryBuilder('b')->orderBy('b.id', 'ASC'); },
            'property' => 'name',
            'preferred_choices' => array($this->defaults['board']),
        ));
    }

    /**
     *
     * for creating and replying to topics
     *
     * @access public
     * @param Array() $options
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'CCDNForum\ForumBundle\Entity\Topic',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'topic_change_board',
        );
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'TopicChangeBoard';
    }

}
