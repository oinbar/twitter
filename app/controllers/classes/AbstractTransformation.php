<?php

/**
 * Class AbstractTransformation
 * basic functional manipulation of a document. reads a jsonDoc, takes some parameters,
 * runs some transformation on the data, add or modify some fields in it, and returns the document
 * in its original JSON form.
 */
abstract class AbstractTransformation {

	protected $doc;
	protected $requiredFields;
    protected $transformationName;

	function __construct($jsonDoc) {
		$this->doc = json_decode($jsonDoc, true);
        $this->signOffTransformation();
	}

	/**
	*	runs the transformation, returns a json doc
	*/
	abstract public function run();

    public function getTransformationName() {
        return $this->transformationName;
    }

    public function getNextTransformation() {
        if (sizeof($this->doc['transformations']['pending'] > 0)) {
            return key($this->doc['transformations']['pending']);
        }
        else {
            return 'persistence';
        }
    }

	/**
	*	makes sure the doc has the required fields for the transformation
	*/
    abstract protected function docHasFields();

    /**
     * modifies the "transformations" field in the doc, and moves the transformationName
     * from "pending", to "completed".
     *
     *so from:
     * doc[
     *      "transformations" => array(
     *          "pending" => array(trans1, trans2),
     *          "completed" => array()
     *      "feeds" => array(feedId)
     *to:
     *      "transformations" => array(
     *          "pending" => array(trans1),
     *          "completed" => array(trans2)
     *      "feeds" => array(feedId)
     *
     * @return mixed
     */
    protected function signOffTransformation() {
        $this->doc['transformations']['completed'][$this->transformationName] = $this->doc['transformations']['pending'][$this->transformationName];
        unset($this->doc['transformations']['pending'][$this->transformationName]);
    }

}