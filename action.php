<?php
	ini_set("error_reporting", E_ALL);
    date_default_timezone_set('Asia/Jakarta');

    require_once __DIR__ . '/vendor/autoload.php';
    
    // setup Propel
    require_once __DIR__ . '/generated-conf/config.php';

    use Models\Config;
    use Models\ConfigQuery;
    use Models\Build;
    use Models\BuildQuery;
    use Models\Email;
    use Models\EmailQuery;

	
	use Propel\Runtime\Propel;
    use Monolog\Logger;
	use Monolog\Handler\StreamHandler;

	//$logger = new Logger('defaultLogger');
	//$logger->pushHandler(new StreamHandler('/Users/decky/Documents/php/release-view/propel.log'));
	//Propel::getConnection()->useDebug(true);
	//Propel::getServiceContainer()->setLogger('defaultLogger', $logger);

	class Action {
	    const PRICE_BUTTER  = 1.00;
	    const PRICE_MILK    = 3.00;
	    const PRICE_EGGS    = 6.95;

	    protected $action 	= '';
	    protected $success 	= false;
	    protected $data 	= array();
	    protected $message 	= '';
	    
	    protected function auth() {
	    	$chalenge 			   	= $_POST['authkey'];

	    	$authkey 				= Config::GetConfig('authkey');
	    	$this->message 			= 'Invalid AUTHKEY';
	    	if ($chalenge == $authkey) {
	    		$this->success = true;
	    		$this->message = '';
	    	}
	        $this->data['authkey'] 	= $chalenge;
	    }

	    protected function build_list() {		    
		    $builds = BuildQuery::Create()
		        ->orderById('DESC')
		        ->find()
		        ->toArray();
		    $this->data['builds'] = $builds;
		    $this->success = true;
		}

	    protected function build_submit() {
	    	$id = $_POST['build-id'];
	    	if (empty($id)) {
	    		$build = new Build();
				$build->setName($_POST['build-name']);
				$build->setVersion($_POST['build-version']);
				$build->setUrl($_POST['build-url']);
				$build->setNote($_POST['build-note']);
				$build->save();
	    	} else {
	    		$build = BuildQuery::Create()->findPK($id);
			    $build->setName($_POST['build-name']);
				$build->setVersion($_POST['build-version']);
				$build->setUrl($_POST['build-url']);
				$build->setNote($_POST['build-note']);
				$build->save();
	    	}
	    	$this->success = true;
		}

		protected function build_remove() {
			$id = $_POST['remove-id'];
			$build = BuildQuery::Create()->findPK($id);
			$build->delete();
			$this->success = true;
		}

		protected function build_upload() {
			$storage = new \Upload\Storage\FileSystem(__DIR__ . '/builds/');
			$file = new \Upload\File('build-file', $storage);

			// Optionally you can rename the file on upload
			$new_filename = uniqid();
			$file->setName($new_filename);

			// Validate file upload
			// MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
			$file->addValidations(array(
			    // Ensure file is of type "image/png"
			    //new \Upload\Validation\Mimetype('image/png'),

			    //You can also add multi mimetype validation
			    //new \Upload\Validation\Mimetype(array('image/png', 'image/gif'))

			    // Ensure file is no larger than 5M (use "B", "K", M", or "G")
			    //new \Upload\Validation\Size('5M')
			));

			// Access data about the file that has been uploaded
			$data = array(
			    'name'       => $file->getNameWithExtension(),
			    'extension'  => $file->getExtension(),
			    'mime'       => $file->getMimetype(),
			    'size'       => $file->getSize(),
			    'md5'        => $file->getMd5(),
			    'dimensions' => $file->getDimensions(),
			    'url'		 => 'builds/' . $file->getNameWithExtension()
			);

			// Try to upload file
			try {
			    // Success!
			    $file->upload();
			    $this->success = true;
			    $this->data['upload'] = $data;
			} catch (\Exception $e) {
			    // Fail!
			    $errors = $file->getErrors();
			    $this->data['message'] 	= $errors;
			}
		}

		protected function email_list() {		    
		    $emails = EmailQuery::Create()
		        ->orderById('DESC')
		        ->find()
		        ->toArray();
		    $this->data['emails'] = $emails;
		    $this->success = true;
		}

	    protected function email_submit() {
	    	$id = $_POST['email-id'];
	    	if (empty($id)) {
	    		$email = new Email();
				$email->setEmail($_POST['email-name']);
				$email->save();
	    	} else {
	    		$email = EmailQuery::Create()->findPK($id);
			    $email->setEmail($_POST['email-name']);
				$email->save();
	    	}
	    	$this->success = true;
		}

		protected function email_remove() {
			$id = $_POST['remove-id'];
			$email = EmailQuery::Create()->findPK($id);
			$email->delete();
			$this->success = true;
		}

		protected function config_list() {		    
		    $configs = ConfigQuery::Create()
		        ->orderById('DESC')
		        ->find()
		        ->toArray();
		    $this->data['configs'] = $configs;
		    $this->success = true;
		}

	    protected function config_submit() {
	    	$id = $_POST['config-id'];
	    	if (empty($id)) {
	    		$config = new Config();
				$config->setName($_POST['config-name']);
				$config->setValue($_POST['config-value']);
				$config->setType($_POST['config-type']);
				$config->setEnabled($_POST['config-enabled']? 1:0);
				$config->save();
	    	} else {
	    		$email = ConfigQuery::Create()->findPK($id);
			    $config->setName($_POST['config-name']);
				$config->setValue($_POST['config-value']);
				$config->setType($_POST['config-type']);
				$config->setEnabled($_POST['config-enabled']? 1:0);
				$config->save();
	    	}
	    	$this->success = true;
		}

		protected function config_remove() {
			$id = $_POST['remove-id'];
			$config = Config::Create()->findPK($id);
			$config->delete();
			$this->success = true;
		}

	    function __construct() {
			$this->action = $_POST['action'];
			$this->{$this->action}();

			$this->data['action'] 	= $this->action;
			$this->data['message'] 	= $this->message;
			echo( json_encode(array('success' => $this->success, 'data' => $this->data)) );
	    }
	}

	new Action();
?>