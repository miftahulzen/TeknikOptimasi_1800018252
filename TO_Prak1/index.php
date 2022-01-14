<?php

    class Catalogue{

        function createProductColumn($column, $listOfRawProduct){
            foreach (array_keys($listOfRawProduct) as $listOfRawProductkey){
                $listOfRawProduct[$column[$listOfRawProductkey]] = $listOfRawProduct[$listOfRawProductkey];
                unset($listOfRawProduct[$listOfRawProductkey]);
            }
            return $listOfRawProduct;
        }

        function product($parameters){
            $collectionofListProduct = [];

            $raw_data = file($parameters['file_name']);
            foreach($raw_data as $listOfRawProduct){
                $collectionofListProduct[] = $this->createProductColumn($parameters['columns'], explode(",",$listOfRawProduct));
            }

            return [
                'product' => $collectionofListProduct,
                'gen_length' => count($collectionofListProduct)
            ];
        }

    }

    class PopulationGenerator {
        function createIndividu($parameters){
            $catalogue = new Catalogue;
            $lengthOfGen = $catalogue->product($parameters)['gen_length'];
            for($i = 0;$i<=$lengthOfGen;$i++){
                $ret[] = rand(0,1);
            }
            return $ret;
        }

        function createPopulation($parameters){
            for($i = 0;$i<$parameters['population_size'];$i++){
                $ret[] = $this->createIndividu($parameters);
            }
            foreach($ret as $key => $val){
                print_r($val);
                echo '<br>';
            }
        }
    }

    $parameters = [
        'file_name' => 'products.txt',
        'columns' => ['item', 'price'],
        'population_size' => 5
    ];

    $initialPopulation = new PopulationGenerator;
    $initialPopulation->createPopulation($parameters);

?>