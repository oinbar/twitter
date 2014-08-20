<?php

/**
* Open Calais Tags
* Last updated 1/16/2012
* Copyright (c) 2012 Dan Grossman
* http://www.dangrossman.info
*
* Please see http://www.dangrossman.info/open-calais-tags
* for documentation and license information.
*/

class OpenCalaisException extends Exception {}

class OpenCalais {

    private $api_url = 'http://api.opencalais.com/enlighten/rest/';
    private $api_key = '';

    public $contentType = 'text/html';
    public $outputFormat = 'Application/JSON';
    public $getGenericRelations = true;
    public $getSocialTags = true;
    public $docRDFaccessible = false;
    public $allowDistribution = false;
    public $allowSearch = false;
    public $externalID = '';
    public $submitter = '';

    private $document = '';
    private $entities = array();

    public function OpenCalais($api_key) {
        if (empty($api_key)) {
            throw new OpenCalaisException('An OpenCalais API key is required to use this class.');
        }
        $this->api_key = $api_key;
    }

    public function getResult($document) {

        $this->document = $document;

        $result = $this->callAPI();

        return $result;

    }

    private function getParamsXML() {

        $types = array();
        if ($this->getGenericRelations)
            $types[] = 'GenericRelations';
        if ($this->getSocialTags)
            $types[] = 'SocialTags';
        
        $xml = '<c:params xmlns:c="http://s.opencalais.com/1/pred/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">';
        $xml .= '<c:processingDirectives ';
        $xml .= 'c:contentType="' . $this->contentType . '" ';
        $xml .= 'c:enableMetadataType="' . implode(',', $types) . '" ';
        $xml .= 'c:outputFormat="' . $this->outputFormat . '" ';
        $xml .= 'c:docRDFaccessible="' . ($this->docRDFaccessible ? 'true' : 'false') . '" ';
        $xml .= '></c:processingDirectives>';
        $xml .= '<c:userDirectives ';
        $xml .= 'c:allowDistribution="' . ($this->allowDistribution ? 'true' : 'false') . '" ';
        $xml .= 'c:allowSearch="' . ($this->allowSearch ? 'true' : 'false') . '" ';

        if (!empty($this->externalID))
            $xml .= 'c:externalID="' . htmlspecialchars($this->externalID) . '" ';

        if (!empty($this->submitter))
            $xml .= 'c:submitter="' . htmlspecialchars($this->submitter) . '" ';

        $xml .= '></c:userDirectives>';
        $xml .= '<c:externalMetadata></c:externalMetadata>';
        $xml .= '</c:params>';
        
        return $xml;

    }

    private function callAPI() {

        $data = 'licenseID=' . urlencode($this->api_key);
        $data .= '&paramsXML=' . urlencode($this->getParamsXML());
        $data .= '&content=' . urlencode($this->document);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);

        if (strpos($response, "<Exception>") !== false) {
            $text = preg_match("/<Exception\>(.*)<\/Exception>/mu", $response, $matches);
            throw new OpenCalaisException('CALAIS ERROR: ' . $matches[1] . ' TEXT: ' . $this->document);
        }
        
        // Log::error($response);
        return $response;
    }
}
