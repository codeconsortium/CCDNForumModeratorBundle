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

namespace CCDNForum\ModeratorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

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
    public function getAlias()
    {
        return 'ccdn_forum_moderator';
    }

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
        $container->setParameter('ccdn_forum_moderator.user.profile_route', $config['user']['profile_route']);

        $this->getSEOSection($container, $config);
        $this->getFlagSection($container, $config);
        $this->getItemFlagSection($container, $config);
        $this->getTopicSection($container, $config);
        $this->getPostSection($container, $config);

    }

    /**
     *
     * @access protected
     * @param $container, $config
     */
    protected function getSEOSection($container, $config)
    {
        $container->setParameter('ccdn_forum_moderator.seo.title_length', $config['seo']['title_length']);
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
        $container->setParameter('ccdn_forum_moderator.flag.show_flagged.flag_created_datetime_format', $config['flag']['show_flagged']['flag_created_datetime_format']);
        $container->setParameter('ccdn_forum_moderator.flag.show_flagged.post_created_datetime_format', $config['flag']['show_flagged']['post_created_datetime_format']);

        $container->setParameter('ccdn_forum_moderator.flag.show_flag.layout_template', $config['flag']['show_flag']['layout_template']);

        $container->setParameter('ccdn_forum_moderator.flag.update_flag.layout_template', $config['flag']['update_flag']['layout_template']);
        $container->setParameter('ccdn_forum_moderator.flag.update_flag.form_theme', $config['flag']['update_flag']['form_theme']);

    }

    /**
     *
     * @access private
     * @param $container, $config
     */
    private function getItemFlagSection($container, $config)
    {
        $container->setParameter('ccdn_forum_moderator.item_flag.flag_created_datetime_format', $config['item_flag']['flag_created_datetime_format']);
        $container->setParameter('ccdn_forum_moderator.item_flag.moderated_datetime_format', $config['item_flag']['moderated_datetime_format']);
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
        $container->setParameter('ccdn_forum_moderator.topic.show_closed.post_created_datetime_format', $config['topic']['show_closed']['post_created_datetime_format']);
        $container->setParameter('ccdn_forum_moderator.topic.show_closed.topic_closed_datetime_format', $config['topic']['show_closed']['topic_closed_datetime_format']);
        $container->setParameter('ccdn_forum_moderator.topic.show_closed.topic_deleted_datetime_format', $config['topic']['show_closed']['topic_deleted_datetime_format']);

        $container->setParameter('ccdn_forum_moderator.topic.delete_topic.layout_template', $config['topic']['delete_topic']['layout_template']);

        $container->setParameter('ccdn_forum_moderator.topic.change_board.layout_template', $config['topic']['change_board']['layout_template']);
        $container->setParameter('ccdn_forum_moderator.topic.change_board.form_theme', $config['topic']['change_board']['form_theme']);

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
        $container->setParameter('ccdn_forum_moderator.post.show_locked.post_created_datetime_format', $config['post']['show_locked']['post_created_datetime_format']);
        $container->setParameter('ccdn_forum_moderator.post.show_locked.post_locked_datetime_format', $config['post']['show_locked']['post_locked_datetime_format']);
        $container->setParameter('ccdn_forum_moderator.post.show_locked.post_deleted_datetime_format', $config['post']['show_locked']['post_deleted_datetime_format']);

    }

}
