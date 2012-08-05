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

namespace CCDNForum\ModeratorBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class TopicController extends ContainerAware
{

    /**
     *
     * Displays a list of closed topics (locked from posting new posts)
     *
     * @access public
     * @param  int                             $page
     * @return RedirectResponse|RenderResponse
     */
    public function showClosedAction($page)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topics_paginated = $this->container->get('ccdn_forum_forum.topic.repository')->findClosedTopicsForModeratorsPaginated();

        $topics_per_page = $this->container->getParameter('ccdn_forum_moderator.topic.show_closed.topics_per_page');
        $topics_paginated->setMaxPerPage($topics_per_page);
        $topics_paginated->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumb_trail = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.dashboard.moderator', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->container->get('translator')->trans('crumbs.topic.closed.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_topics_closed_show_all'), "home");

        return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:show_closed.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
            'user' => $user,
            'topics' => $topics_paginated,
            'crumbs' => $crumb_trail,
            'pager' => $topics_paginated,
        ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function stickyAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->sticky($topic, $user)->flush();

        $this->container->get('session')->setFlash('success', $this->container->get('translator')->trans('flash.topic.sticky.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function unstickyAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->unsticky($topic)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.topic.unsticky.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId()) ));
    }

    /**
     *
     * Once a topic is locked, no posts can be added, deleted or edited!
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function closeAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->close($topic, $user)->flush();

        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.close.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function reopenAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->reopen($topic)->flush();

        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.reopen.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function restoreAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->restore($topic)->flush();

        // set flash message
        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.topic.restore.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        // forward user
        return new RedirectResponse($this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $topic->getBoard()->getId()) ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function deleteAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such post exists!');
        }

        $board = $topic->getBoard();
        $category = $board->getCategory();

//		$confirmationMessage = 'topic.delete.question';
        $crumbDelete = $this->container->get('translator')->trans('crumbs.topic.delete', array(), 'CCDNForumForumBundle');
//		$pageTitle = $this->container->get('translator')->trans('title.topic.delete', array('%topic_title%' => $topic->getTitle()), 'CCDNForumForumBundle');

        // setup crumb trail.
        $crumb_trail = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), $this->container->get('router')->generate('cc_forum_category_index'), "home")
            ->add($category->getName(),	$this->container->get('router')->generate('cc_forum_category_show', array('category_id' => $category->getId())), "category")
            ->add($board->getName(), $this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $board->getId())), "board")
            ->add($topic->getTitle(), $this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId())), "communication")
            ->add($crumbDelete, $this->container->get('router')->generate('cc_forum_topic_reply', array('topic_id' => $topic->getId())), "trash");

        return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:delete_topic.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
//			'page_title' => $pageTitle,
//			'confirmation_message' => $confirmationMessage,
            'topic' => $topic,
            'crumbs' => $crumb_trail,
        ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function deleteConfirmedAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->softDelete($topic, $user)->flush();

        // set flash message
        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.delete.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        // forward user
        return new RedirectResponse($this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $topic->getBoard()->getId()) ));
    }

    /**
     *
     * @access public
     * @param  int                             $topic_id
     * @return RedirectResponse|RenderResponse
     */
    public function moveAction($topic_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topic_id);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $formHandler = $this->container->get('ccdn_forum_moderator.topic.form.change_board.handler')->setDefaultValues(array('topic' => $topic));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.move.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId()) ));
        } else {
            $board = $topic->getBoard();
            $category = $board->getCategory();

            // setup crumb trail.
            $crumb_trail = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), $this->container->get('router')->generate('cc_forum_category_index'), "home")
                ->add($category->getName(), $this->container->get('router')->generate('cc_forum_category_show', array('category_id' => $category->getId())), "category")
                ->add($board->getName(), $this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $board->getId())), "board")
                ->add($topic->getTitle(), $this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId())), "communication")
                ->add($this->container->get('translator')->trans('crumbs.topic.change_board', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_topic_change_board', array('topic_id' => $topic->getId())), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:change_board.html.' . $this->getEngine(), array(
                'topic' => $topic,
                'crumbs' => $crumb_trail,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @return RedirectResponse
     */
    public function bulkAction()
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        //
        // Get all the checked item id's.
        //
        $itemIds = array();
        $ids = $_POST;
        foreach ($ids as $itemKey => $itemId) {
            if (substr($itemKey, 0, 6) == 'check_') {
                //
                // Cast the key values to int upon extraction.
                //
                $id = (int) substr($itemKey, 6, (strlen($itemKey) - 6));

                if (is_int($id) == true) {
                    $itemIds[] = $id;
                }
            }
        }

        //
        // Don't bother if there are no flags to process.
        //
        if (count($itemIds) < 1) {
            return new RedirectResponse($this->container->get('router')->generate('cc_moderator_forum_topics_closed_show_all'));
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topics = $this->container->get('ccdn_forum_forum.topic.repository')->findTheseTopicsByIdForModeration($itemIds);

        if ( ! $topics || empty($topics)) {
            $this->container->get('session')->setFlash('error', $this->container->get('translator')->trans('flash.topic.no_topics_found', array(), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('cc_moderator_forum_topics_closed_show_all'));
        }

        if (isset($_POST['submit_close'])) {
            $this->container->get('ccdn_forum_moderator.topic.manager')->bulkClose($topics, $user)->flush();
        }
        if (isset($_POST['submit_reopen'])) {
            $this->container->get('ccdn_forum_moderator.topic.manager')->bulkReopen($topics)->flush();
        }
        if (isset($_POST['submit_restore'])) {
            $this->container->get('ccdn_forum_moderator.topic.manager')->bulkRestore($topics)->flush();
        }
        if (isset($_POST['submit_soft_delete'])) {
            $this->container->get('ccdn_forum_moderator.topic.manager')->bulkSoftDelete($topics, $user)->flush();
        }

        return new RedirectResponse($this->container->get('router')->generate('cc_moderator_forum_topics_closed_show_all'));
    }

    /**
     *
     * @access protected
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_moderator.template.engine');
    }

}
