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
class FlagManager extends ForumBundle\Manager\FlagManager implements ManagerInterface
{



	/**
	 *
	 * @access public
	 * @param $flag
	 * @return $this
	 */
	public function update($flag)
	{
		// update a record
		$this->persist($flag);
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $flags
	 * @return $this
	 */
	public function bulkDelete($flags)
	{
		foreach($flags as $flag)
		{
			$this->remove($flag);
		}
		
		return $this;
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param $flags, $status
	 * @return $this
	 */
	public function bulkMarkAs($flags, $status)
	{
		foreach($flags as $flag)
		{
			$flag->setStatus($status);
			$this->persist($flag);
		}
		
		return $this;
	}
	
}