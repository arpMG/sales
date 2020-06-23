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
        }
        
        //Open file for reading
        $sales_file = new SplFileObject('data/sales.csv', 'r');
        $sales_file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);


        //Load all sales into array
        $sales = [];
        while(! $sales_file->eof()){
            $temp = $sales_file->fgetcsv();
            if($temp){
                $sales[] = $temp;
            }
        }
        $sales_file = null;


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
                <input name="salesId" id="salesId" type="text">
            </div>
            <div class="row">
                <label for="month">Month</label>
                <select name="month" id="month">
                    <option value="">All</option>
                    <option value="1">January</option>
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
                    <option value="12">December</option>
                </select>
            </div>
            <div class="row">
                <button class="btn block" name="submit" type="submit">Search</button>
            </div>

        </form>
        <table class="tbl">
            <thead>
                <th>id</th>
                <th>Sales Id</th>
                <th>Date</th>
                <th>Amount ($)</th>
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
                        ($salesId == "" || $salesId == $sale[1]) 
                        
                        &&
                        
                        ($month == "" || $month == $curMonth)
                        ){
                            
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