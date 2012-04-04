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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
	 * @param int $page, int $status
	 * @return RedirectResponse|RenderResponse
	 */
	public function showFlaggedAction($page, $status)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$status_codes = $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes();
		
		if (!array_key_exists($status, $status_codes))
		{
			throw new NotFoundHttpException('The status code you are looking up does not exist!');
		}

		$flags_paginated = $this->container->get('ccdn_forum_forum.flag.repository')->findForModeratorsByStatusPaginated($status);
			
		$flags_per_page = $this->container->getParameter('ccdn_forum_moderator.flag.flags_per_page');
		$flags_paginated->setMaxPerPage($flags_per_page);
		$flags_paginated->setCurrentPage($page, false, true);
				
		if (!$flags_paginated) {
			throw new NotFoundHttpException('No flagged posts exist!');
		}	
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('ccdn_component_crumb_trail.crumb_trail')
			->add($this->container->get('translator')->trans('crumbs.flag.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_show_all_flagged_posts'), "home");
				
		return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Flag:show_flagged.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
			'user' => $user,
			'posts' => $flags_paginated,
			'pager' => $flags_paginated,
			'crumbs' => $crumb_trail,
			'reason_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getReasonCodes(),
			'status_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes(),
		));
		
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param int $flag_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function showFlagAction($flag_id)
	{	
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$flag = $this->container->get('ccdn_forum_forum.flag.repository')->find($flag_id);
		
		if ( ! $flag) {
			throw new NotFoundHttpException('No such flag exists!');
		}
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('ccdn_component_crumb_trail.crumb_trail')
			->add($this->container->get('translator')->trans('crumbs.flag.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_show_all_flagged_posts'), "home")
			->add($this->container->get('translator')->trans('crumbs.flag.show', array('%flag_id%' => '#' . $flag->getId()), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_show_flag', array('flag_id' => $flag->getId())), "flag");
				
		return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Flag:show_flag.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
			'user' => $user,
			'flag' => $flag,
			'crumbs' => $crumb_trail,
			'reason_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getReasonCodes(),
			'status_codes' => $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes(),
		));
		
	}
	
	
	
	/**
	 *
	 * Mark the flagged post as Resolved / Unresolved / Defer to
	 *
	 * @access public
	 * @param int $flag_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function markFlagAction($flag_id)
	{		
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$flag = $this->container->get('ccdn_forum_forum.flag.repository')->find($flag_id);
		
		if ( ! $flag) {
			throw new NotFoundHttpException('No such flag exists!');
		}
			
		$formHandler = $this->container->get('ccdn_forum_moderator.flag.form.update.handler')->setDefaultValues(array('flag' => $flag, 'user' => $user));
					
		if ($formHandler->process())
		{
			$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.flag.update.success', array('%flag_id%' => $flag_id), 'CCDNForumModeratorBundle'));
			
			return new RedirectResponse($this->container->get('router')->generate('cc_moderator_forum_show_all_flagged_posts', array() ));
		}
		else
		{
			// setup crumb trail.
			$crumb_trail = $this->container->get('ccdn_component_crumb_trail.crumb_trail')
				->add($this->container->get('translator')->trans('crumbs.flag.index', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_show_all_flagged_posts'), "home")
				->add($this->container->get('translator')->trans('crumbs.flag.show', array('%flag_id%' => '#' . $flag->getId()), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_show_flag', array('flag_id' => $flag->getId())), "flag")
				->add($this->container->get('translator')->trans('crumbs.flag.mark', array(), 'CCDNForumModeratorBundle'), $this->container->get('router')->generate('cc_moderator_forum_mark_flag', array('flag_id' => $flag->getId())), "edit");
			
			return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Flag:flag_mark.html.' . $this->getEngine(), array(
				'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
				'user' => $user,
				'flag' => $flag,
				'post' => $flag->getPost(),
				'crumbs' => $crumb_trail,
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
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		//
		// Get all the flag id's.
		//
		$flagIds = array();
		$ids = $_POST;
		foreach ($ids as $flagKey => $flagId)
		{
			// check_{{ flag.id }}_for_{{ post.getId }}
			if (substr($flagKey, 0, 6) == 'check_')
			{
				//
				// Cast the key values to int upon extraction. 
				//
				preg_match('/^check_([0-9]*)_/', $flagKey, $matches);
				
				if (array_key_exists(0, $matches))
				{
					$id = (int) substr($matches[0], 6, (strlen($matches[0]) -1));

					if (is_int($id) == true)
					{
						$flagIds[] = $id;
					}
				}
			}
		}

		//
		// Don't bother if there are no flags to process.
		//
		if (count($flagIds) < 1)
		{
			return new RedirectResponse($this->container->get('router')->generate('cc_moderator_forum_show_all_flagged_posts'));
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$flags = $this->container->get('ccdn_forum_forum.flag.repository')->findTheseFlagsByIdForModeration($flagIds);

		if ( ! $flags || empty($flags))
		{
			throw new NotFoundHttpException('No flags found!');
		}

		$statusCodes = $this->container->get('ccdn_forum_forum.flag.form.default_choices')->getStatusCodes();
		$statusCode = $flags[0]->getStatus();
		$statusName = array_search($statusCode, $statusCodes);

		if (isset($_POST['submit_delete']))
		{
			$this->container->get('ccdn_forum_forum.flag.manager')->bulkDelete($flags)->flushNow();
		}
		if (isset($_POST['submit_mark_as']))
		{			
			$markAs = (int) $_POST['select_mark_as'];
			
			if (is_int($markAs) == true)
			{
				$this->container->get('ccdn_forum_forum.flag.manager')->bulkMarkAs($flags, $markAs)->flushNow();
			} else {
				$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.flag.bad_status', array(), 'CCDNForumModeratorBundle'));
			}
		}

		return new RedirectResponse($this->container->get('router')->generate('cc_moderator_forum_show_all_flagged_posts_status', array('status' => $statusName)));
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