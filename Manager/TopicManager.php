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
class TopicManager extends ForumBundle\Manager\TopicManager implements ManagerInterface
{
	
	
	
	/**
	 *
	 * @access public
	 * @param $topic
	 * @return $this
	 */
	public function sticky($topic)
	{
		$topic->setIsSticky(true);
		
		$this->persist($topic);
		
		return $this;
	}
	

	
	/**
	 *
	 * @access public
	 * @param $topic
	 * @return $this
	 */
	public function unsticky($topic)
	{
		$topic->setIsSticky(false);
		
		$this->persist($topic);
		
		return $this;
	}


	
	/**
	 *
	 * @access public
	 * @param $topic, $user
	 * @return $this
	 */
	public function close($topic, $user)
	{
		// Don't overwite previous users accountability.
		if ( ! $topic->getClosedBy() && ! $topic->getClosedDate())
		{
			$topic->setClosedBy($user);
			$topic->setClosedDate(new \DateTime());
		
			$this->persist($topic);
		}
		
		return $this;
	}
	

	
	/**
	 *
	 * @access public
	 * @param $topic
	 * @return $this
	 */
	public function reopen($topic)
	{
		$topic->setClosedBy(null);
		$topic->setClosedDate(null);
		
		$this->persist($topic);
		
		return $this;
	}

	
	
	/**
	 *
	 * @access public
	 * @param $topics 
	 * @return $this
	 */
	public function bulkClose($topics, $user)
	{
		foreach($topics as $topic)
		{
			// Don't overwite previous users accountability.
			if ( ! $topic->getClosedBy() && ! $topic->getClosedDate())
			{
				$topic->setClosedBy($user);
				$topic->setClosedDate(new \DateTime());
			
				$this->persist($topic);
			}
		}
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $topics 
	 * @return $this
	 */
	public function bulkReopen($topics)
	{
		foreach($topics as $topic)
		{
			$topic->setClosedBy(null);
			$topic->setClosedDate(null);
			
			$this->persist($topic);
		}
		
		return $this;
	}

	
	
	/**
	 *
	 * @access public
	 * @param $topics
	 * @return $this
	 */
	public function bulkSoftDelete($topics, $user)
	{
		$boards_to_update = array();
		
		foreach($topics as $topic)
		{
			// Don't overwite previous users accountability.
			if ( ! $topic->getDeletedBy() && ! $topic->getDeletedDate())
			{
				// Add the board of the topic to be updated.
				if ($topic->getBoard())
				{
					if ( ! array_key_exists($topic->getBoard()->getId(), $boards_to_update))
					{
						$boards_to_update[$topic->getBoard()->getId()] = $topic->getBoard();
					}
				}
				
				$topic->setDeletedBy($user);
				$topic->setDeletedDate(new \DateTime());
			
				$this->persist($topic);			
			}
		}
		
		$this->flushNow();
		
		// Update all affected board stats.
		$this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boards_to_update)->flushNow();
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $topics 
	 * @return $this
	 */
	public function bulkRestore($topics)
	{
		$boards_to_update = array();
		
		foreach($topics as $topic)
		{
			// Add the board of the topic to be updated.
			if ($topic->getBoard())
			{
				if ( ! array_key_exists($topic->getBoard()->getId(), $boards_to_update))
				{
					$boards_to_update[$topic->getBoard()->getId()] = $topic->getBoard();
				}
			}
			
			$topic->setDeletedBy(null);
			$topic->setDeletedDate(null);
		
			$this->persist($topic);
		}

		$this->flushNow();
		
		// Update all affected board stats.
		$this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boards_to_update)->flushNow();
		
		return $this;
	}
	
}