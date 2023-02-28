# Single Page App Landing Page module for Drupal

This module provides a standardised way for site managers to configure and serve
single-page applications as pages in a Drupal site, with app configuration available as JSON.

For more information on the thinking behind this module, see [this blog post](https://capgemini.github.io/drupal/spalp/)

This approach has been described by Dries Buytaert as [“Progressively Decoupled”](https://dri.es/how-to-decouple-drupal-in-2018).

## Installation

The module uses the [JSON Forms library](https://github.com/brutusin/json-forms) to provide an enhanced editor experience.

If this library is not present, the JSON will be editable as a textarea.

1. Include the following lines in the `repositories` section
 of your project's `composer.json`:

        {
            "type": "package",
            "package": {
                "name": "brutusin/json-forms",
                "version": "0.0.0",
                "type": "drupal-library",
                "source": {
                    "url": "https://github.com/brutusin/json-forms.git",
                    "type": "git",
                    "reference": "origin/master"
                }
            }
        }
2. Run `composer require brutusin/json-forms`
3. Run `composer require drupal/spalp`

## Usage
You will need to create a custom module that declares a dependency on `spalp`.

Your module should implement an EventSubscriber on the
`SpalpAppIdsAlterEvent::APP_IDS_ALTER` event, and provide the module's app id.

See [\Drupal\spalp_example\EventSubscriber\SpalpExampleAppIdsAlterSubscriber](https://git.drupalcode.org/project/spalp/-/blob/2.x/spalp_example/src/EventSubscriber/SpalpExampleAppIdsAlterSubscriber.php) for an example.

### App Landing Page content type
The `spalp` module defines a content type to called `applanding` to provide a
landing page for each app.

When your module is installed, an unpublished `applanding` node will be created.

The app ID provided by your module will be used as the ID of
a `<div>` element on the node view.
This can be used as your main app element.

All relevant configuration and text used in the app is stored on the node's
`field_spalp_config_json` field.

### Default configuration and application text
Create a JSON file in your module directory called `config/spalp/mymodule.config.json`.
See [spalp_example.config.json](https://git.drupalcode.org/project/spalp/blob/8.x-2.x/spalp_example/config/spalp/spalp_example.config.json) for an example of the structure.

The values in this file will be used to populate the `field_spalp_config_json` field when the `applanding` node is created.

Generate a [JSON schema](https://json-schema.org/) file at `config/spalp/mymodule.config.schema.json`. This is used by the [JSON Forms library](https://github.com/brutusin/json-forms) to build an editing form for the JSON field.

If the location of the initial config file needs to change, you can implement an EventSubscriber for the `SpalpConfigLocationAlterEvent`, similar to [SpalpExampleConfigLocationAlterSubscriber](https://git.drupalcode.org/project/spalp/-/blob/2.x/spalp_example/src/EventSubscriber/SpalpExampleConfigLocationAlterSubscriber.php)

### Adding your app's assets
1. [Define a library for your module's assets](https://www.drupal.org/docs/creating-custom-modules/adding-assets-css-js-to-a-drupal-module-via-librariesyml)
2. If the library name matches your module's machine name, the spalp module
   will attach the library when the applanding node is viewed.

### JSON endpoints
The configuration from the `applanding` node is made available as a JSON endpoint at `/spalp/mymodule/json`.

When viewing an `applanding` node, a link to the JSON endpoint will appear
 in the page head, with the id `appConfig`.

Your JavaScript application should get its configuration from this endpoint.
 
### Overriding configuration
It may be useful to override the configuration stored in the node. 
For example, you may connect to different endpoints in production and test environments.

To do this, implement an EventSubscriber for the `SpalpConfigAlterEvent`, similar to [SpalpExampleConfigAlterSubscriber](https://git.drupalcode.org/project/spalp/-/blob/2.x/spalp_example/src/EventSubscriber/SpalpExampleConfigAlterSubscriber.php)

TODO: info on translations, revisioning

### Using configuration in Drupal code
It may be useful to access config and text in applanding nodes from elsewhere in Drupal, such as a block linking to the applanding node. 

This can be achieved using the `getAppConfig` method from the `spalp.core` service, as shown in [the build method in the example block](https://git.drupalcode.org/project/spalp/-/blob/2.x/spalp_example/src/Plugin/Block/ExampleBlock.php).

## Known Issues

### [Fatal error while creating app landing page content](https://www.drupal.org/project/drupal/issues/2599228)
With versions of core older than 8.8, it is necessary to apply [this patch](https://www.drupal.org/files/issues/2018-05-17/2599228-51.patch)
