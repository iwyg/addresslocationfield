<?php

	Class extension_addresslocationfield extends Extension{

		public function about()
		{
			return array(
				'name' => 'Field: Address Location',
				'version' => '1.01 beta',
				'release-date' => '2010-01-04',
				'author' => array(
					'name' => 'John Porter',
					'website' => 'http://designermonkey.co.uk',
					'email' => 'contact@designermonkey.co.uk'
				)
			);
		}

		public function uninstall()
		{
			Symphony::Database()->query("DROP TABLE `tbl_fields_addresslocation`");
		}

		public function install()
		{
			return Symphony::Database()->query("CREATE TABLE `tbl_fields_addresslocation` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`field_id` int(11) unsigned NOT NULL,
				`street_label` varchar(80) NOT NULL,
				`city_label` varchar(80) NOT NULL,
				`region_label` varchar(80) NOT NULL,
				`postal_code_label` varchar(80) NOT NULL,
				`country_label` varchar(80) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `field_id` (`field_id`)
			) TYPE=MyISAM");
		}

		/*
			Modified from:
			http://www.kevinbradwick.co.uk/developer/php/free-to-script-to-calculate-the-radius-of-a-coordinate-using-latitude-and-longitude
		*/
		public function geoRadius($lat, $lng, $rad, $kilometers=false)
		{
			$radius = ($kilometers) ? ($rad * 0.621371192) : $rad;

			(float)$dpmLAT = 1 / 69.1703234283616;

			// Latitude calculation
			(float)$usrRLAT = $dpmLAT * $radius;
			(float)$latMIN = $lat - $usrRLAT;
			(float)$latMAX = $lat + $usrRLAT;

			// Longitude calculation
			(float)$mpdLON = 69.1703234283616 * cos($lat * (pi/180));
			(float)$dpmLON = 1 / $mpdLON; // degrees per mile longintude
			$usrRLON = $dpmLON * $radius;

			$lonMIN = $lng - $usrRLON;
			$lonMAX = $lng + $usrRLON;

			return array("lonMIN" => $lonMIN, "lonMAX" => $lonMAX, "latMIN" => $latMIN, "latMAX" => $latMAX);
		}

		/*
		Calculate distance between two lat/long pairs
		*/
		public function geoDistance($lat1, $lon1, $lat2, $lon2, $unit)
		{
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;

			$unit = strtolower($unit);

			$distance = 0;

			if ($unit == "k") {
				$distance = ($miles * 1.609344);
			} else if ($unit == "n") {
				$distance = ($miles * 0.8684);
			} else {
				$distance = $miles;
			}

			return round($distance, 1);

		}

	}

?>