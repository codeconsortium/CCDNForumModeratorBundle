Installing CCDNForum ModeratorBundle 1.0
========================================


## Dependencies:

1. [PagerFanta](http://github.com/whiteoctober/Pagerfanta).
2. [PagerFantaBundle](http://github.com/whiteoctober/WhiteOctoberPagerfantaBundle).
3. [CCDNComponent CommonBundle](http://github.com/codeconsortium/CommonBundle).
4. [CCDNComponent BBCodeBundle](http://github.com/codeconsortium/BBCodeBundle).
5. [lib-geshi](http://github.com/codeconsortium/lib-geshi).
6. [CCDNComponent CrumbTrailBundle](http://github.com/codeconsortium/CrumbTrailBundle).
7. [CCDNComponent DashboardBundle](http://github.com/codeconsortium/DashboardBundle).
8. [CCDNComponent AttachmentBundle](http://github.com/codeconsortium/AttachmentBundle).
9. [CCDNForum ForumBundle](http://github.com/codeconsortium/CCDNForumForumBundle).
10. [CCDNForum KarmaBundle](http://github.com/codeconsortium/CCDNForumKarmaBundle).
11. [CCDNForum AdminBundle](http://github.com/codeconsortium/CCDNForumModeratorBundle).

## Installation:

Installation takes only 9 steps:

1. Download and install the dependencies.
2. Register bundles with autoload.php.
3. Register bundles with AppKernel.php.  
4. Run vendors install script.
5. Update your app/config/routing.yml. 
6. Update your app/config/config.yml. 
7. Update your database schema.
8. Symlink assets to your public web directory.
9. Warmup the cache.


### Step 1: Download and install the dependencies.

Append the following to end of your deps file (found in the root of your Symfony2 installation):

``` ini
[CCDNForum_ModeratorBundle]
    git=http://github.com/codeconsortium/CCDNForumModeratorBundle.git
    target=/bundles/CCDNForum/ModeratorBundle

```

### Step 2: Register bundles with autoload.php.

Add the following to the registerNamespaces array in the method by appending it to the end of the array.

``` php
// app/autoload.php
$loader->registerNamespaces(array(
    'CCDNForum'        => __DIR__.'/../vendor/bundles',	
	**...**
));
```

### Step 3: Register bundles with AppKernel.php.

In your AppKernel.php add the following bundles to the registerBundles method array:  

``` php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
		new CCDNForum\ModeratorBundle\CCDNForumModeratorBundle(),
		**...**
	);
}
```

### Step 4: Run vendors install script.

From your projects root Symfony directory on the command line run:

``` bash
$ php bin/vendors install
```

### Step 5: Update your app/config/routing.yml.

In your app/config/routing.yml add:  

``` yml
CCDNForumModeratorBundle:
    resource: "@CCDNForumModeratorBundle/Resources/config/routing.yml"
    prefix: /

```
	
### Step 6: Update your app/config/config.yml.

In your app/config/config.yml add:    

``` yml
#
# for CCDNForum ModeratorBundle
#
ccdn_forum_moderator:
    user:
        profile_route: cc_profile_show_by_id
    template:
        engine: twig
        theme: CCDNForumModeratorBundle:Form:fields.html.twig
    flag:
        flags_per_page: 40
        layout_templates:
            flag_mark: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
            show_flag: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
            show_flagged: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
    topic:
        topics_per_page: 40
        posts_per_page: 20
        layout_templates:
            change_board: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
            show_closed: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
            delete_topic: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
    post:
        posts_per_page: 40
        layout_templates:
            show_locked: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig

```

**Warning:**

>Set the appropriate layout templates you want under the sections 'layout_templates' and the 
route to a users profile if you are not using the [CCDNUser\ProfileBundle](http://github.com/codeconsortium/CCDNUserProfileBundle). Otherwise use defaults.

### Step 7: Update your database schema.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console doctrine:schema:update --dump-sql
```

Take the SQL that is output and update your database manually.

**Warning:**

> Please take care when updating your database, check the output SQL before applying it.

### Step 8: Symlink assets to your public web directory.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console assets:install --symlink web/
```

### Step 9: Warmup the cache.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console cache:warmup
```

Change the layout template you wish to use for each page by changing the configs under the labelled section 'layout_templates'.

## Next Steps.

Installation should now be complete!

If you need further help/support, have suggestions or want to contribute please join the community at [Code Consortium](http://www.codeconsortium.com)

[Return back to the docs index](http://github.com/codeconsortium/CCDNForumModeratorBundle/blob/master/Resources/doc/index.md).
