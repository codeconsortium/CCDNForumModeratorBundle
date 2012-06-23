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
		// Don't overwite previous users accountability.
		if ( ! $post->getLockedBy() && ! $post->getLockedDate())
		{
			$post->setIsLocked(true);
			$post->setLockedBy($user);
			$post->setLockedDate(new \DateTime());
		
			$this->persist($post);
		}
		
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
		$post->setIsLocked(false);
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
	public function bulkLock($posts, $user)
	{
		foreach($posts as $post)
		{
			// Don't overwite previous users accountability.
			if ( ! $post->getLockedBy() && ! $post->getLockedDate())
			{
				$post->setIsLocked(true);
				$post->setLockedBy($user);
				$post->setLockedDate(new \DateTime());
			}
			
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
			$post->setIsLocked(false);
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
	public function bulkSoftDelete($posts, $user)
	{	
		$boards_to_update = array();
		$topic_to_delete = array();
		
		foreach($posts as $post)
		{
			// Don't overwite previous users accountability.
			if ( ! $post->getDeletedBy() && ! $post->getDeletedDate())
			{
				// Add the board of the topic to be updated.
				if ($post->getTopic())
				{			
					$topic = $post->getTopic();
					
					if ($topic->getBoard())
					{
						if ( ! array_key_exists($topic->getBoard()->getId(), $boards_to_update))
						{
							$boards_to_update[$topic->getBoard()->getId()] = $topic->getBoard();
						}
					}
					
					if ($topic->getReplyCount() < 1 && $topic->getFirstPost()->getId() == $post->getId())
					{
						$topic->setIsDeleted(true);
						$topic->setDeletedBy($user);
						$topic->setDeletedDate(new \DateTime());
						
						$this->persist($topic);
					}
				}
				
				$post->setIsDeleted(true);
				$post->setDeletedBy($user);
				$post->setDeletedDate(new \DateTime());

				$this->persist($post);
			}		
		}
		
		$this->flushNow();
		
		if (count($boards_to_update) > 0)
		{
			// Update all affected board stats.
			$this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boards_to_update)->flushNow();
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
		$boards_to_update = array();
		
		foreach($posts as $post)
		{
			// Add the board of the topic to be updated.
			if ($post->getTopic())
			{
				$topic = $post->getTopic();
						
				if ($topic->getBoard())
				{
					if ( ! array_key_exists($topic->getBoard()->getId(), $boards_to_update))
					{
						$boards_to_update[$topic->getBoard()->getId()] = $topic->getBoard();
					}
				}
				
				if ($topic->getReplyCount() < 1 && $topic->getFirstPost()->getId() == $post->getId())
				{
					$topic->setIsDeleted(false);
					$topic->setDeletedBy(null);
					$topic->setDeletedDate(null);
					
					$this->persist($topic);
				}
			}

			$post->setIsDeleted(false);
			$post->setDeletedBy(null);
			$post->setDeletedDate(null);
		
			$this->persist($post);
		}
		
		$this->flushNow();
		
		if (count($boards_to_update) > 0)
		{
			// Update all affected board stats.
			$this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boards_to_update)->flushNow();
		}
		
		return $this;		
	}
	
}