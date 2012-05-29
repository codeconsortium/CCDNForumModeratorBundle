<?php

/*
 * This file is part of the CCDN ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ModeratorBundle\Manager;

use CCDNComponent\CommonBundle\Manager\ManagerInterface;
use CCDNForum\ForumBundle;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class PostManager extends ForumBundle\Manager\PostManager implements ManagerInterface
{


	
	/**
	 *
	 * @access public
	 * @param $post, $user
	 * @return $this
	 */
	public function lock($post, $user)
	{		
		$post->setLockedBy($user);
		$post->setLockedDate(new \DateTime());
		
		$this->persist($post);
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $post
	 * @return $this
	 */
	public function unlock($post)
	{
		$post->setLockedBy(null);
		$post->setLockedDate(null);
				
		$this->persist($post);
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $posts 
	 * @return $this
	 */
	public function bulkLock($posts)
	{
		foreach($posts as $post)
		{
			$post->setLockedBy($this->container->get('security.context')->getToken()->getUser());
			$post->setLockedDate(new \DateTime());
						
			$this->persist($post);
		}
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $posts 
	 * @return $this
	 */
	public function bulkUnlock($posts)
	{
		foreach($posts as $post)
		{
			$post->setLockedBy(null);
			$post->setLockedDate(null);
						
			$this->persist($post);
		}
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $posts 
	 * @return $this
	 */
	public function bulkRestore($posts)
	{
		foreach($posts as $post)
		{
			$post->setDeletedBy(null);
			$post->setDeletedDate(null);
			
			$this->persist($post);
		}
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $posts 
	 * @return $this
	 */
	public function bulkSoftDelete($posts)
	{
		
		$boardsToUpdate = array();
		
		foreach($posts as $post)
		{
			$post->setDeletedBy($this->container->get('security.context')->getToken()->getUser());
			$post->setDeletedDate(new \DateTime());
			
			$this->persist($post);
			
			if ($post->getTopic())
			{
				$topic = $post->getTopic();
				
				if ($topic->getBoard())
				{
					if ( ! array_key_exists($topic->getBoard()->getId(), $boardsToUpdate))
					{
						$boardsToUpdate[$topic->getBoard()->getId()] = $topic->getBoard();
					}
				}
			}
		}
		
		$this->flushNow();
		
		$boardManager = $this->container->get('ccdn_forum_forum.board.manager');
		
		foreach($boardsToUpdate as $board)
		{
			$boardManager->updateBoardStats($board);
		}
				
		$boardManager->flushNow();
				
		return $this;
	}
	
}