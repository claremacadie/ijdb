<?php
//This file contains generic code for accessing websites
//This file changes what is displayed on the webpage based on $route as defined in index.php
//It defines $title and $outut which are used by layout.html.php to display stuff to the webpage
//$routes is an array with all the possible URLs and $route is the actual page the user is on
class EntryPoint
{
	private $route;
	private $routes;
	
	public function __construct($route, $routes)
	{
		$this->route = $route;
		$this->routes = $routes;
		$this->checkUrl();
	}
	
	//This function checks if the URL is correct
	//If $route is not in lowercase, 301 says this is a permanent redirect and redirects to the lowercase version
	// E.g. if someones visits index.php?action=ListJOKES, they will be redirected to index.php?action=listjokes
	//The permanent redirect is important for search engines not to include erroneous pages in their searches
	//Once redirected to index.php, it eventually comes back into this file, 
	//but passes the lowercase test and so does the callAction method below
	
	private function checkUrl()
	{
		if ($this->route !== strtolower($this->route)) {
			http_response_code(301);
			header('location: ' . strtolower($this->route));
		}
	}
	
	//This function includes files from the templates folder
	//The file it includes depends of the $templateFileName given
	//It also extracts (stores) variables that can be used in that file
	private function loadTemplate($templateFileName, $variables = [])
	{
		extract($variables);
		
		
		//ob_start starts a buffer that gets filled by the include file and then output to the website at the end
		ob_start();

		include __DIR__ . '/../templates/' . $templateFileName;
		
		return ob_get_clean();
	}

	public function run()
	{
		//Define $page as the output of callAction
		$page = $this->routes->callAction($this->route);

		//Define $title as whatever is output by the method used above
		$title = $page['title'];
		
		//If $page has defined variables, 
		//pass them to the loadTemplate function (defined above) along with $page['template']
		//$output is used in layout.html.php and sets what is put in the main body of the web page
		if (isset($page['variables'])) {
			$output = $this->loadTemplate($page['template'], $page['variables']);
		//Otherwise, just pass $page['template'] to loadTemplate	
		} else {
			$output = $this->loadTemplate($page['template']);
		}
		
		//This file contains the layout information and uses $title and $output defined above
		include __DIR__ . '/../templates/layout.html.php';
	}
}
