=== Really Simple CAPTCHA for cformsII ===
Contributors: bgermann
Donate link: https://www.betterplace.org/organisations/tatkraeftig/donations/new
Tags: captcha, spam, protection, verification, cforms, cforms2, cformsII
Requires at least: 3.9
Tested up to: 4.5
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0

This enables the Really Simple CAPTCHA for the cformsII form plugin.

== Description ==

Beginning with version 14.9.1 [cformsII](https://wordpress.org/plugins/cforms2)
has pluggable captcha support.
This plugin makes use of that by providing an implementation for the
[Really Simple CAPTCHA](https://wordpress.org/plugins/really-simple-captcha/).

= License Information =

Copyright (c) 2015-2016 Bastian Germann

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


== Installation ==

= Installing the plugin =

You can install the plugin via WordPress Dashboard. It should show up by
searching for cforms2-really-simple-captcha.
If this does not work for you, there should be an option to upload a zip file,
which is available on the [wordpress.org plugin directory](https://wordpress.org/plugins/cforms2-really-simple-captcha/).

If you want to install manually, please upload the complete plugin folder
"cforms2-really-simple-captcha", contained in the zip file,
to your WordPress plugin directory!

If there are missing dependencies, you should be notified on plugin activation.


== Frequently Asked Questions ==

= Can I configure the generated image's parameters? =

The following parameters are taken from the CAPTCHA configuration that is
integrated in cformsII: characters length, font size, image size, foreground
color, background color and allowed characters.
For further details please see the cformsII Global Settings.

== Changelog ==

= 1.0 =
* added:    animation on CAPTCHA reset to indicate activity

= 0.3 =
* enhanced: adopt the cformsII 14.11 pluggable CAPTCHA API change
* enhanced: it is possible to show more than one form with CAPTCHA on one site

= 0.2 =
* added:    CAPTCHA reset feature
* bugfix:   check for classes existing to not cause a fatal error

= 0.1 =
* added:    Really Simple CAPTCHA implementation for the cformsII pluggable
            captcha API
