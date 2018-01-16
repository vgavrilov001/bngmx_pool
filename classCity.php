<?php
	class monitoringBNGMX {

		public $cityRus = array(
			'Атырау',
			'Караганда',
			'Усть-Каменогорск',
			'Шымкент',
			'Кокшетау',
			'Астана',
			'Кызылорда',
			'Актау',
			'Актобе',
			'Костанай',
			'Павлодар',
			'Петропавловск',
			'Тараз',
			'Уральск',
			'Семипалатинск',
			'Талдыкорган',
			'Алматы'
		);

		public $koordinati = array(
			'margin-left:205px;margin-top:287px;',
			'margin-left:605px;margin-top:250px;',
			'margin-left:766px;margin-top:232px;',
			'margin-left:527px;margin-top:470px;',
			'margin-left:541px;margin-top:180px;',
			'margin-left:561px;margin-top:220px;',
			'margin-left:437px;margin-top:386px;',
			'margin-left:176px;margin-top:397px;',
			'margin-left:315px;margin-top:242px;',
			'margin-left:416px;margin-top:155px;',
			'margin-left:658px;margin-top:165px;',
			'margin-left:508px;margin-top:110px;',
			'margin-left:589px;margin-top:438px;',
			'margin-left:205px;margin-top:180px;',
			'margin-left:699px;margin-top:214px;',
			'margin-left:708px;margin-top:370px;',
			'margin-left:678px;margin-top:420px;'
		);

		public $cityBras_1 = array(
			'atyr-bngmx-1',
			'kara-bngmx-1',
			'ustk-bngmx-1',
			'shim-bngmx-1',
			'koks-bngmx-1',
			'asta-bngmx-2',
			'kyzy-bngmx-1',
			'akta-bngmx-1',
			'akto-bngmx-1',
			'kost-bngmx-1',
			'pavl-bngmx-1',
			'petr-bngmx-1',
			'tara-bngmx-1',
			'ural-bngmx-1',
			'seme-bngmx-1',
			'tald-bngmx-1',
			'alma-bngmx-1'
		);

		public $cityBras_1_2 = array(
			'atyr-bngmx-1-2',
			'kara-bngmx-1-2',
			'ustk-bngmx-1-2',
			'shim-bngmx-1-2',
			'koks-bngmx-1-2',
			'asta-bngmx-3',
			'kyzy-bngmx-1-2',
			'akta-bngmx-1-2',
			'akto-bngmx-1-2',
			'kost-bngmx-1-2',
			'pavl-bngmx-1-2',
			'petr-bngmx-1-2',
			'tara-bngmx-1-2',
			'ural-bngmx-1-2',
			'seme-bngmx-1-2',
			'tald-bngmx-1-2',
			'alma-bngmx-3'
		);

		public $cityBras_1_3 = array(
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'alma-bngmx-4'
		);

		public $cityBras_1_4 = array(
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'alma-bngmx-6'
		);


		public $cityVal_1 = array();
		public $cityVal_1_2 = array();
		public $cityVal_1_3 = array();
		public $cityVal_1_4 = array();

		public $cityTotl_1 = array();
		public $cityTot_1_2 = array();
		public $cityTot_1_3 = array();
		public $cityTot_1_4 = array();

		public $cityMaxl_1 = array();
		public $cityMax_1_2 = array();
		public $cityMax_1_3 = array();
		public $cityMax_1_4 = array();


		private $dirRead = 'data/stat/';
		private $readFilesAll = array();



		public function __construct() {
			$this->readFilesAll = $this->checkArray();
			//var_dump($this->readFilesAll["ustk-bngmx-1-2"]->{"pool-usage"});
			//$this->xml();


			$this->cityVal_1 = $this->glueArrays($this->cityBras_1,"pool-usage");
			$this->cityVal_1_2 = $this->glueArrays($this->cityBras_1_2,"pool-usage");
			$this->cityVal_1_3 = $this->glueArrays($this->cityBras_1_3,"pool-usage");
			$this->cityVal_1_4 = $this->glueArrays($this->cityBras_1_4,"pool-usage");

			$this->cityTot_1 = $this->glueArrays($this->cityBras_1,"used-addresses");
			$this->cityTot_1_2 = $this->glueArrays($this->cityBras_1_2,"used-addresses");
			$this->cityTot_1_3 = $this->glueArrays($this->cityBras_1_3,"used-addresses");
			$this->cityTot_1_4 = $this->glueArrays($this->cityBras_1_4,"used-addresses");

			$this->cityMax_1 = $this->glueArrays($this->cityBras_1,"total-addresses");
			$this->cityMax_1_2 = $this->glueArrays($this->cityBras_1_2,"total-addresses");
			$this->cityMax_1_3 = $this->glueArrays($this->cityBras_1_3,"total-addresses");
			$this->cityMax_1_4 = $this->glueArrays($this->cityBras_1_4,"total-addresses");

			/*var_dump($this->cityVal_1);
			var_dump($this->cityVal_1_2);
			var_dump($this->cityVal_1_3);*/

		}

		/*public function xml() {
			$filesArray = shell_exec("find ".$this->dirRead." -type f -iname '*.xml'");
			$filesArray = explode("\n",trim($filesArray));
			foreach($filesArray as $val) {
				$xml[] = simplexml_load_file($val);
			}
			var_dump(count($xml[0]->{"aaa-module-address-assignment-pool-statistics"}));
		}*/


		private function glueArrays($readArray,$keyparam) {
			//var_dump($readArray);
			foreach($readArray as $key=>$val) {
				if(isset($this->readFilesAll[$val])) {
					$result[] = (string)$this->readFilesAll[$val]->{$keyparam};
				} else {
					$result[] = "";
				}
			}
			return $result;
		}





		private function readDir() {
			$filesArray = shell_exec("find ".$this->dirRead." -type f -iname '*.xml'");
			return explode("\n",trim($filesArray));
		}

		private function checkArray() {

			function clearArray($array) {
				foreach($array->{"aaa-module-address-assignment-pool-statistics"} as $key=>$val) {
					if($val->{"pool-name"}=="(all pools in chain)") {
						$result = $val;
					}
				}

				return $result;
			}

			$arrayFiles = $this->readDir();
			$params=array();
			foreach($arrayFiles as $key=>$val) {
				if(file_exists($val)) {
					$newKey = basename($val, ".xml");
					$params[$newKey] = clearArray(simplexml_load_file($val));
				}
			}
			return $params;
		}




	}



	$monitoringBNGMX = new monitoringBNGMX;
?>
