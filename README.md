# TemplateEngine

A simple template engine for PHP.

![Branch](https://img.shields.io/badge/Branch-0.6.x-blue?style=flat-square)
![Release](https://img.shields.io/badge/Release----blue?style=flat-square)
![PHP version](https://img.shields.io/badge/PHP-%5E8.3-blue?style=flat-square&color=777BB4)
![PHPStan level](https://img.shields.io/badge/PHPStan_level-max+strict-darkgreen?style=flat-square)
[![Total downloads](http://img.shields.io/packagist/dt/ceus-media/template-engine.svg?style=flat-square)](https://packagist.org/packages/ceus-media/template-engine)
[![Package version](http://img.shields.io/packagist/v/ceus-media/template-engine.svg?style=flat-square)](https://packagist.org/packages/ceus-media/template-engine)
[![License](https://img.shields.io/packagist/l/ceus-media/template-engine.svg?style=flat-square)](https://packagist.org/packages/ceus-media/template-engine)

## Versions

Until version <code>0.4.1</code>, support for PHP 7.4+ and CeusMedia::Common 0.9+.  
Until version <code>0.5.1</code>, support for PHP 8.1+ and CeusMedia::Common 1.0+.  
This version <code>0.6.x</code>, support for PHP 8.3+ and CeusMedia::Common 1.1+.  

## Facts
- **logic-less** - no conditions or loop
  - **conditions** are done by <code>Optional</code> and <code>Inclusion</code> plugin
  - **loops** are done by assigning sub templates on lists
- **filters** - apply filters before and after
- **plugins** - functionality is extendable by plugins
  - **includes** - include nested templates
  - **comments** - enable developer comments outside production
  - **optional** - conditionally render placeholders

## Template Syntax

### Placeholders
- <code>&lt;%tag%&gt;</code> — mandatory placeholder; throws an exception if not filled
- <code>&lt;%?tag%&gt;</code> — optional placeholder; silently removed if not filled

### Filters
Apply a filter to a placeholder by appending the filter keyword and arguments:
- <code>&lt;%tag:escape%&gt;</code> — escapes HTML entities (prevents XSS)
- <code>&lt;%tag:code:php%&gt;</code> — wraps output in <code>&lt;class="php"&gt;</code>
- <code>&lt;%tag:code:highlight%&gt;</code> — highlights PHP code using <code>highlight_string()</code>
- <code>&lt;%tag:code:json%&gt;</code> — pretty-prints JSON
- <code>&lt;%tag:rot13%&gt;</code> — applies ROT13 encoding
- <code>&lt;%tag:strip:tags%&gt;</code> — strips HTML tags
- <code>&lt;%tag:strip:space%&gt;</code> — normalizes whitespace
- <code>&lt;%tag:strip:comments%&gt;</code> — strips HTML comments
- <code>&lt;%tag:strip:styles%&gt;</code> — strips <code>&lt;style&gt;</code> blocks
- <code>&lt;%tag:strip:scripts%&gt;</code> — strips <code>&lt;script&gt;</code> blocks

### Combining Filters

Multiple filters can be chained using the pipe <code>|</code> separator. Filters are applied left to right — each filter's output becomes the next filter's input.

- <code>&lt;%tag|escape|strip:space%&gt;</code> — escape HTML entities, then normalize whitespace
- <code>&lt;%tag|strip:tags|escape%&gt;</code> — remove HTML tags first, then escape the remaining content
- <code>&lt;%tag|escape:ISO-8859-1|rot13%&gt;</code> — escape with custom encoding, then apply ROT13
- <code>&lt;%tag|escape|code:php%&gt;</code> — escape content, then wrap in a <code>&lt;code&gt;</code> block

**Order matters.** For example, <code>&lt;%tag|escape|strip:tags%&gt;</code> will escape first (converting <code>&lt;</code> to <code>&amp;lt;</code>), so <code>strip:tags</code> won't find actual tags to remove. Conversely, <code>&lt;%tag|strip:tags|escape%&gt;</code> removes tags first, then escapes the result — which is typically the desired behavior for user input.

### Plugins
- <code>&lt;%include(path/to/template.tmpl)%&gt;</code> — includes a nested template file
- <code>&lt;%file(path/to/file.txt)%&gt;</code> — reads and inserts a file's content
- <code>&lt;%matrix(key:depth:table)%&gt;</code> — looks up a value in a key-value table
- <code>&lt;%optional(tag)%&gt;</code> — conditionally renders a placeholder
- <code>&lt;%--comment--%&gt;</code> — template comments (removed by the Comments plugin)

### Comments
Tagged comments can embed metadata for elements:
```html
&lt;%--form--%&gt;
    @action /submit
    @method post
&lt;/%--%&gt;
```

## Usage

### Basic rendering
```php
use CeusMedia\TemplateEngine\Template;

$template = new Template( 'path/to/template.html', [
    'title' => 'Hello World',
    'name'  => 'John Doe',
]);
echo $template->render();
```

### Static rendering
```php
use CeusMedia\TemplateEngine\Template;

$html = Template::renderString( '&lt;%title%&gt; &lt;%name%&gt;', [
    'title' => 'Mr',
    'name'  => 'Smith',
]);
```

### Using filters
```php
// In template: &lt;%userInput:escape%&gt;
$template = new Template( 'page.html', [
    'userInput' => '&lt;script>alert("xss")&lt;/script>',
]);
// Output: &amp;lt;script&amp;gt;alert(&quot;xss&quot;)&amp;lt;/script&amp;gt;
```

### Registering plugins and filters
```php
use CeusMedia\TemplateEngine\Template;
use CeusMedia\TemplateEngine\Plugin\Comments;
use CeusMedia\TemplateEngine\Filter\Escape;

Template::addPlugin( new Comments() );
Template::addFilter( new Escape() );
```

### Caching
```php
use CeusMedia\TemplateEngine\Template;
use Psr\SimpleCache\CacheInterface;

$template = new Template();
$template->setCache( $cacheInstance, 'my_prefix_' );
```

## Built-in Plugins

| Plugin | Description |
|---|---|
| `Comments` | Removes or processes `<%--comment--%>` blocks |
| `File` | Reads external files via `<%file(path)%>` tags |
| `Inclusion` | Includes nested templates via `<%include(path)%>` tags |
| `Matrix` | Key-value lookups via `<%matrix(key:depth:table)%>` tags |
| `Optional` | Conditionally renders placeholders |
| `Tidy` | Cleans HTML output using the Tidy extension |

## Built-in Filters

| Filter | Keywords | Description |
|---|---|---|
| `Escape` | `escape`, `htmlentities` | Escapes HTML entities to prevent XSS |
| `Code` | `code` | Wraps content in `<code>`, highlights PHP, or pretty-prints JSON |
| `Rot13` | `rot13` | Applies ROT13 encoding |
| `Strip` | `strip` | Strips HTML tags, whitespace, comments, styles, or scripts |

## Security Notes

- The engine does **not** automatically escape output. Use the `escape` filter for user-controlled data to prevent XSS.
- The `File` plugin validates file paths to prevent directory traversal attacks.
  
