<?php
function example_page_before( $FlatSite ){

	if(isset($FlatSite->page)){
		$FlatSite->page->social = 'Testing';
	}
	echo 'Here in example module!';

}