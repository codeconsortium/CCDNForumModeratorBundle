CCDNForum ModeratorBundle README.
=================================


## Notes: 

This bundle is for the symfony framework and requires Symfony 2.0.x and PHP 5.3.6
  
This project uses Doctrine 2.0.x and so does not require any specific database.
  

This file is part of the CCDNForum Bundle(s)

(c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 

Available on github <http://www.github.com/codeconsortium/>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.

## Description:

This is a complimentary ModeratorBundle for forum management of the [CCDNForum ForumBundle](https://github.com/codeconsortium/CCDNForumForumBundle) for Symfony (2.0.11).

You will need to look at [CCDNForum ForumBundle](https://github.com/codeconsortium/CCDNForumForumBundle) and install it along with its dependencies before using this bundle.

## Features.

ModeratorBundle Provides the following features:

1. Flag Management:
	1. Show Flags raised by users.
	2. Flags are grouped by the post which is being flagged.
	3. Delete multiple Flags by checkboxes on Flag manager index.
	4. Change status of multiple Flags by checkboxes on Flag manager index.
	5. View individual Flag along with in-depth description of issue.
	6. Append moderators note to Flag when changing status individually.
	7. Edit the post that the Flag has raised issue from within Flag.
2. Closed/Soft-deleted Topics Management:
	1. List all closed/soft-deleted Topics.
	2. Batch close soft-deleted Topics.
	3. Batch re-open closed Topics.
	4. Batch soft-delete closed Topics.
	5. Batch restore soft-deleted Topics.
3. Locked/Soft-deleted Posts Management:
	1. List all locked/soft-deleted Posts.
	2. Batch lock soft-deleted Posts.
	3. Batch unlock locked Posts.
	4. Batch soft-delete locked Posts.
	5. Batch restore soft-deleted Posts.

Before installation of this bundle, you can download the [Sandbox](https://github.com/codeconsortium/CCDNSandBox) for testing/development and feature review, or alternatively see the product in use at [CodeConsortium](http://www.codeconsortium.com).

## Documentation.

Documentation can be found in the `Resources/doc/index.md` file in this bundle:

[Read the Documentation](http://github.com/codeconsortium/CCDNForumModeratorBundle/blob/master/Resources/doc/index.md).

## Installation.

All the installation instructions are located in [documentation](http://github.com/codeconsortium/CCDNForumModeratorBundle/blob/master/Resources/doc/Install.md).

## License.

This software is licensed under the MIT license. See the complete license file in the bundle:

	Resources/meta/LICENSE

[Read the License](http://github.com/codeconsortium/CCDNForumModeratorBundle/blob/master/Resources/meta/LICENSE).

## About.

[CCDNForum ModeratorBundle](http://github.com/codeconsortium/CCDNForumModeratorBundle) is free software as part of the CCDNForum from [Code Consortium](http://www.codeconsortium.com). 
See also the list of [contributors](http://github.com/codeconsortium/CCDNForumModeratorBundle/contributors).

## Reporting an issue or feature request.

Issues and feature requests are tracked in the [Github issue tracker](http://github.com/codeconsortium/CCDNForumModeratorBundle/issues).

Discussions and debates on the project can be further discussed at [Code Consortium](http://www.codeconsortium.com).

