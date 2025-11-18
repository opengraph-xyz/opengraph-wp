=== Dynamic Open Graph Images - OpenGraph.xyz ===
Contributors: opengraphxyz
Tags: open graph, images, og image, dynamic og image, meta tags
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 5.6
Version: 1.3.0
Stable tag: 1.3.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Enhance your WordPress site with dynamic Open Graph images.

== Description ==

Enable dynamic and automatic updates of Open Graph images across your website. This plugin allows you to choose from an array of templates, both free and premium, ensuring your social media previews are always eye-catching and brand-aligned.

### What is OpenGraph.xyz?

[OpenGraph.xyz](https://www.opengraph.xyz/) is a SaaS platform designed to enhance your website's presence on social media. By generating and previewing Open Graph meta tags, OpenGraph.xyz ensures your content stands out when shared. With the introduction of dynamic Open Graph images, your website's visibility and click-through rates on social media platforms can significantly increase. This plugin brings the power of OpenGraph.xyz directly into your WordPress site, allowing for seamless integration and management.

### What does this plugin do?

Dynamic Open Graph Images - OpenGraph.xyz integrates your WordPress site with OpenGraph.xyz services, enabling:

- Automatic generation and updating of Open Graph meta tags.
- Selection from a variety of Open Graph image templates.
- Access to both free and premium templates for diverse customization.
- Enhanced social media previews to attract more clicks and engagement.

### Who is this plugin for?

This plugin is ideal for website owners, marketers, and content creators who want to boost their social media presence and engagement. Whether you run a blog, an e-commerce site, or a business website, dynamic Open Graph images can help your content capture attention on platforms like Facebook, Twitter, and LinkedIn.

== Installation ==

1. Upload `opengraph-xyz.zip` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Do I need an OpenGraph.xyz account to use this plugin? =

No, an OpenGraph.xyz account is not required to access free templates. However, to create your own branded custom Open Graph templates and access premium features, an account with OpenGraph.xyz is necessary.

= Is this plugin suitable for beginners? =

Absolutely! Dynamic Open Graph Images - OpenGraph.xyz is designed to be user-friendly, allowing anyone to enhance their social media previews with ease.

= How do I select an Open Graph template for my website? =

Once the plugin is installed and activated, navigate to the 'OpenGraph' menu in your WordPress dashboard. Here, you'll find a variety of templates to choose from. Simply select a template that aligns with your brand's identity, and it will be automatically applied to your website's Open Graph meta tags.

= How do I link template variables to WordPress variables? =

Once you've selected a template, the next step is to map the template's dynamic variables to corresponding WordPress variables. For example, you can link a 'title' variable in your template to the 'Post Title' in WordPress. This ensures that the dynamic content from your site, like post titles or featured images, is automatically incorporated into your Open Graph images. You'll find these settings in the template editing interface where you can easily assign WordPress variables to your template's dynamic elements.

= Can I use different templates for different types of content? =

Yes, the plugin allows you to assign different templates to different post types or individual posts. This ensures that each piece of content on your website can have a unique and relevant Open Graph image.

= How to contact OpenGraph.xyz? =

If you have any questions or need assistance, you can reach out to the OpenGraph.xyz team directly. Visit our website at [OpenGraph.xyz](https://www.opengraph.xyz) and use the chat feature to get in touch with us. Our team is always ready to help you with any queries or support you may need regarding our service or the WordPress plugin.

== Screenshots ==

1. Choose an OG template
2. Assign dynamic fields to a template
3. Add OpenGraph.xyz API key
4. Open Graph image example

== External Services ==

= ogcdn.net = 
This is our open graph image generation service to create your og image. The URLs of your generated og:image tags point to this service with data about your page to dynamically create the images.
[Terms of Service](https://www.opengraph.xyz/page/terms-of-service) | [Privacy Policy](https://www.opengraph.xyz/page/privacy-policy)

= api.opengraph.xyz = 
This is our service for the og image templates. This service will be accessed when you view and choose image templates for your pages.
[Terms of Service](https://www.opengraph.xyz/page/terms-of-service) | [Privacy Policy](https://www.opengraph.xyz/page/privacy-policy)


== Changelog ==

= 1.3.0 =
* Improved compatibility with Rank Math SEO plugin

= 1.2.3 =
* Added validation when saving API Key

= 1.2.2 =
* Fix a bug when changing versions
* Add an "edit on Open Graph" button

= 1.2.0 =
* Add API Key verification.
* Split templates into "Stock" and "Your Templates" tabs for improved user experience.
* Stock templates now create a template inside Open Graph.
* Add thumbnail images to the OG Manager and Match Variables screen.

= 1.1.0 =
* Prevent multiple template creations when choosing a template

= 1.0.1 =
* Tested up to 6.8.1
* Improved compatibility with Yoast SEO plugin.
* Fixed issue where both OpenGraph XYZ and Yoast SEO tags were being displayed simultaneously.
* Optimized plugin initialization process to ensure proper loading order with other plugins.
* Added 'opengraph-xyz-meta-tag' class to meta tags for better identification.
* Improved caching mechanism for OpenGraph image URLs to enhance performance.

= 1.0.0 =
* Initial release.
