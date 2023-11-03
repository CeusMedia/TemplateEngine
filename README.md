# TemplateEngine

A simple template engine for PHP.

![Branch](https://img.shields.io/badge/Branch-0.5.x-blue?style=flat-square)
![Release](https://img.shields.io/badge/Release-0.5.1-blue?style=flat-square)
![PHP version](https://img.shields.io/badge/PHP-%5E8.1-blue?style=flat-square&color=777BB4)
![PHPStan level](https://img.shields.io/badge/PHPStan_level-max+strict-darkgreen?style=flat-square)
[![Total downloads](http://img.shields.io/packagist/dt/ceus-media/template-engine.svg?style=flat-square)](https://packagist.org/packages/ceus-media/common)
[![Package version](http://img.shields.io/packagist/v/ceus-media/template-engine.svg?style=flat-square)](https://packagist.org/packages/ceus-media/common)
[![License](https://img.shields.io/packagist/l/ceus-media/template-engine.svg?style=flat-square)](https://packagist.org/packages/ceus-media/common)

## Versions

Until version <code>0.4.1</code>, support for PHP 7.4+ and CeusMedia::Common 0.9+.  
This version <code>0.5.1</code>, support for PHP 8.1+ and CeusMedia::Common 1.0+.  

## Facts
- **logic-less** - no conditions or loop
  - **conditions** are done by <code>Optional</code> and <code>Inclusion</code> plugin
  - **loops** are done by assigning sub templates on lists
- **filters** - apply filters before and after
- **plugins** - functionality is extendable by plugins
  - **includes** - include nested templates
  - **comments** - enable developer comments outside production 
  - **optional** - ...
  
