=== Captcha Free Anti Spam for Contact Form 7 (Simple No-Bot) ===
Contributors: lilaeamedia
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DE4W9KW7HQJNA
Tags: contact form 7, auto captcha, bot blocker, spam blocker, invisible recaptcha, honeypot, antispam, anti-spam, captcha, anti spam, form, forms, contactform7, contact form, cf7, recaptcha, no captcha
Requires at least: 4.7
Tested up to: 4.7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple, lightweight, no captcha, no configuration. Just works.

== Description ==

Simple No-Bot uses a combination of WordPress nonces and Javascript to verify Contact Form 7 is not being submitted by a spam bot instead of captcha. 

We wrote this when clients were reporting hundreds of bogus contact forms were getting past Honeypot, but did not want to add a captcha that would impact conversions. 

This lightweight script has been extremely effective for eliminating Contact Form 7 spam messages. It does not pretend to be a complete anti spam solution.

If there is demand we might extend it to work with other forms, including comment forms. Please report any feedback and false negatives/positives on our support form at http://www.lilaeamedia.com/contact/

== Installation ==

1. To install from the Plugins repository:
    * In the WordPress Admin, go to "Plugins > Add New."
    * Type "simple no-bot" in the "Search" box and click "Search Plugins."
    * Locate "Simple No-Bot Captcha Alternative for Contact Form 7" in the list and click "Install Now."

2. To install manually:
    * Download the IntelliWidget plugin from https://wordpress.org/plugins/simple-no-bot/
    * In the WordPress Admin, go to "Plugins > Add New."
    * Click the "Upload" link at the top of the page.
    * Browse for the zip file, select and click "Install."

3. In the WordPress Admin, go to "Plugins > Installed Plugins." Locate "Simple No-Bot Captcha Alternative for Contact Form 7" in the list and click "Activate."

== Frequently Asked Questions ==

= How does it work? =

The browser automatically generates an arbitrary string based on user input events and passes it to the server via XHR. The server generates a unique token, stores a session in a transient record and returns token to the browser. The browser then injects a new input field to WPCF7 form that contains token and hashed event string. When form is submitted, server compares hashed string to stored event string and rejects form if it does not match or if no corresponding session exists. 

The plugin relies on WordPress nonces so there are no guarantees. So far, however, it has been extremely effective.

= Does it work without Javascript =

No. Contact forms will fail if Javascript is not enabled.

= Does it require cookies? =

It uses the default WordPress session token validate nonces.

== Screenshots ==

No screens to shoot.

== Changelog ==

1.0 Initial release
1.0.2 Change wp nonce functions to wpcf7 nonce functions

== Upgrade Notice ==

Nothing to report yet.

== Support ==

Please report any feedback and false negatives/positives on our support form at http://www.lilaeamedia.com/contact/
