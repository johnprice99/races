<?php
namespace JP\RaceBundle\Twig;

class DistanceExtension extends \Twig_Extension {

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('raceDistance', array($this, 'raceDistance')),
		);
	}

	private function getDivision($n, $d) {
		return array(
			number_format($n / $d),
			$n % $d
		);
	}

	public function raceDistance($distance) {
		$yardsInMile = 1760;
		$yardsInFurlong = 220;

		if ($distance >= $yardsInMile) {
			$result = $this->getDivision($distance, $yardsInMile);
			$miles = $result[0];
			$result = $this->getDivision($result[1], $yardsInFurlong);
			$furlongs = $result[0];
			$yards = $result[1];
		}
		else {
			$result = $this->getDivision($distance, $yardsInFurlong);
			$furlongs = $result[0];
			$yards = $result[1];
		}

		$returnStr = '';
		if (isset($miles) && $miles) {
			$returnStr .= $miles.'m ';
		}
		if ($furlongs) {
			$returnStr .= $furlongs.'f ';
		}
		if ($yards) {
			$returnStr .= $yards.'y';
		}

		return $returnStr;
	}

	public function getName() {
		return 'distance_extension';
	}
}