 <?php
/**
 * Model generator file (model.php).
 * 
 * @author OPTeam S.A.
 * @copyright (c) 2011 OPTeam S.A.
 * @package Core
 */
	set_time_limit(2);
 
	define('APPLICATION_ENV', 'DEVELOPMENT'); 
	
	// define the site path
	define('APPLICATION_ROOT', realpath(dirname(__FILE__)));
	
	// add the application to the include path
	set_include_path(APPLICATION_ROOT);
	
	include 'includes/init.php';
	
	spl_autoload_register('__autoload_applications_controller');
	
	global $config;
	
	$config['developer']['toolbar']['enabled'] = false;
	
	System::init();
	System::initDatabase();
	System::initSession();
	System::initProject();
	
	System::$cache = new Cache(cms::$conf['system']['cache']['settings']);
		
	if (User::getIdentity()->is_developer == 1 || User::getIdentity()->is_superuser == 1) {
		
		try {
			
			$contents = '';
			
			$_SESSION['developer-code'] = $_POST['code'];
					
			if ($_POST['code'] === '') {
				unset($_SESSION['developer-code']);
			}
			
			if ($_POST['code'] !== 'help') {
				
				Benchmark::start('eval');
				
				ob_start();
				$result = eval($_POST['code']);
				$contents = ob_get_clean();

				if (strlen($contents) > 0) {
					echo '<pre class="output">';
					echo $contents;
					echo '</pre>';
				}
				
				if ($_SESSION['console_mode'] == 'dump') {
					dump($result);
				}
				elseif ($_SESSION['console_mode'] == 'echo') {
					echo $result;
				}
				elseif ($_SESSION['console_mode'] == 'vdump') {
					dumpv($result);
				}
				elseif ($_SESSION['console_mode'] == 'auto') {
					if ($result !== null) {
						dumpv($result);
					}
				}
				else {
					dumpv($result);
				}
			}
			else {
				echo "
					<b>OPTeam PHP Framework: PHP console</b><br/>
					This console evaluetes entered code and returns result and output buffer <br/>
					1. To see function result type <b>return</b> before function call (ex. return strtolower('ABC');)<br/>
					2. To see function output buffer just call function, buffer will be placed in silver frame above functions result<br/>
					<br/>
					Examples:<br/>
					return 1+1;<br/>
					return strtolower('ABC');<br/>
					echo 'hello world';<br/>
					dump(\$_SESSION);<br/>
					<br/>
					Happy coding :)
				";
			}
		}
		catch (Exception $exception) {
			
				dump("
<b>{$exception->getMessage()}</b>

{$exception->getTraceAsString()}");
			}
	}
	else {
		dump('Acces denied: login as developer or superuser');
	}