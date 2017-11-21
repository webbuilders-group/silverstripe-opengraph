# Opengraph module for Silverstripe

This module provides a complete implementation of each of the Open Graph types as documented at <http://ogp.me/>

Open Graph object types may be applied to any Page or DataObject by applying the appropriate interface.

For instance, if your page represents a music album you would implement the IOGMusicAlbum interface.

By default, the module will attempt to classify pages as the og:website type, and automatically
generate appropriate meta tags for it. This is all that most websites require to adequately interact
with Facebook.

## Credits and Authors

 * Damian Mooyman - <https://github.com/tractorcow/silverstripe-opengraph>

## Requirements

 * SilverStripe 4

## Installation Instructions

 * Extract all files into the 'opengraph' folder under your Silverstripe root, or install using composer

```bash
composer require "tractorcow/silverstripe-opengraph" "3.1.*@dev"
```

 * Ensure the namespace is defined in your template with ``` <html $OGNS> ```
 * If you need to add a prefix attribute to the ``` <head /> ``` tag then you should do this directly
   in your template.
 * If you are working with video files, you might want to install <https://github.com/tractorcow/silverstripe-mediadata>
   alongside this to extract video dimension for opengraph tags.

## Configuration

The main configuration options for this module can be found in [OpenGraph.yml](_config/OpenGraph.yml).

Override these in your own `mysite/_config/OpenGraph.yml` or `mysite/_config.php`

```yaml
---
Name: myopengraphsettings
After: '#opengraphsettings'
---
OpenGraph:
  application_id: 'SiteConfig'
  admin_id: 'SiteConfig'
  default_locale: 'en_US'
  default_tagbuilder: 'OpenGraphBuilder'

```

* Set application_id to either `SiteConfig` (to be set in the CMS) or a literal facebook app id
* Set admin_id to either `SiteConfig` (to be set in the CMS) or a literal facebook admin_id
* The default_locale is the literal value to use as the locale tag (if i18n doesn't have a locale set)
* The default_tagbuilder is the name of the class to use to generate tags (unless a type has one
  specified explicitly). See below under [Adding new types][#adding-new-types] for details.

Any value above can be set to an empty string to disable it completely. E.g.

```yaml
---
Name: myopengraphsettings
After: '#opengraphsettings'
---
OpenGraph:
  application_id: ''
  admin_id: ''
```

## How to do stuff

### Implementing Open Graph object properties

To get specific information on each of the fields an opengraph object can have, check
out the various implementations of each in the [interfaces/ObjectTypes](interfaces/ObjectTypes) folder,
or in the [_config/OpenGraphTypes.yml](_config/OpenGraphTypes.yml) file for the list of
types and their respective interfaces.

The basic opengraph object has a set of required properties (as defined by IOGObjectRequired)
and additionally a set of optional properties (as defined by IOGObjectExplicit).

Since most of the field values are generated by the page extension class OpenGraphPageExtension
automatically, you don't need to explicitly implement either of these. These should however
should be used as a guide to what can be specified.

For example, if you wanted to override the getOGImage property (og:image meta tag) you would implement the
following in your page classe:

```php
class MyPage extends Page {

    function getOGImage() {
        return $this->Thumbnail();
    }

}
```

By implementing these properties explicitly in your page classes, you can override the default properties
defined in the OpenGraphPageExtension.

### Adding new types

If you wish to add a new og:type you will need to:
 * Create an interface that extends IOGObject that defines the fields (if any)
   that your object will publish
 * Extend the OpenGraphBuilder class and override the BuildTags function
   to generate the actual HTML for the tags in your interface
 * Implement your interface on pages of the new type
 * Register your object type with the following code:

```php
OpenGraph::register_type('type-name', IOGMyObjectInterface, MyObjectTagBuilder);
```

Or better still, do this directly in yaml as below

```yaml
OpenGraph:
  types:
    'type-name':
      interface: IOGMyObjectInterface
      tagbuilder: MyObjectTagBuilder
```

### Creating a custom tag builder

In order to add an opengraph meta tag to your page, you need to write the code that
describes how to translate an object into a piece of html. This can be done by
implementing this in PHP with a `TagBuilder` object.

Note that there are two objects for every request; The entity being viewed (Page or DataObject)
and the application (SiteConfig). Each has their own set of tags.

E.g.

```php
class MyObjectTagBuilder extends OpenGraphBuilder {

    public function BuildTags(&$tags, $object, $config) {
        parent::BuildTags($tags, $object, $config);

        $this->appendTag($tags, 'appnamespace:nameofthetag', $object->getOGNameOfTheTag());
    }
}
```

Our interface might look something like

```php
interface IOGMyObjectInterface extends IOGObject {
	
	function getOGNameOfTheTag();
}

```

### Adding tags to the default type

You can decorate the OpenGraphBuilder object instead of extending it if you need
to add additional tags to all object types.

The example below shows how to add extra fields from the Page and SiteConfig
to the set of opengraph tags.

```php
OpenGraphBuilder::add_extension('OpengraphBuilderExtension');

class OpengraphBuilderExtension extends Extension {

    function updateApplicationMetaTags(&$tags, $siteconfig) {
        $this->owner->AppendTag($tags, 'og:application-name', $siteconfig->Title);
    }
    
    function updateDefaultMetaTags(&$tags, $page) {
        $this->owner->AppendTag($tags, 'og:page-menu-name', $page->MenuTitle);
    }

}
```

### Disabling opengraph for a single page (or page type)

If you need to disable opengraph for any page then a null value for `getOGType()`
will disable tag generation.

```php
NonOGPage extends Page {

    function getOGType() {
        return null;
    }

}
```

### Using DataObjects as pages

See [https://github.com/tractorcow/silverstripe-opengraph/wiki/Using-DataObjects-as-Pages](https://github.com/tractorcow/silverstripe-opengraph/wiki/Using-DataObjects-as-Pages)
for how to extend your `DataObject` with `OpenGraphObjectExtension`.

 * Add the `OpenGraphObjectExtension` extension to your object
 * Implement `AbsoluteLink` on your object
 * Implement `MetaTags` on your object, making sure to call `$this->extend('MetaTags', $tags);`
 * Make sure the actual page type being viewed delegates the meta tag generation to your dataobject

## Need more help?

Message or email me at damian.mooyman@gmail.com or, well, read the code!

## License

Copyright (c) 2013, Damian Mooyman
All rights reserved.

All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

 * Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
 * The name of Damian Mooyman may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

## Apologies

I went a bit crazy with this module! Good old interfaces eh?
