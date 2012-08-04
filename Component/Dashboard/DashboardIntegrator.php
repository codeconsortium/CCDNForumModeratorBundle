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

namespace CCDNForum\ModeratorBundle\Component\Dashboard;

use CCDNComponent\DashboardBundle\Component\Integrator\BaseIntegrator;
use CCDNComponent\DashboardBundle\Component\Integrator\IntegratorInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class DashboardIntegrator extends BaseIntegrator implements IntegratorInterface
{

    /**
     *
     *
     * Structure of $resources
     * 	[DASHBOARD_PAGE String]
     * 		[CATEGORY_NAME String]
     *			[ROUTE_FOR_LINK String]
     *				[AUTH String]
     *				[URL_LINK String]
     *				[URL_NAME String]
     */
    public function getResources()
    {
        $resources = array(
            'moderator' => array(
                'Forum Moderation' => array(
                    'cc_moderator_forum_flagged_show_all' => array('auth' => 'ROLE_MODERATOR', 'name' => 'Flagged Posts', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_flag.png'),
                    'cc_moderator_forum_topics_closed_show_all' => array('auth' => 'ROLE_MODERATOR', 'name' => 'Closed Topics', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_lock.png'),
                    'cc_moderator_forum_posts_show_all_locked' => array('auth' => 'ROLE_MODERATOR', 'name' => 'Locked Posts', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_lock.png'),
                ),
            ),

        );

        return $resources;
    }

}
