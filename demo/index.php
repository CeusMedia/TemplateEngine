<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.');

use CeusMedia\Common\UI\HTML\PageFrame as HtmlPage;
use CeusMedia\Common\UI\HTML\Exception\Page as HtmlExceptionPage;

use CeusMedia\TemplateEngine\Plugin\Inclusion as InclusionPlugin;
use CeusMedia\TemplateEngine\Template;

error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

$date		= date( 'Y-m-d' );
$time		= date( 'H:i:s' );

ob_start();
try{
	print '<h3>Basics</h3>';
	$template1	= new Template();
	$template1->setTemplate( 'template.date.tmpl' );
	$template1->addElement( 'date', $date );
	$output1	= $template1->render();

	print '<h4>Template content</h4>';
	print 'Presume having this template code stored in <code>template.date.tmpl</code>:';
	print '<pre class="code-php">'.htmlentities( (string) $template1->getTemplate() ).'</pre>';

	print '<h4>PHP code</h4>';
	print 'After loading the template engine with:';
	print '<pre>use CeusMedia\TemplateEngine\Template;</pre>';
	print 'And having some data to assign, like:';
	print '<pre>$date = date( \'Y-m-d\' );</pre>';
	print 'And applying this PHP code to the template:';
	print '<pre>print new Template( \'template.date.tmpl\', [\'date\' => $date] );</pre>';

	print '<h4>HTML output</h4>';
	print 'Will create this HTML output:';
	print '<pre>'.htmlentities( $output1 ).'</pre>';
	print '<h4>PHP code in long and explained</h4>';
	print '<pre>$template  = new Template();				// create template instance
$template->setTemplate( \'template.date.tmpl\' );		// load template from file "template.tmpl"
$template->addElement( \'date\', $date );			// assign data element to template
print $template->render();				// render template and print it out
</pre>';
	print '<h4>Static call <span class="muted"></span></h4>';
	print '<pre>print Template::renderFile( \'template.date.tmpl\', [\'date\' => $date] );</pre>';


	$data		= ['date' => $date, 'time' => $time];
	$template2	= new Template( 'template.page.tmpl', $data );
	Template::addPlugin( new InclusionPlugin() );

	print '<h3>Plugins</h3>';
	print '<h4>Inclusion</h4>';
	print 'Presume having this template code stored in <code>template.page.html</code>:';
	print '<pre>'.htmlentities( (string) $template2->getTemplate() ).'</pre>';
	print '<div class="row-fluid"><div class="span6">';
	print 'And <code>template.date.html</code>:<pre>'.htmlentities( (string) file_get_contents( 'template.date.tmpl' ) ).'</pre></div>';
	print '<div class="span6">And <code>template.time.html</code>:<pre>'.htmlentities( (string) file_get_contents( 'template.time.tmpl' ) ).'</pre></div></div>';
	print 'And having some data to assign, like:';
	print '<pre class="code-php">$data = [
	\'data\'	=> date( \'Y-m-d\' ),
	\'time\'	=> date( \'H:i:s\' )
];</pre>';
	print 'And applying this PHP code to the template:';
	print '<pre>$template = new Template( \'template.date.tmpl\', $data );</pre>';
	print 'And, this time, activating plugin <code>Inclusion</code>:';
	print '<pre>Template::addPlugin( new \CeusMedia\TemplateEngine\Plugin\Inclusion() );</pre>';
	print 'Will create this HTML output:';
	print '<pre>'.htmlentities( $template2->render() ).'</pre>';

}
catch( Exception $e ){
	HtmlExceptionPage::display( $e );
	exit;
}

$body	= '<div class="container">
<h1 class="muted">CeusMedia Component Demo</h1>
<h2>TemplateEngine</h2>
'.ob_get_clean().'
</div>
<style type="text/css" media="screen">
.code-php {}
</style>
';

$page	= new HtmlPage( 'HTML_5' );
$page->addStylesheet( "https://cdn.ceusmedia.de/css/bootstrap.min.css" );
$page->addJavaScript( "https://cdn.ceusmedia.de/js/bootstrap.min.js" );
$page->addJavaScript( "https://cdn.ceusmedia.de/js/jquery/1.10.2.min.js" );

$page->addBody( $body );
print $page->build();
