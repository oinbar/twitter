<?php



include __DIR__.'/../CalaisTransformation.php';

$doc = array('text' => 'The blue whale (Balaenoptera musculus) is a marine mammal belonging to the baleen whales (Mysticeti).[3] At 30 metres (98 ft)[4] in length and 170 tonnes (190 short tons)[5] or more in weight, it is the largest existing animal and the heaviest that ever existed',
             'transformations' => array(
                 'pending' => array('trans1' => 'params1', 'calais'=> 'params2'),
                 'completed' =>  array('trans2' => ''),
             ));

$jsonDoc = json_encode($doc);

$apiKey = 'qupquc5c4qzj7sg9knu5ad4w';

$ct = new CalaisTransformation($jsonDoc, $apiKey);

$doc = $ct->run();

echo print_r(json_decode($doc), true);
echo print_r($ct->getNextTransformation());

assert(
    $doc ==
        '{"text":"The blue whale (Balaenoptera musculus) is a marine mammal belonging to the baleen whales (Mysticeti).[3] At 30 metres (98 ft)[4] in length and 170 tonnes (190 short tons)[5] or more in weight, it is the largest existing animal and the heaviest that ever existed","transformations":{"pending":{"trans1":"params1"},"completed":{"trans2":"","calais":"params2"}},"opencalais":[{"_typeGroup":"topics","category":"http:\/\/d.opencalais.com\/cat\/Calais\/Environment","classifierName":"Calais","categoryName":"Environment","score":0.988},{"_typeGroup":"topics","category":"http:\/\/d.opencalais.com\/cat\/Calais\/HumanInterest","classifierName":"Calais","categoryName":"Human Interest","score":0.586},{"_typeGroup":"language","language":"http:\/\/d.opencalais.com\/lid\/DefaultLangId\/English"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/1","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/69abc0bf-fc6b-3dd4-9741-c8eeefa896cf","name":"Baleen whales","importance":"1","originalValue":"Baleen whales"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/2","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/760fa253-dda4-3531-a103-d62b70aa87c8","name":"Zoology","importance":"1","originalValue":"Zoology"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/3","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/a4d237aa-c4e9-3f9b-a26f-70199de66284","name":"Biology","importance":"1","originalValue":"Biology"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/4","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/743cee9e-7a54-3a15-aea7-8d0b2566c502","name":"Megafauna","importance":"2","originalValue":"Megafauna"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/5","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/c818b0fb-259c-3881-9201-a55fb462a6dc","name":"Blue whale","importance":"2","originalValue":"Blue whale"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/6","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/4f1aead9-f25b-37ae-8799-596a8c2c217a","name":"Whale","importance":"2","originalValue":"Whale"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/7","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/7a731a9a-d930-3b6c-99fe-bdc919e5ac48","name":"Balaenoptera","importance":"2","originalValue":"Balaenoptera"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/8","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/e4afabb8-b233-3687-b8d2-ff7bffe671e8","name":"Largest mammals","importance":"2","originalValue":"Largest mammals"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/9","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/b33c1b73-66ce-3e1e-97ee-c69ae9fda1e9","name":"Sei whale","importance":"2","originalValue":"Sei whale"},{"_typeGroup":"socialTag","id":"http:\/\/d.opencalais.com\/dochash-1\/71fd7e9e-f97b-3f5b-9cf7-6d79b3bd7b10\/SocialTag\/10","socialTag":"http:\/\/d.opencalais.com\/genericHasher-1\/5790e09d-909e-3258-9673-96208305e369","name":"Environment","importance":"1"}]}'
);
assert($ct->getNextTransformation() == 'trans1');
