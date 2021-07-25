<?php
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.');

use CeusMedia\TemplateEngine\Template;

error_reporting( E_ALL );
ini_set( 'display_errors', TRUE );

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
	print '<pre class="code-php">'.htmlentities( $template1->getTemplate() ).'</pre>';

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
$template->setTemplate( \'template.date.tmpl\' );		// load template from file "template.tmpl"
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
	print '<pre class="code-php">$data = array(
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
<script src="https://pagecdn.io/lib/ace/1.4.12/ace.min.js" crossorigin="anonymous" integrity="sha256-T5QdmsCQO5z8tBAXMrCZ4f3RX8wVdiA0Fu17FGnU1vU=" ></script>
<h1 class="muted">CeusMedia Component Demo</h1>
<h2>TemplateEngine</h2>
'.ob_get_clean().'
</div>
<style type="text/css" media="screen">
.code-php {}
</style>
<script>
jQuery("######pre.code-php").each(function(){
	console.log(this);
	var editor = ace.edit($(this).get(0));
	editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/javascript");
	editor.setTheme("ace/theme/github");

	editor.session.setMode("ace/mode/php");
});
</script>';

$page	= new UI_HTML_PageFrame( 'HTML_5' );
$page->addStylesheet( "https://cdn.ceusmedia.de/css/bootstrap.min.css" );
$page->addJavaScript( "https://cdn.ceusmedia.de/js/bootstrap.min.js" );
$page->addJavaScript( "https://cdn.ceusmedia.de/js/jquery/1.10.2.min.js" );

/*
$page->addJavaScript( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.0/ace.js' );
$page->addJavaScript( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js' );
$page->addJavaScript( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-html.min.js' );
$page->addJavaScript( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-php.min.js' );
$page->addJavaScript( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-github.min.js' );
*/

$page->addBody( $body );
print $page->build();
