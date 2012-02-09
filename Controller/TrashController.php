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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class TrashController extends ContainerAware
{

	
	/**
	 *
	 * Displays a list of soft deleted messages / topics, but only Admin can remove via admin bundle
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function showTrashAction()
	{
		
	}


	/**
	 *
	 * Restores items, marked for removal from db via checkboxes for each item
	 * present in a form. This can only be done via a member with role_admin!
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function restoreItemsAction()
	{
		
	}
	
	
	/**
	 *
	 * Deletes items, marked for removal from db via checkboxes for each item
	 * present in a form. This can only be done via a member with role_admin!
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function deleteItemsAction()
	{
		
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
