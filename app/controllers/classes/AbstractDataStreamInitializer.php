<?php


abstract class AbstractDataStreamInitializer {

    protected $transformations_params;
    protected $feedId;
    protected $doc;

    protected function __construct($transformations_params, $feedId) {
        $this->transformations_params = $transformations_params;
        $this->feedId = $feedId;
        $this->doc = $this->initializeDoc();
    }

    public function getNextTransformation() {
        if (!empty($this->doc['transformations']['pending'])) {
            reset($this->doc['transformations']['pending']);
            $first_key = key($this->doc['transformations']['pending']);
            return $first_key;
        }
        else {
            return 'persistence';
        }
    }

    /**
     *
     *
     * @return returns an array of documents, where each document is built ontop of the initialized doc
     * (fields are simply added on top)
     */
    abstract public function run();

    /**
     * create initial doc, with the fields:
     *      "transformations" => array(
     *          "pending" => array(trans1=>param1, trans2=>params2),
     *          "completed" => array()
     *      "feeds" => array(feedId)
     *
     * @return mixed
     */
    private function initializeDoc(){
        return array(
            'transformations' => array(
                'pending' => $this->transformations_params,
                'completed' => array()),
            'feeds' => array($this->feedId));
    }
}