<?php
   wp_enqueue_media();
   
   //Global variable
   global $wpdb;
   
   // Sets timezone date
   $date = new DateTime("now", new DateTimeZone('Asia/Calcutta'));
   $date = $date->format('Y-m-d H:i:s');
   
   // Table name
   $table_name='wp_import';
   
   if(isset($_POST['submit']))
   {
		// File extension
		$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
		
		// File Location
		$fileName = $_FILES['file']['tmp_name'];
	  
        if($_FILES['file']['size'] > 0 && $extension == 'csv')
        {
			// Open file in read mode
			$file = fopen($fileName, 'r');
		 
			fgetcsv($file);//Skipping header row
		 
			while(($getData = fgetcsv($file,10000,','))!==FALSE)
			{	
				
				// Assign value to variables
				$id = trim($getData[0]);
				$product = trim($getData[1]);
				$price = trim($getData[2]);
				$sku = trim($getData[3]);
				$inventory = trim($getData[4]);
				
				// Check record already exists or not
				$table_name='wp_import';
				$record = $wpdb->get_results( "SELECT * FROM $table_name where sku='".$sku."'");
			  if(count($record)>0)
			  {	  // Update Record
				  $result = $wpdb->update($table_name, array('id'=>$id,'product'=>$product,'price'=>$price,'inventory'=>$inventory,'update_at'=>$date),  array('sku'=>$sku) );
			  }
			  else
			  {
				 // Insert Record
				 $result = $wpdb->insert('wp_import', array(
				   'id'      =>$id,
				   'product' =>$product,
				   'price' =>$price,
				   'sku' =>$sku,
				   'inventory' => $inventory,
				   'update_at'=> $date
				 )); 
			  }
				
			}
			//Close file
			fclose($file );
		}
		else
		{
		  echo '<script type="text/javascript">alert("Please select only csv file!");</script>';
		}
	   
   }
   
   //Gets last update date
   $record = $wpdb->get_results( "SELECT update_at as last_update FROM $table_name");
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Add Images</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
   </head>
   <body>
      <div class="container">
         <div class="panel panel-danger" style="margin-top:20px;">
            <div class="panel-heading">Import CSV File</div>
            <div class="panel-body">
               <form action="#" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                     <input type="file"  name="file" id="csv_File" required>
					 
                  </div>
                  <div class="form-group">
					<input type="submit" name="submit" class="btn btn-success" value="Import" />
				  </div>
				  <span style="font-size:20px;" class="text-primary">Last updated at &#8212;  <?php echo $record[0]->last_update; ?></span>
               </form>
            </div>
         </div>
      </div>
   </body>
</html>