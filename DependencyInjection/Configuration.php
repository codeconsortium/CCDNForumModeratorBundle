<?php

/*
 * This file is part of the CCDN ModeratorBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ModeratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 *
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class Configuration implements ConfigurationInterface
{
	
	
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ccdn_forum_moderator');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
		$rootNode
			->children()
				->arrayNode('user')
					->children()
						->scalarNode('profile_route')->defaultValue('cc_profile_show_by_id')->end()
					->end()
				->end()
				->arrayNode('template')
					->children()
						->scalarNode('engine')->defaultValue('twig')->end()
						->scalarNode('theme')->defaultValue('CCDNForumModeratorBundle:Form:fields.html.twig')->end()
					->end()
				->end()
			->end();
			
		$this->addFlagSection($rootNode);
		$this->addTopicSection($rootNode);
		$this->addPostSection($rootNode);
		$this->addTrashSection($rootNode);
		
        return $treeBuilder;
    }


	/**
	 *
	 * @access private
	 * @param ArrayNodeDefinition $node
	 */
	private function addFlagSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('flag')
					->children()
						->arrayNode('show_flagged')
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
								->scalarNode('flags_per_page')->defaultValue('40')->end()
								->scalarNode('topic_title_truncate')->defaultValue('50')->end()
							->end()
						->end()
						->arrayNode('show_flag')
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
							->end()
						->end()
						->arrayNode('update_flag')
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	

	/**
	 *
	 * @access private
	 * @param ArrayNodeDefinition $node
	 */
	private function addTopicSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('topic')
					->children()						
						->arrayNode('change_board')
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
							->end()
						->end()
						->arrayNode('delete_topic')
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
							->end()
						->end()
						->arrayNode('show_closed')
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
								->scalarNode('topics_per_page')->defaultValue('40')->end()
								->scalarNode('topic_title_truncate')->defaultValue('20')->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	

	/**
	 *
	 * @access private
	 * @param ArrayNodeDefinition $node
	 */
	private function addPostSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('post')
					->children()
						->arrayNode('show_locked')
							->children()
								->scalarNode('posts_per_page')->defaultValue('20')->end()
								->scalarNode('topic_title_truncate')->defaultValue('20')->end()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	

	/**
	 *
	 * @access private
	 * @param ArrayNodeDefinition $node
	 */
	private function addTrashSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
			->end();
	}
	
}
