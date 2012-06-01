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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class CCDNForumModeratorExtension extends Extension
{
	
	
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


		$container->setParameter('ccdn_forum_moderator.template.engine', $config['template']['engine']);
		$container->setParameter('ccdn_forum_moderator.template.theme', $config['template']['theme']);
		$container->setParameter('ccdn_forum_moderator.user.profile_route', $config['user']['profile_route']);
		
		$this->getFlagSection($container, $config);
		$this->getTopicSection($container, $config);
		$this->getPostSection($container, $config);
		$this->getTrashSection($container, $config);
		
    }
	
	
    /**
     * {@inheritDoc}
     */
	public function getAlias()
	{
		return 'ccdn_forum_moderator';
	}
	
	

	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getFlagSection($container, $config)
	{
		$container->setParameter('ccdn_forum_moderator.flag.show_flagged.layout_template', $config['flag']['show_flagged']['layout_template']);
		$container->setParameter('ccdn_forum_moderator.flag.show_flagged.topic_title_truncate', $config['flag']['show_flagged']['topic_title_truncate']);
		$container->setParameter('ccdn_forum_moderator.flag.show_flagged.flags_per_page', $config['flag']['show_flagged']['flags_per_page']);

		$container->setParameter('ccdn_forum_moderator.flag.update_flag.layout_template', $config['flag']['update_flag']['layout_template']);
		
		$container->setParameter('ccdn_forum_moderator.flag.show_flag.layout_template', $config['flag']['show_flag']['layout_template']);	
	}
	

	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getTopicSection($container, $config)
	{
		$container->setParameter('ccdn_forum_moderator.topic.show_closed.layout_template', $config['topic']['show_closed']['layout_template']);
		$container->setParameter('ccdn_forum_moderator.topic.show_closed.topics_per_page', $config['topic']['show_closed']['topics_per_page']);
		$container->setParameter('ccdn_forum_moderator.topic.show_closed.topic_title_truncate', $config['topic']['show_closed']['topic_title_truncate']);

		$container->setParameter('ccdn_forum_moderator.topic.delete_topic.layout_template', $config['topic']['delete_topic']['layout_template']);	
		
		$container->setParameter('ccdn_forum_moderator.topic.change_board.layout_template', $config['topic']['change_board']['layout_template']);	
	}
	

	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getPostSection($container, $config)
	{
		$container->setParameter('ccdn_forum_moderator.post.show_locked.layout_template', $config['post']['show_locked']['layout_template']);
		
		$container->setParameter('ccdn_forum_moderator.post.show_locked.posts_per_page', $config['post']['show_locked']['posts_per_page']);
		
		$container->setParameter('ccdn_forum_moderator.post.show_locked.topic_title_truncate', $config['post']['show_locked']['topic_title_truncate']);
	}
	

	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getTrashSection($container, $config)
	{
		
	}
	
}
