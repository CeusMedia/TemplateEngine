<?php
require_once '../vendor/autoload.php';

use CeusMedia\TemplateEngine as T;
use CeusMedia\TemplateEngine\Template;

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
	print '<pre>'.htmlentities( $template1->getTemplate() ).'</pre>';

	print '<h4>PHP code</h4>';
	print 'After loading the template engine with:';
	print '<pre>use CeusMedia\TemplateEngine\Template;</pre>';
	print 'And having some data to assign, like:';
	print '<pre>$date = date( \'Y-m-d\' );</pre>';
	print 'And applying this PHP code to the template:';
	print '<pre>print new Template( \'template.date.tmpl\', array( \'date\' => $date ) );</pre>';

	print '<h4>HTML output</h4>';
	print 'Will create this HTML output:';
	print '<pre>'.htmlentities( $output1 ).'</pre>';
	print '<h4>PHP code in long and explained</h4>';
	print '<pre>$template  = new Template();				// create template instance
$template->setTemplate( \'template.date.tmpl\' );	// load template from file "template.tmpl"
$template->addElement( \'date\', $date );			// assign data element to template
print $template->render();				// render template and print it out
</pre>';
	print '<h4>Static call <span class="muted"></span></h4>';
	print '<pre>print Template::renderFile( \'template.date.tmpl\', array( \'date\' => $date ) );</pre>';


	$data		= array( 'date' => $date, 'time' => $time );
	$template2	= new Template( 'template.page.tmpl', $data );
	$template2->addPlugin( new \CeusMedia\TemplateEngine\Plugin\Inclusion() );

	print '<h3>Plugins</h3>';
	print '<h4>Inclusion</h4>';
	print 'Presume having this template code stored in <code>template.page.html</code>:';
	print '<pre>'.htmlentities( $template2->getTemplate() ).'</pre>';
	print 'And having some data to assign, like:';
	print '<pre>$data = array(
	\'data\'	=> date( \'Y-m-d\' ),
	\'time\'	=> date( \'H:i:s\' )
);</pre>';
	print 'And applying this PHP code to the template:';
	print '<pre>$template = new Template( \'template.date.tmpl\', $data );</pre>';
	print 'And, this time, activating plugin <code>Inclusion</code>:';
	print '<pre>$template->addPlugin( new \CeusMedia\TemplateEngine\Plugin\Inclusion() );</pre>';
	print 'Will create this HTML output:';
	print '<pre>'.htmlentities( $template2->render() ).'</pre>';

}
catch( Exception $e ){
	UI_HTML_Exception_Page::display( $e );
	exit;
}

$body	= '<div class="container">
<h1 class="muted">CeusMedia Component Demo</h1>
<h2>TemplateEngine</h2>
'.ob_get_clean().'
</div>';

$page	= new UI_HTML_PageFrame();
$page->addStylesheet( "http://cdn.int1a.net/css/bootstrap.min.css" );
$page->addJavaScript( "http://cdn.int1a.net/js/jquery/1.10.2.min.js" );
$page->addJavaScript( "http://cdn.int1a.net/js/bootstrap.min.js" );
$page->addBody( $body );
print $page->build();
