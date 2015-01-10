<?php
include __DIR__.'/AbstractDataStreamInitializer.php';
include __DIR__.'/../../twitter-api-php/TwitterAPIExchange.php';

class TwitterSearchInitializer extends AbstractDataStreamInitializer{

    private $searchCriteria;
    private $credentials;

    public function __construct($transformations, $feedId, $access_token, $access_token_secret, $consumer_key, $consumer_secret, $searchCriteria) {
        parent::__construct($transformations, $feedId);
        $this->searchCriteria = $searchCriteria;
        $this->credentials = array(
            'oauth_access_token' => $access_token,
            'oauth_access_token_secret' => $access_token_secret,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret);
    }

    public function run($since_id='') {

        Log::error($this->searchCriteria);

        if ($since_id != '') {
            $since_id = '&since_id=' . $since_id;
        }
        $getfield = '?count=100' . $since_id . '&q=' . $this->searchCriteria;


        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($this->credentials);
        $json = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();



        $data = json_decode($json, true);

        foreach ($data['statuses'] as $key=>$val) {
            $val['_id'] = $val['id'];
            $data['statuses'][$key] = $val;
            $data['statuses'][$key] = array_merge($data['statuses'][$key], $this->doc);
        }

        return json_encode($data);
    }


} 