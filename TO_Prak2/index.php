<?php

    class Parameters{
        const FILE_NAME = 'products.txt';
        const COLUMNS = ['item', 'price'];
        const POPULATION_SIZE = 5;
        const BUDGET = 150000; //MENGATUR BUDGET MAX
        const STOPPING_VALUE = 10000; //MAKSIMAL SELISIH BIAYA DENGAN BUDGET
    }

    class Catalogue{

        function createProductColumn($listOfRawProduct){
            foreach (array_keys($listOfRawProduct) as $listOfRawProductkey){
                $listOfRawProduct[Parameters::COLUMNS[$listOfRawProductkey]] = $listOfRawProduct[$listOfRawProductkey];
                unset($listOfRawProduct[$listOfRawProductkey]);
            }
            return $listOfRawProduct;
        }

        function product(){
            $collectionofListProduct = [];

            $raw_data = file(Parameters::FILE_NAME);
            foreach($raw_data as $listOfRawProduct){
                $collectionofListProduct[] = $this->createProductColumn(explode(",",$listOfRawProduct));
            }
            return $collectionofListProduct;
        }

    }

    Class Individu{

        function countNumberOfGen(){ //MENGHITUNG JUMLAH PRODUCT
            $catalogue = new Catalogue;
            return count($catalogue->product());
        }

        function createRandomIndividu(){ //MEMBERI ANGKA BINER RANDOM ANTARA 1 DAN 0
            for($i = 0;$i<=$this->countNumberOfGen()-1;$i++){
                $ret[] = rand(0,1);
            }
            return $ret;
        }

    }

    class Population {

        function createRandomPopulation(){ 
            $individu = new Individu();
            for($i = 0;$i<Parameters::POPULATION_SIZE-1;$i++){
                $ret[] = $individu->createRandomIndividu();
            }
            return $ret;
        }

    }

    class Fitness{

        function selectingItem($individu){ 
            $catalogue = new Catalogue;
            foreach ($individu as $individukey => $binaryGen){
                if($binaryGen === 1){
                    $ret[] = [
                        'selectedKey' => $individukey,
                        'selectedPrice' => $catalogue->product()[$individukey]['price']
                    ];
                }
            }
            return $ret;
        }

        function calculateFitnessValue($individu){
            return array_sum(array_column($this->selectingItem($individu), 'selectedPrice'));
        }

        function countSelectedItem($individu){ 
            return count($this->selectingItem($individu));
        }

        function searchBestIndividu($fits, $maxItem, $numberOfIndividuHasMaxItem){
            if($numberOfIndividuHasMaxItem === 1){
                $index = array_search($maxItem, array_column($fits, 'numberOfSelectedItem'));
                return $fits[$index];
            }else{
                foreach($fits as $key => $val){
                    if($val['numberOfSelectedItem'] === $maxItem){
                        echo $key.' '.$val['fitnessValue'].'<br>';
                        $ret[] = [
                            'individuKey' => $key,
                            'fitnessValue' => $val['fitnessValue']
                        ];
                    }
                }
                if(count(array_unique(array_column($ret, 'fitnessValue'))) === 1){
                    $index = rand(0, count($ret) - 1);
                }else{
                    $max = max(array_column($ret, 'fitnessValue'));
                    $index = array_search($max, array_column($ret, 'fitnessValue'));
                }
                
                return $ret[$index];
            }
        }

        function isFound($fits){
            $countedMaxItems = array_count_values(array_column($fits, 'numberOfSelectedItem'));
            print_r($countedMaxItems);
            echo '<br>';
            $maxItem = max(array_keys($countedMaxItems));
            echo $maxItem;
            echo '<br>';
            echo $countedMaxItems[$maxItem];
            $numberOfIndividuHasMaxItem = $countedMaxItems[$maxItem];

            $bestFitnessValue = $this->searchBestIndividu($fits, $maxItem, $numberOfIndividuHasMaxItem)['fitnessValue'];
            echo '<br>';
            echo '<br>Best Fitness Value: '. $bestFitnessValue;

            $residual = Parameters::BUDGET - $bestFitnessValue;
            echo ' Residual: '. $residual;

            if($residual <= Parameters::STOPPING_VALUE && $residual > 0){
                return TRUE;
            }
        }

        function isFit($fitnessValue){
            if($fitnessValue <= Parameters::BUDGET){
                return TRUE;
            }
        }

        function fitnessEvaluation($population){
            $catalogue = new Catalogue;
            foreach ($population as $listOfIndividukey => $listOfIndividu){
                echo 'Individu-'.$listOfIndividukey."<br>";
                foreach ($listOfIndividu as $individukey => $binaryGen){
                   echo $binaryGen.'&nbsp;&nbsp;';
                   print_r($catalogue->product()[$individukey]);
                   echo '<br>';
                }
                $fitnessValue = $this->calculateFitnessValue($listOfIndividu);
                $numberOfSelectedItem = $this->countSelectedItem($listOfIndividu);

                echo 'Max Items: '.$numberOfSelectedItem;
                echo '<br>';
                echo 'Fitness Value: '.$fitnessValue;
                echo '<br>';

                if($this->isFit($fitnessValue)){
                    echo '(Fit)';
                    $fits[]= [
                        'selectedIndividuKey' => $listOfIndividukey,
                        'numberOfSelectedItem' => $numberOfSelectedItem,
                        'fitnessValue' => $fitnessValue
                    ];
                    print_r($fits);
                }else{
                    echo '(Not Fit)';
                }
                echo '<p>';
            }
            if($this->isFound($fits)){ 
                echo 'found'; 
            }else{
                echo ' >> Next Generation';
            }
        }
    }

    $parameters = [
        'file_name' => 'products.txt',
        'columns' => ['item', 'price'],
        'population_size' => 5
    ];

    $initialPopulation = new Population;
    $population = $initialPopulation->createRandomPopulation();

    $fitness = new Fitness;
    $fitness->fitnessEvaluation($population);

?>