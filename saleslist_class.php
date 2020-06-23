<?php require_once "classes/Sales.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales List</title>

    <link rel="stylesheet" href="style.css">
    <?php

        //Search keys
        $salesId = ""; //this will have the salesPerson's unique Id
        $month = ""; //this will have the NUMBER of the month, Jan=1,Feb=2 etc... 
        $sortBy = ""; //default = no sort, otherwise by each field
        if(isset($_GET['submit'])){
            $salesId = $_GET['salesId'];
            $month = $_GET['month'];
            $sortBy = $_GET['sort'];
        }
        
        //Open file for reading
        $sales_file = new SplFileObject('data/sales.csv', 'r');
        $sales_file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);


        //Load all sales into array
        $sales = [];
        while(! $sales_file->eof()){

            //Create instance of Sales class and use constructor to initalise properties
            $temp = new Sales($sales_file->fgetcsv()); //constructor takes array and puts values to member properties
            
            if($temp){
                $sales[] = $temp;

                //Create a separate array with just salesIds - for filter drop down list
                $salesIds[] = $temp[1];
            }
        }
        $sales_file = null;

        // //Format all the dates and put them back
        // for($i=0; $i<count($sales); $i++) {
        //    $date = DateTime::createFromFormat('d/m/Y', $sales[$i][2]);
        //    $sales[$i][2] = $date->format('Y-m-d');
        //    $sales[$i][3] = number_format($sales[$i][3], 2, '.', '');
        // }
        // $sales_file = new SplFileObject('data/sales.csv', 'w');
        // foreach ($sales as $sale){
        //     $sales_file->fputcsv($sale);
        // }
        // $sales_file = null;
        // exit('sorted');

        //Do I need to sort?
        $sort_key = [];
        if(strlen($sortBy) > 0){
            foreach ($sales as $key => $row) {
                if($sortBy == "salesid"){
                    $sort_key[$key] = $row[1]; 
                }elseif($sortBy == 'sales_date'){
                    $sort_key[$key] = $row[2];
                }elseif($sortBy == 'amount'){
                    $sort_key[$key] = $row[3];
                }
            }
            if($sort_key){
                array_multisort($sort_key, SORT_ASC, $sales);
            }
   
        }

        //Only want to keep each salesId ONCE
        $salesIds = array_unique($salesIds);
        //Lets put them in order for niceties
        sort($salesIds);

    ?>
</head>
<body>
    <pre>
        <?php
            // print_r($sales);
        ?>
    </pre>
    <div class="container">
        <h1>Sales Data</h1>
        <form class="">
            <div class="row">
                <label for="salesId">Sales ID</label>
                <!-- <input name="salesId" id="salesId" type="text"> -->
                <select name="salesId" id="salesId">
                    <option value="">All</option>
                    <?php
                        foreach($salesIds as $id){
                            echo "<option value='$id'>$id</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="row">
                <label for="month">Month</label>
                <select name="month" id="month">
                    <option value="">All</option>
                    <!-- <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option> -->
                    <?php
                    //These 2 lines replace the 12 lines of HTML
                        for($m = 1; $m <= 12; $m++){
                            echo "<option value='$month'>".DateTime::createFromFormat("!m", $m)->format('F')."</option>".PHP_EOL;
                        }
                    ?>

                </select>
            </div>
            <h4>Sort by...</h4>
            <div class="row">
                
                <label for="default"><input type="radio" name="sort" id="default" value="default" checked='checked'> default</label>
                <label for="salesid"><input type="radio" name="sort" id="salesid" value="salesid"> Sales Id</label>
                <label for="sales_date"><input type="radio" name="sort" id="sales_date" value="sales_date"> Sales Date</label>
                <label for="amount"><input type="radio" name="sort" id="amount" value="amount"> Amount</label>
            </div>
            <div class="row">
                <button class="btn block" name="submit" type="submit">Search</button>
            </div>

        </form>
        <div class="row half center">
            <h3>Current Filter</h3>
            <h4>
                <span class="filter">Sales Id: </span> 
                <span class="filter_value"><?php echo (strlen($salesId) == 0)?'none':$salesId; ?></span>
                <span class="filter">Month: </span>
                <span class="filter_value"><?php echo (strlen($month) == 0)?'none':DateTime::createFromFormat('!m', $month)->format('F');?></span>
            </h4>
        </div>
        <table class="tbl">
            <thead>
                <th>id <span class="sortBy">
                        <?php if($sortBy == 'default'){echo html_entity_decode('&#8593;');}?>
                    </span>
                </th>
                <th>Sales Id <span class="sortBy">
                        <?php if($sortBy == 'salesid'){echo html_entity_decode('&#8593;');}?>
                    </span>
                </th>
                <th>Date <span class="sortBy">
                    <?php if($sortBy == 'sales_date'){echo html_entity_decode('&#8593;');}?>
                    </span>
                </th>
                <th>Amount ($) <span class="sortBy">
                    <?php if($sortBy == 'amount'){echo html_entity_decode('&#8593;');}?>
                    </span>
                </th>
            </thead>
            <tbody>

            <?php
                //loop through array and add rows
                foreach($sales as $sale){

                    //Will I display this?
                    $curDate = DateTime::createFromFormat('Y-m-d', $sale[2]);
                    $curMonth = intval($curDate->format('m'));

                    //SalesId Filter
                    if(
                        // (strlen($salesId) == 0 || $sale[1] === $salesId) &&
                        // (strlen($month) == 0 || intval($month) == $curMonth)
                        ($salesId == "" || $salesId == $sale[1]) &&
                        ($month == "" || $month == $curMonth)
                        )
                        
                        {

                        echo "<tr>".PHP_EOL;
                        echo "<td>".$sale[0]."</td>".PHP_EOL;
                        echo "<td>".$sale[1]."</td>".PHP_EOL;
                        // echo "<td>".$sale[2]."</td>".PHP_EOL;
                        echo "<td>".$curDate->format('d/m/Y')."</td>".PHP_EOL;
                        echo "<td style='text-align:right'>".$sale[3]."</td>".PHP_EOL;
                        echo "</tr>".PHP_EOL;
                    }
                }
            ?>

            </tbody>

        </table>
    </div>
</body>
</html>