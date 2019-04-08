<?php
//This file contains generic code for accessing websites
//This file changes what is displayed on the webpage based on $route, $method and $routes as defined in index.php
//It defines $title and $outut which are used by layout.html.php to display stuff to the webpage
//$routes is an array with all the possible URLs and $route is the actual page the user is on

//namespace is like a folder and gives classes unique names, in case another developed creates an EntryPoint class
namespace Ninja;

class EntryPoint
{
	private $route;
	private $method;
	private $routes;
	
	public function __construct($route, $method, $routes)
	{
		$this->route = $route;
		$this->method = $method;
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

		include __DIR__ . '/../../templates/' . $templateFileName;
		
		return ob_get_clean();
	}

	//This method defines $title and $output dependent on $routes
	//$routes is created by getRoutes, which is defined in IjdbRoutes
	//$routes is basically the method (_GET or _POST) and the URL
	public function run()
	{
		//Define $routes as the output of getRoutes
		$routes = $this->routes->getRoutes();
		
		//Define $controller dependent on $routes
		$controller = $routes[$this->route][$this->method]['controller'];
		
		//Define $action dependent on $routes
		$action = $routes[$this->route][$this->method]['action'];

		//Define $page dependent on the method and URL
		$page = $controller->$action();
				
		//Define $title as whatever is output by $page
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
		include __DIR__ . '/../../templates/layout.html.php';
	}
}
