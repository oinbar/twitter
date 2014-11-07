<?php

include __DIR__.'/CalaisAPI.php';
include __DIR__.'/AbstractTransformation.php';
include __DIR__.'/CalaisTransformationException.php';

class CalaisTransformation extends AbstractTransformation {

    protected $apiKey = null;
    protected $requiredFields = array('text');
    protected $transformationName = 'calais';

    public function __construct($doc, $apiKey)
    {
        parent::__construct($doc, $this->transformationName);
        $this->apiKey = $apiKey;
        $this->docHasFields();
    }

    /**
     * make sure document contains the necessary fields, for example a field called "text"
     * if this is an NLP transformation
     *
     * @return bool
     * @throws CalaisTransformationException
     */
    protected function docHasFields()
    {

        foreach ($this->requiredFields as $field) {
            if (!in_array($field, array_keys($this->doc))) {
                throw new CalaisTransformationException("Doc does not contain field: " . $field);
            }
        }
        return true;
    }

    public function run()
    {

        try {
            $doc = $this->doc;
            $oc = new OpenCalais($this->apiKey);
            $results = json_decode($oc->getResult($doc['text']), true);

            // fix some issue with the keys
            unset($results['doc']);
            unset($results[0]);
            foreach ($results as $key => $val) {
                unset($results[$key]);
                array_push($results, $val);
            }

            $doc['opencalais'] = $results;
            return json_encode($doc);
        }

        catch (Exception $e) {
            throw new CalaisTransformationException('');
        }
    }
}