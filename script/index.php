<?php

    /**
     * LaMetric – Raider.io App Callback Script
     * 
     * All details are stored within $app.
     * In case of debug, have a look into the array.
     * 
     * @author Andreas Jon Grote
     * @copyright Free For All!
     * @version 0.1
     * @link githublink comming
     * @todo regions update
     */

    /**
     * Map given details from app
     * 
     * Name: Charakter
     * Realm: Realm the character is on
     * Region: Region the realm is connected to
     * Fields: Current value to retrieve the most recent score
     */
    $app['rio'] = array(
        'name' => $_GET['name'],
        'realm' => $_GET['realm'],
        'region' => $_GET['region'],
        'fields' => 'mythic_plus_scores_by_season:current'
    ) ;
    
    /**
     * Raider.io API URL
     */
    $app['request_url'] = 'https://raider.io/api/v1/characters/profile?'.http_build_query($app['rio']) ;
    
    /**
     * CURL request to retrieve characters details from raider.io
     */
    $app['curl_request'] = curl_init();
    curl_setopt($app['curl_request'], CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($app['curl_request'], CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($app['curl_request'], CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($app['curl_request'], CURLOPT_URL, $app['request_url']);
    curl_setopt($app['curl_request'], CURLOPT_TIMEOUT, 80);
    $app['curl_result'] = curl_exec($app['curl_request']);
    curl_close($app['curl_request']);  
    
    /**
     * Parsing json
     */
    $app['rio']['details'] = json_decode($app['curl_result'], true);
    
    /**
     * Define specific callback
     */
    if (is_array($app['rio']['details'])) {
        if ($app['rio']['details']['statusCode'] == 400) {
            $app['callback'] = $app['rio']['details']['message'] ;
        } else {
            $app['callback'] = round($app['rio']['details']['mythic_plus_scores_by_season'][0]['scores']['all']) ;
        }
    } else {
        $app['callback'] = 'Error: Not able to request data' ;
    }
    
    /**
     * JSON Callback
     */
    header('Content-Type: application/json');
    $app['json']['frames'] = array(array('icon' => 'i40998', 'text' => $app['callback'] )) ;
    echo json_encode($app['json']) ;

    /**
     * THATS ALL FOLKS!
     */

?>