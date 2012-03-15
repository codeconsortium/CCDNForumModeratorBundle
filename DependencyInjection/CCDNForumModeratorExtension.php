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
		$container->setParameter('ccdn_forum_moderator.flag.flags_per_page', $config['flag']['flags_per_page']);
		
		$container->setParameter('ccdn_forum_moderator.flag.layout_templates.flag_mark', $config['flag']['layout_templates']['flag_mark']);
		$container->setParameter('ccdn_forum_moderator.flag.layout_templates.show_flag', $config['flag']['layout_templates']['show_flag']);
		$container->setParameter('ccdn_forum_moderator.flag.layout_templates.show_flagged', $config['flag']['layout_templates']['show_flagged']);
		
	}
	

	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getTopicSection($container, $config)
	{
		$container->setParameter('ccdn_forum_moderator.topic.topics_per_page', $config['topic']['topics_per_page']);
		$container->setParameter('ccdn_forum_moderator.topic.posts_per_page', $config['topic']['posts_per_page']);
		
		$container->setParameter('ccdn_forum_moderator.topic.layout_templates.change_board', $config['topic']['layout_templates']['change_board']);
		$container->setParameter('ccdn_forum_moderator.topic.layout_templates.delete_topic', $config['topic']['layout_templates']['delete_topic']);
		$container->setParameter('ccdn_forum_moderator.topic.layout_templates.show_closed', $config['topic']['layout_templates']['show_closed']);
		
	}
	

	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getPostSection($container, $config)
	{
		$container->setParameter('ccdn_forum_moderator.post.posts_per_page', $config['post']['posts_per_page']);
		
		$container->setParameter('ccdn_forum_moderator.post.layout_templates.show_locked', $config['post']['layout_templates']['show_locked']);
		
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
