<?php
class JTLConnectorAdmin extends oxAdminView
{ 
	protected $_sThisTemplate = "jtlconnector.tpl";

    private $_errors = array();

	public function render()
	{
		parent::render();

		$oxConfig = \oxRegistry::getConfig();
		include('metadata.php');

        if (version_compare(PHP_VERSION, '5.4') < 0) {
            $this->_aViewData['info'] = '<li class="fail"><b>PHP-Version:</b> '.sprintf('Der Connector benötigt mindestens PHP 5.4. Ihr System läuft auf PHP %s.', PHP_VERSION).'</li>';
        } else {
            $this->_aViewData['info'] = '<li class="pass"><b>PHP-Version:</b> '.PHP_VERSION.'</li>';
        }

        if (!extension_loaded('sqlite3')) {
            $this->_aViewData['info'] .= '<li class="fail"><b>SQLite3:</b> Die benötigte SQLite3 PHP Extension ist nicht installiert.</li>';
        } else {
            $this->_aViewData['info'] .= '<li class="pass"><b>SQLite3:</b> Die benötigte SQLite3 PHP Extension ist installiert.</li>';
        }

        $dbFile = __DIR__.DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.'connector.s3db';
        chmod($dbFile, 0777);
        if (!is_writable($dbFile)) {
            $this->_aViewData['info'] .= sprintf('<li class="fail"><b>Session-DB:</b> Die Datei "%s" muss beschreibbar sein.</li>', $dbFile);
        } else {
            $this->_aViewData['info'] .= sprintf('<li class="pass"><b>Session-DB:</b> Die Datei "%s" ist beschreibbar.</li>', $dbFile);
        }

        $logDir = __DIR__.DIRECTORY_SEPARATOR.'logs';
        chmod($logDir, 0777);
        if (!is_writable($logDir)) {
            $this->_aViewData['info'] .= sprintf('<li class="fail"><b>Logs:</b> Das Verzeichnis "%s" muss beschreibbar sein.', $logDir);
        } else {
            $this->_aViewData['info'] .= sprintf('<li class="pass"><b>Logs:</b> Das Verzeichnis "%s" ist beschreibbar.', $logDir);
        }

        if (count($this->_errors) != 0) {
            $this->_aViewData['info'] = '<li class="fail"><b>'.sprintf('Die Mindest-Voraussetzungen für den Connector sind nicht erfüllt!<br>Bitte lesen Sie den %s für die Installations-Anweisungen.', '<a href="http://guide.jtl-software.de/jtl/JTL-Connector">Connector Guide</a>').'</b></li>' . $this->_aViewData['info'];
        }

		$this->_aViewData['url'] = $oxConfig->getShopURL(null,true).'jtlconnector/';
		$this->_aViewData['pass'] = $oxConfig->getShopConfVar('password', null, 'module:jtl-connector');
		$this->_aViewData['version'] = $aModule['version'];

		return parent::render();
	}
}
