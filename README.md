# TemplateEngine

A simple template engine for PHP.

## Versions

Until version <code>0.4.1</code>, support for PHP 7.4+ and CeusMedia::Common 0.9+.  
This version <code>0.5.0</code>, support for PHP 8.1+ and CeusMedia::Common 1.0+.  

## Facts
- **logic-less** - no conditions or loop
  - **conditions** are done by <code>Optional</code> and <code>Inclusion</code> plugin
  - **loops** are done by assigning sub templates on lists
- **filters** - apply filters before and after
- **plugins** - functionality is extendable by plugins
  - **includes** - include nested templates
  - **comments** - enable developer comments outside production 
  - **optional** - ...
  
