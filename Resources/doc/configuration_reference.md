CCDNForum ModeratorBundle Configuration Reference.
==================================================

All available configuration options are listed below with their default values.

``` yml
#
# for CCDNForum ModeratorBundle
#
ccdn_forum_moderator:
    user:
        profile_route: ccdn_user_profile_show_by_id
    template:
        engine: twig
	seo:
		title_length: 67
    flag:
        show_flagged:
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            flags_per_page: 40
            topic_title_truncate: 50
            flag_created_datetime_format: 'd-m-Y - H:i'
            post_created_datetime_format: 'd-m-Y - H:i'
        show_flag:
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
        update_flag:
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme: CCDNForumModeratorBundle:Form:fields.html.twig
    item_flag:
        flag_created_datetime_format: 'd-m-Y - H:i'
        moderated_datetime_format: 'd-m-Y - H:i'
    topic:
        change_board:
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme: CCDNForumModeratorBundle:Form:fields.html.twig
        delete_topic: 
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
        show_closed: 
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            topics_per_page: 40
            topic_title_truncate: 20
            post_created_datetime_format: 'd-m-Y - H:i'
            topic_closed_datetime_format: 'd-m-Y - H:i'
            topic_deleted_datetime_format: 'd-m-Y - H:i'
    post:
        show_locked:
            layout_template: CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            posts_per_page: 40
            topic_title_truncate: 20
            post_created_datetime_format: 'd-m-Y - H:i'
            post_locked_datetime_format: 'd-m-Y - H:i'
            post_deleted_datetime_format: 'd-m-Y - H:i'

```

- [Return back to the docs index](index.md).
