<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
// Function VOC
class volvooncall_api
{

    private $VocUsername;
    private $VocPassword;
    private $VocRegion;

    public function switchRegion(){
        switch ($this->VocRegion) {
            case 'eu':
                $domain = '';
                break;
            case 'na':
                $domain = '-na';
                break;
            case 'ch':
                $domain = '-ch';
                break;
            default:
                $domain = '';
        }
        return $domain;
    }

    private $api_url = 'https://vocapi.wirelesscar.net/customerapi/rest/v3.0';


    private $ACCOUNT = '/customeraccounts';
    private $ACCOUNT_RELATION = '/vehicle-account-relations/';
    private $VEHICLE = '/vehicles/';

    function login($VocUsername, $VocPassword)
    {
      $this->VocUsername = $VocUsername;
      $this->VocPassword = $VocPassword;

      if (isset($this->VocUsername) && isset($this->VocPassword))
      return true;
    }

    private function _request($url , $post = null, $postFields = null) {
        $ch = curl_init();
      
        // Default CURL options
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      	curl_setopt($ch, CURLOPT_USERPWD, $this->VocUsername . ":" . $this->VocPassword);
      	//curl_setopt($ch, CURLOPT_USERPWD, $this->getConfiguration('VocUsername') . ":" . $this->getConfiguration('VocPassword'));
      	curl_setopt($ch, CURLOPT_URL, $url);
       	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Cache-control: no-cache',
            'Accept-Encoding: br, gzip, deflate',
            'Content-type: application/json; charset=utf-8',
            'User-Agent: Volvo%20On%20Call/4.6.9.264685 CFNetwork/1120 Darwin/19.0.0',
            'X-device-id: Device',
            'X-originator-type: App',
            'X-os-type: Android',
            'X-os-version: 22',
            'Accept: */*'));
        if (!empty($post))
        {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if (!empty($postFields))
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Execute request
        $response = curl_exec($ch);
    
        if (!$response)
          throw new \Exception('Unable to retrieve data');
    
        /*
        // Get response
        $body = [];
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body[] = substr($response, $header_size);
    
        curl_close($ch);
    
        return (object)[
          'headers' => $header,
          'body' => $body
        ];
        */
        // Check if error
        if(curl_errno($ch)) {
            $info = [];
            echo 'Erreur Curl : ' . curl_error($ch);
        }
        else {
            $info = curl_getinfo($ch);
        }
        curl_close($ch);
        $ret_array["info"] = $info;
        $ret_array["result"] = json_decode($response, TRUE);
        return $ret_array;
    }

    private function _checkAuth() {
        if (empty($this->body)){
            return false;
        }
        else {
            return true;
        }
    }
    public function getTestUrl() {
        $this->_checkAuth();
   
        $result = $this->_request($this->api_url . $this->ACCOUNT);
        $url = $result["result"]['account'];

        //return json_decode($idAccount);
        return $url;
    }

    public function getAccount() {
        $this->_checkAuth();
   
        $result = $this->_request($this->api_url . $this->ACCOUNT);

        $accountVehicleRelations = explode('/', $result["result"]['accountVehicleRelations'][0]);
        $idAccount = end($accountVehicleRelations);

        //return json_decode($idAccount);
        return $idAccount;
    }

    public function getVin() {
        $this->_checkAuth();
    
        $result = $this->_request($this->api_url . $this->ACCOUNT_RELATION . $this->getAccount());
        $vin = $result["result"]['vehicleId'];
        //print_r($vin);
    
        //return json_decode($vin);
        return $vin;
    }

    public function getAttributes($vin) {
        $this->_checkAuth();
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/attributes');
        //print_r($result["result"]);
    
        return $result["result"];
    }

    public function getStatus($vin) {
        $this->_checkAuth();
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/status');
        //print_r($result["result"]);
    
        return $result["result"];
    }

    public function getPosition($vin, $params) {
        $this->_checkAuth();
    
        $params = null; //?client_longitude=0.000000&client_precision=0.000000&client_latitude=0.000000
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/position'.$params);
        //print_r($result["result"]);
    
        return $result["result"];
    }

    public function getChargeLocations($vin, $params) {
        $this->_checkAuth();
    
        $params = null; //?status=Accepted
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/chargeLocations'.$params);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body);
    }

    public function getTrips($vin) {
        $this->_checkAuth();
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/trips');
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }

    // Command POST
    // Verify by attributes if lockSupported is true
    public function postLock($vin) {
        $this->_checkAuth();

        $post = 'POST';
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/lock', $post);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }

    // Verify by attributes if unlockSupported is true
    public function postUnLock($vin) {
        $this->_checkAuth();

        $post = 'POST';
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . '/unlock', $post);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }

    // Verify by attributes if remoteHeaterSupported is true
    public function postHeaterStart($vin) {
        $this->_checkAuth();

        $post = 'POST';
        $postFields = '/heater/start';
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . $postFields, $post, $postFields);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }

    // Verify by attributes if remoteHeaterSupported is true
    public function postHeaterStop($vin) {
        $this->_checkAuth();

        $post = 'POST';
        $postFields = '/heater/stop';
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . $postFields, $post, $postFields);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }

    // Verify by attributes if preclimatizationSupported is true
    public function postPreclimatizationStart($vin) {
        $this->_checkAuth();

        $post = 'POST';
        $postFields = '/preclimatization/start';
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . $postFields, $post, $postFields);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }

    // Verify by attributes if preclimatizationSupported is true
    public function postPreclimatizationStop($vin) {
        $this->_checkAuth();

        $post = 'POST';
        $postFields = '/preclimatization/stop';
    
        $result = $this->_request($this->api_url . $this->VEHICLE . $vin . $postFields, $post, $postFields);
        //print_r($result["result"]);
    
        return $result["result"];
        //return json_decode($result->body, true);
    }
}
?>