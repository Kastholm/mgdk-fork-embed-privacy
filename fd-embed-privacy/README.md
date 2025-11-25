# DEPRECATED

Logic is now part of https://github.com/funke-pe/fd-og-embed-privacy

# fd-embed-privacy

This plugin is an extension to the regular vanillia [Embed Privcy plugin](https://wordpress.org/plugins/embed-privacy/). It needs to be installed parallel to this plugin.

It will modify the plugin output to contain a footer section below the privacy overlay and the embed to enable/disable the embed and a link to the privacy statement.

## Setup

### CSS

The footer section might need additional CSS in the Customizer. See [customizer-css.md](./customizer-css.md) for further information and code.

### Perfmatters

You must add to _Exclude from Deferral_

```
embed-privacy.min.js
disable-perfmatters
``` 

and to _Excluded from Delay_.

```
disable-perfmatters
``` 

The entry ```_static/``` is not allowed in _Excluded from Delay_.

## i18n

The plugin uses WPs i18n mechanismen to set the text for the overlay text. As a result, the text for embed provider set in the Embed-Privacy-Plugin settings will be ignored.
