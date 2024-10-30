=== MSN-Notifier ===
Contributors: tension7
Tags: post, msnspace, publish, synchronization, blog, update
Requires at least: 2.0
Tested up to: 2.6.2
Stable tag: 2.2

A wordpress plugin to synchronize your MSNSpace whenever you publish a post.

== Description ==

This is a wordpress plugin, which help those who don't want to give up MSNSpace. This plugin hooks onto the publish action of a post, so that whenever you publish a post in your wordpress, a notification containing the title, a teaser of the post (therefore first 50 characters or up to the "more" tag) and a bloglink is sent to your MSNspace automatically. 

You need to enable email-publishing from your MSNSpace.
Only posts are synchronized, not pages.
You can choose not to notify msnspace for a specific post easily by unchecking an option on post editing page.

== Installation ==

unzip and put msnnotifier.php into your plugin page (usually /wordpress/wp-content/plugins). Setup the plugin in the option page.

Now the plugin can use three method to synchronize with your MSNSpace: PHP mail() function, linux sendmail, and SMTP, basically what's supported by wp-includes/class-phpmailer.php. The mail and sendmail methods are recommended, which requires little setup. You just need to provide your MSNSpace email publishing address, your domain name in the plugin option page. 

REMINDER: Add your msnnotifier@yourdomainn to MSNSpace's email publishing from address.

The SMTP method is still included but it no longer works for Gmail which requires a SSL connection not supported by wordpress's phpmailer class. Install a newer version of phpmailer may solve the problem and you will have change the source yourself. SMTP Authencation is always used for SMTP method.
