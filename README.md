#Sales

This php page reads a CSV file with the format:
id, sales_id, sales_date, sales_amount

Where:
*id = unique row id [integer]
*sales_id = id for sales person [text, format: XXX99]
*sales_date = date of sales [format :Y-m-d]
*sales_amount = value of sale [float: format N.99]

Eg: 
1,ARP99,2019-03-01,345.98

This page allows:
*Reading the file and display in HTML table
*Filter view by Sales Id and by Month of Sale
*Sort the view (only ascending) by each field
