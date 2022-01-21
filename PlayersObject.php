<?php

/*
    Development Exercise

      The following code is poorly designed and error prone. Refactor the objects below to follow a more SOLID design.
      Keep in mind the fundamentals of MVVM/MVC and Single-responsibility when refactoring.

      Further, the refactored code should be flexible enough to easily allow the addition of different display
        methods, as well as additional read and write methods.

      Feel free to add as many additional classes and interfaces as you see fit.

      Note: Please create a fork of the https://github.com/BrandonLegault/exercise repository and commit your changes
        to your fork. The goal here is not 100% correctness, but instead a glimpse into how you
        approach refactoring/redesigning bad code. Commit often to your fork.

*/


interface IReadWritePlayers {
    function readPlayers($filename = null);
    function writePlayer($player, $filename = null);
    function display($isCLI, $course, $filename = null);
}

interface IRead {
    function readPlayers( $filename = null);
}

interface IWrite {
    function writePlayer( $player, $filename = null);
}

interface IDisplay {
    function display($isCLI, $course, $filename = null);
}

class ArrayDataSource implements IRead, IWrite {
    
    private $playersArray = [];

    function readPlayers( $filename = null)
    {

        $players = null;

        $jonas = new \stdClass();
        $jonas->name = 'Jonas Valenciunas';
        $jonas->age = 26;
        $jonas->job = 'Center';
        $jonas->salary = '4.66m';
        $players[] = $jonas;

        $kyle = new \stdClass();
        $kyle->name = 'Kyle Lowry';
        $kyle->age = 32;
        $kyle->job = 'Point Guard';
        $kyle->salary = '28.7m';
        $players[] = $kyle;

        $demar = new \stdClass();
        $demar->name = 'Demar DeRozan';
        $demar->age = 28;
        $demar->job = 'Shooting Guard';
        $demar->salary = '26.54m';
        $players[] = $demar;

        $jakob = new \stdClass();
        $jakob->name = 'Jakob Poeltl';
        $jakob->age = 22;
        $jakob->job = 'Center';
        $jakob->salary = '2.704m';
        $players[] = $jakob;

        return $players;
    }

    function writePlayer( $player, $filename = null){
        $this->playersArray[] = $player;
    }
}

class JsonDataSource implements IRead, IWrite {
    private $playerJsonString;
    function readPlayers( $filename = null)
    {
        $json = '[{"name":"Jonas Valenciunas","age":26,"job":"Center","salary":"4.66m"},{"name":"Kyle Lowry","age":32,"job":"Point Guard","salary":"28.7m"},{"name":"Demar DeRozan","age":28,"job":"Shooting Guard","salary":"26.54m"},{"name":"Jakob Poeltl","age":22,"job":"Center","salary":"2.704m"}]';
        return json_decode($json);
        
    }

    function writePlayer( $player, $filename = null){
        $players = [];
        if ($this->playerJsonString) {
            $players = json_decode($this->playerJsonString);
        }
        $players[] = $player;
        $this->playerJsonString = json_encode($player);
    }
}

class FileDataSource implements IRead, IWrite {
    
    function readPlayers( $filename = null)
    {
        $file = file_get_contents($filename);
        return  json_decode($file);
    }

    function writePlayer( $player, $filename = null){
        $players = json_decode($this->readPlayers($filename));
        if (!$players) {
            $players = [];
        }
        $players[] = $player;
        file_put_contents($filename, json_encode($players));
    }

    
}

class DataDisplay implements IDisplay {

    
    function display($isCLI, $dataSource, $filename = null) {

        $players = $dataSource->readPlayers( $filename);

        if ($isCLI) {
            echo "Current Players: \n";
            foreach ($players as $player) {

                echo "\tName: $player->name\n";
                echo "\tAge: $player->age\n";
                echo "\tSalary: $player->salary\n";
                echo "\tJob: $player->job\n\n";
            }
        } else {

            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    li {
                        list-style-type: none;
                        margin-bottom: 1em;
                    }
                    span {
                        display: block;
                    }
                </style>
            </head>
            <body>
            <div>
                <span class="title">Current Players</span>
                <ul>
                    <?php foreach($players as $player) { ?>
                        <li>
                            <div>
                                <span class="player-name">Name: <?= $player->name ?></span>
                                <span class="player-age">Age: <?= $player->age ?></span>
                                <span class="player-salary">Salary: <?= $player->salary ?></span>
                                <span class="player-job">Job: <?= $player->job ?></span>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </body>
            </html>
            <?php
        }
    }

}

$arrayDataSource = new ArrayDataSource();

$dataDisplay = new DataDisplay();

$dataDisplay->display(php_sapi_name() === 'cli', $arrayDataSource, "playerdata.json");

?>