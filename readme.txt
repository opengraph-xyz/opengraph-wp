=== AI Open Graph Image Generator - OpenGraph.xyz ===
Contributors: opengraphxyz
Tags: open graph, images, og image, dynamic og image, meta tags
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 5.6
Version: 1.5.4
Stable tag: 1.5.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Description: Generate dynamic Open Graph images for WordPress posts and pages with AI templates, variable mapping, and automated social preview metadata.

== Description ==
**Stop losing clicks to boring social previews. Boost your Click-Through Rate (CTR) by up to 70% with AI-powered, dynamic Open Graph images.**

Most websites suffer from "broken" or generic social media previews. If you have a resource-rich site—like a news portal, real estate listing site, e-commerce store, or a large blog—manually designing and uploading Open Graph (OG) images for every page is impossible.

**[OpenGraph.xyz](https://www.opengraph.xyz)** solves this by automating your entire social metadata workflow.


= Why OpenGraph.xyz? =
Our plugin bridges the gap between your WordPress site and the [OpenGraph.xyz](https://www.opengraph.xyz) SaaS platform. We’ve seen specialized links jump by 70% in CTR when switching from static, unoptimized images to our dynamic, branded previews.

= Key Features =
- AI OG Image Template Creator: Simply enter your URL, and our AI analyzes your brand theme, colors, and content to generate a beautiful, custom OG image template in seconds.
- Dynamic Image Automation: Once set up, every new post or listing automatically generates a unique social image. No design skills or manual uploads required.
- Metadata Site Audit: Scan your entire website to identify missing tags, broken links, or unoptimized metadata before it affects your traffic.
- AI Alt Text Generator: Improve accessibility and SEO automatically with AI-generated descriptions for your website images.
- AI Meta Title & Description Generation: Let AI craft high-converting titles and descriptions for every page on your site.
- Custom Sharing Links: Create beautiful, branded sharing links that allow you to A/B test your OG images and boost CTR.
- Global CDN Hosting: All images are served through our lightning-fast global CDN, ensuring your previews load instantly.

**Note:** This plugin requires an [OpenGraph.xyz](https://www.opengraph.xyz) account. We offer a 7-day Free Trial with full access to all available features during the trial period (credit card required). Cancel anytime.


== Installation ==

1. Upload `opengraph-xyz.zip` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Do I need an OpenGraph.xyz account to use this plugin? =

Yes, an account is required to connect your WordPress site to our automation engine. You can start with our **[7-day Free Trial](https://www.opengraph.xyz/pricing)**, which gives you full access to our Lite plan, which includes our AI Template Creator, Site Audits, and the full design suite. You can cancel at any time during the trial.

= Who is this plugin for? =

This plugin is designed for blogs, resource centers, listing sites, media publishers, e-commerce stores, and UGC websites that need automated Open Graph images at scale.

= Is this plugin suitable for beginners? =

Absolutely. For beginners, our **AI Template Creator** does all the heavy lifting—it creates a branded design for you based on your website's URL. For users who need more control, we offer a manual editor to customize every pixel of your dynamic templates.

= How do I design and set up my Open Graph templates? =

After installing the plugin, you can
- Use the **AI Template Creator** to generate a branded design automatically.
- Select from our wide variety of **pre-designed templates**.
- Use the **Manual Editor** to build a template from scratch.
For detailed setup steps, check out our installation guide here.


= How do I link template variables to WordPress variables? =

Once you’ve selected a template, the next step is to map the template’s dynamic variables to corresponding WordPress variables. For example, you can link a ‘title’ variable in your template to the ‘Post Title’ in WordPress. This ensures that the dynamic content from your site, like post titles or featured images, is automatically incorporated into your Open Graph images. You’ll find these settings in the template editing interface, where you can easily assign WordPress variables to your template’s dynamic elements.

= Can I use different templates for different types of content? =

Yes, the plugin allows you to assign different templates to different post types or individual posts. This ensures that each piece of content on your website can have a unique and relevant Open Graph image.

= How to contact OpenGraph.xyz? =

If you have any questions or need assistance, you can reach out to the [OpenGraph.xyz](https://www.opengraph.xyz) team directly. Visit our website at [opengraph.xyz](https://www.opengraph.xyz) and use the chat feature to get in touch with us. Our team is always ready to help you with any queries or support you may need regarding our service or the WordPress plugin.

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

= 1.5.4 =
* Fix deploy workflow to sync plugin assets to WordPress.org assets directory
* Upgrade actions/checkout to v6 for Node.js 24 compatibility

= 1.5.3 =
* Fix plugin version header to correctly reflect 1.5.2 release

= 1.5.2 =
* Updated Readme with new features and FAQs
* Display Layer names instead of variables on the Template Details page

= 1.5.1 =
* Updated default dashboard URL to dashboard.opengraph.xyz

= 1.5.0 =
* Added advanced filters for templates

= 1.4.0 =
* Added "Published Date" and "Modified Date" page filters for a template

= 1.3.1 =
* Fix version numbering

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
