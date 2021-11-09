<?php

/**
 * Class simpleQueueMainController
 */
abstract class simpleQueueMainController extends modExtraManagerController {
	/** @var simpleQueue $simpleQueue */
	public $simpleQueue;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('simplequeue_core_path', null, $this->modx->getOption('core_path') . 'components/simplequeue/');
		require_once $corePath . 'model/simplequeue/simplequeue.class.php';

		$this->simpleQueue = new simpleQueue($this->modx);
		//$this->addCss($this->simpleQueue->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->simpleQueue->config['jsUrl'] . 'mgr/simplequeue.js');
		$this->addHtml('
		<script type="text/javascript">
			simpleQueue.config = ' . $this->modx->toJSON($this->simpleQueue->config) . ';
			simpleQueue.config.connector_url = "' . $this->simpleQueue->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('simplequeue:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends simpleQueueMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}