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
     * @param  Int $page
     * @return RenderResponse
     */
    public function showClosedAction($page)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topicsPager = $this->container->get('ccdn_forum_forum.topic.repository')->findClosedTopicsForModeratorsPaginated();

        $topicsPerPage = $this->container->getParameter('ccdn_forum_moderator.topic.show_closed.topics_per_page');
        $topicsPager->setMaxPerPage($topicsPerPage);
        $topicsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.dashboard.moderator', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->container->get('translator')->trans('crumbs.topic.closed.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_topic_show_all_closed'), "home");

        return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:show_closed.html.' . $this->getEngine(), array(
            'crumbs' => $crumbs,
            'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
            'user' => $user,
            'topics' => $topicsPager,
            'pager' => $topicsPager,
        ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse
     */
    public function stickyAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->sticky($topic, $user)->flush();

        $this->container->get('session')->setFlash('success', $this->container->get('translator')->trans('flash.topic.sticky.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse
     */
    public function unstickyAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->unsticky($topic)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.topic.unsticky.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * Once a topic is locked, no posts can be added, deleted or edited!
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse
     */
    public function closeAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->close($topic, $user)->flush();

        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.close.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse
     */
    public function reopenAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->reopen($topic)->flush();

        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.reopen.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse
     */
    public function restoreAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->restore($topic)->flush();

        // set flash message
        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.topic.restore.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        // forward user
        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $topic->getBoard()->getId()) ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RenderResponse
     */
    public function deleteAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such post exists!');
        }

        $board = $topic->getBoard();
        $category = $board->getCategory();

        $crumbDelete = $this->container->get('translator')->trans('crumbs.topic.delete', array(), 'CCDNForumForumBundle');

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), $this->container->get('router')->generate('ccdn_forum_forum_category_index'), "home")
            ->add($category->getName(),	$this->container->get('router')->generate('ccdn_forum_forum_category_show', array('categoryId' => $category->getId())), "category")
            ->add($board->getName(), $this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $board->getId())), "board")
            ->add($topic->getTitle(), $this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId())), "communication")
            ->add($crumbDelete, $this->container->get('router')->generate('ccdn_forum_forum_topic_reply', array('topicId' => $topic->getId())), "trash");

        return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:delete_topic.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
            'topic' => $topic,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_moderator.topic.manager')->softDelete($topic, $user)->flush();

        // set flash message
        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.delete.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

        // forward user
        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $topic->getBoard()->getId()) ));
    }

    /**
     *
     * @access public
     * @param  Int $topicId
     * @return RedirectResponse|RenderResponse
     */
    public function moveAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $topic = $this->container->get('ccdn_forum_forum.topic.repository')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $formHandler = $this->container->get('ccdn_forum_moderator.topic.form.change_board.handler')->setDefaultValues(array('topic' => $topic));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('flash.topic.move.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
        } else {
            $board = $topic->getBoard();
            $category = $board->getCategory();

            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), $this->container->get('router')->generate('ccdn_forum_forum_category_index'), "home")
                ->add($category->getName(), $this->container->get('router')->generate('ccdn_forum_forum_category_show', array('categoryId' => $category->getId())), "category")
                ->add($board->getName(), $this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $board->getId())), "board")
                ->add($topic->getTitle(), $this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId())), "communication")
                ->add($this->container->get('translator')->trans('crumbs.topic.change_board', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_topic_change_board', array('topicId' => $topic->getId())), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:change_board.html.' . $this->getEngine(), array(
                'crumbs' => $crumbs,
                'topic' => $topic,
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
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_topic_show_all_closed'));
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topics = $this->container->get('ccdn_forum_forum.topic.repository')->findTheseTopicsByIdForModeration($itemIds);

        if ( ! $topics || empty($topics)) {
            $this->container->get('session')->setFlash('error', $this->container->get('translator')->trans('flash.topic.no_topics_found', array(), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_topic_show_all_closed'));
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

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_topic_show_all_closed'));
    }

    /**
     *
     * @access protected
     * @return String
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_moderator.template.engine');
    }

}
