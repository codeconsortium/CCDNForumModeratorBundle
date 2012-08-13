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
class FlagController extends ContainerAware
{

    /**
     *
     * Displays flagged messages
     *
     * @access public
     * @param  Int $page, Int $status
     * @return RedirectResponse|RenderResponse
     */
    public function showFlaggedAction($page, $status)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $statusCodes = $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes();

        if ( ! array_key_exists($status, $statusCodes)) {
            $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('ccdn_forum_moderator.flash.flag.no_flags_found', array(), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'));
        }

        $flagsPager = $this->container->get('ccdn_forum_forum.flag.repository')->findForModeratorsByStatusPaginated($status);

        $flagsPerPage = $this->container->getParameter('ccdn_forum_moderator.flag.show_flagged.flags_per_page');
        $flagsPager->setMaxPerPage($flagsPerPage);
        $flagsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.dashboard.moderator', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.flag.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'), "home");

        return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Flag:show_flagged.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
            'user' => $user,
            'posts' => $flagsPager,
            'pager' => $flagsPager,
            'crumbs' => $crumbs,
            'reason_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getReasonCodes(),
            'status_codes' => $statusCodes,
        ));

    }

    /**
     *
     * @access public
     * @param  Int $flagId
     * @return RedirectResponse|RenderResponse
     */
    public function showFlagAction($flagId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $flag = $this->container->get('ccdn_forum_forum.flag.repository')->find($flagId);

        if (! $flag) {
            $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('ccdn_forum_moderator.flash.flag.no_flags_found', array(), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'));
        }

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.dashboard.moderator', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.flag.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'), "home")
            ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.flag.show', array('%flag_id%' => '#' . $flag->getId()), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_flag_show', array('flagId' => $flag->getId())), "flag");

        return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Flag:show_flag.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
            'user' => $user,
            'flag' => $flag,
            'crumbs' => $crumbs,
            'reason_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getReasonCodes(),
            'status_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes(),
        ));

    }

    /**
     *
     * Mark the flagged post as Resolved / Unresolved / Defer to
     *
     * @access public
     * @param  Int $flagId
     * @return RedirectResponse|RenderResponse
     */
    public function updateFlagAction($flagId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $flag = $this->container->get('ccdn_forum_forum.flag.repository')->find($flagId);

        if (! $flag) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_moderator.flash.flag.no_flags_found', array(), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'));
        }

        $formHandler = $this->container->get('ccdn_forum_moderator.flag.form.update.handler')->setDefaultValues(array('flag' => $flag, 'user' => $user));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('success', $this->container->get('translator')->trans('ccdn_forum_moderator.flash.flag.update.success', array('%flag_id%' => $flagId), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flag_show', array('flagId' => $flagId) ));
        } else {
            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.dashboard.moderator', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
                ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.flag.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'), "home")
                ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.flag.show', array('%flag_id%' => '#' . $flag->getId()), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_flag_show', array('flagId' => $flag->getId())), "flag")
                ->add($this->container->get('translator')->trans('ccdn_forum_moderator.crumbs.flag.mark', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('ccdn_forum_moderator_update_flag', array('flagId' => $flag->getId())), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Flag:update_flag.html.' . $this->getEngine(), array(
                'crumbs' => $crumbs,
                'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
                'user' => $user,
                'flag' => $flag,
                'post' => $flag->getPost(),
                'form' => $formHandler->getForm()->createView(),
                'reason_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getReasonCodes(),
                'status_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes(),
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
        // Get all the flag id's.
        //
        $flagIds = array();
        $ids = $_POST;
        foreach ($ids as $flagKey => $flagId) {
            // check_{{ flag.id }}_for_{{ post.getId }}
            if (substr($flagKey, 0, 6) == 'check_') {
                //
                // Cast the key values to int upon extraction.
                //
                preg_match('/^check_([0-9]*)_/', $flagKey, $matches);

                if (array_key_exists(0, $matches)) {
                    $id = (int) substr($matches[0], 6, (strlen($matches[0]) -1));

                    if (is_int($id) == true) {
                        $flagIds[] = $id;
                    }
                }
            }
        }

        //
        // Don't bother if there are no flags to process.
        //
        if (count($flagIds) < 1) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'));
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $flags = $this->container->get('ccdn_forum_forum.flag.repository')->findTheseFlagsByIdForModeration($flagIds);

        if ( ! $flags || empty($flags)) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_moderator.flash.flag.no_flags_found', array(), 'CCDNForumModeratorBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all'));
        }

        $statusCodes = $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes();
        $statusCode = $flags[0]->getStatus();
        $statusName = array_search($statusCode, $statusCodes);

        if (isset($_POST['submit_delete'])) {
            $this->container->get('ccdn_forum_moderator.flag.manager')->bulkDelete($flags)->flush();
        }
        if (isset($_POST['submit_mark_as'])) {
            $markAs = (int) $_POST['select_mark_as'];

            if (is_int($markAs) == true) {
                $this->container->get('ccdn_forum_moderator.flag.manager')->bulkMarkAs($flags, $markAs)->flush();
            } else {
                $this->container->get('session')->setFlash('error', $this->container->get('translator')->trans('ccdn_forum_moderator.flash.flag.bad_status', array(), 'CCDNForumModeratorBundle'));
            }
        }

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_moderator_flagged_show_all_status', array('status' => $statusName)));
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
