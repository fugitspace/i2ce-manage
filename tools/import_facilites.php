#!/usr/bin/php
<?php
/*
 * © Copyright 2007, 2008 IntraHealth International, Inc.
 * 
 * This File is part of iHRIS
 * 
 * iHRIS is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * The page wrangler
 * 
 * This page loads the main HTML template for the home page of the site.
 * @package iHRIS
 * @subpackage iHRISGambia
 * @access public
 * @author Sovello HIldebrand
 * @copyright Copyright &copy; 2019
 */

require_once("./import_base.php");



/*********************************************
 *
 *      Process Class
 *
 *********************************************/

class FacilityImporter extends Processor {

    protected static $date_format ='Y-m-d' ;//YYYY-MM-DD


    protected function getExpectedHeaders() {
        return  array(
                'country'=>'Country',
                'region'=>'Region',
                'district'=>'District',
                'facility'=>'Facility'
            );
        
    }
    /**
     * map expected header references to header columns in the data file 
     */
    protected function mapHeaders() {
        $headers = $this->dataFile->getHeaders();
        $this->header_map = $this->getHeaderMap($headers, $this->getExpectedHeaders());
    }


    protected $countries = array();
    protected $regions = array();
    protected $districts = array();
    protected $facilities = array();


    protected function _processRow() {
        if(!array_key_exists('Gambia', $this->countries)){
            $countryObj = $this->ff->createContainer('country');
            $countryObj->name = "Gambia";
            // $countryId = $this->save($countryObj);
            $this->countries['Gambia'] = 'country|GM';
        }
        $region = trim($this->mapped_data['region']);
        $district = trim($this->mapped_data['district']);
        $facility = trim($this->mapped_data['facility']);
        //save region if not in array already
        if (!array_key_exists($region, $this->regions)) {
            $regionObj = $this->ff->createContainer('region');
            $regionObj->name = $region;
            $regionObj->country = array('country', $this->countries['Gambia']);
            $regionId = $this->save($regionObj);
            $this->regions[$region] = $regionId;
            $regionObj->cleanup();
        }

        //district
        if (!array_key_exists($district, $this->districts)) {
            $districtObj = $this->ff->createContainer('district');
            $districtObj->name = $district;
            $districtObj->region = array('region', $this->regions[$region]);
            $districtId = $this->save($districtObj);
            $this->districts[$district] = $districtId;
            $districtObj->cleanup();
        }

        //facility
        if (!array_key_exists($facility, $this->facilities)) {
            $facilityObj = $this->ff->createContainer('facility');
            $facilityObj->name = $facility;
            $facilityObj->region = array('district', $this->districts[$district]);
            $facilityId = $this->save($facilityObj);
            $this->facilities[$facility] = $facilityId;
            $facilityObj->cleanup();
        }
        return true;
    }


}




/*********************************************
 *
 *      Execute!
 *
 *********************************************/

if (count($arg_files) != 1) {
    usage("Please specify the name of a spreadsheet to process");
}
reset($arg_files);
$file = current($arg_files);
if($file[0] == '/') {
    $file = realpath($file);
} else {
    $file = realpath($dir. '/' . $file);
}
if (!is_readable($file)) {
    usage("Please specify the name of a spreadsheet to import: " . $file . " is not readable");
}

I2CE::raiseMessage("Loading from $file");


$processor = new FacilityImporter($file);
$processor->run();

echo "Processing Statistics:\n";
print_r( $processor->getStats());




# Local Variables:
# mode: php
# c-default-style: "bsd"
# indent-tabs-mode: nil
# c-basic-offset: 4
# End:
