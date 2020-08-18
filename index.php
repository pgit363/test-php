<?php 
//TendersProject001
//Cuso4@mgso4
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Credentials: true"); 

header('Access-Control-Allow-Headers: origin, content-type, accept');

header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');

header('Access-Control-Max-Age: 86400'); 

header("HTTP/1.1 200 OK");

include 'DBconfig.php';

$con = mysqli_connect($servername, $username, $password, $dbname);

$request=$_SERVER['REQUEST_METHOD'];

$data=array();
 switch ($request) {
 	case 'GET':
 	      response(getData());
 	break;

    case 'POST':
 		  response(addData());
 	break;

    case 'PUT':
 		  response(updateData());
 	break;

    case 'DELETE':
 		  response(removeData());
 	break;

 	default:
 		# code...
 	break;
 }




function getData()
{
    global $con;
     
    $json = file_get_contents('php://input');
     
    $obj = json_decode($json,true);
     
    if($obj['task']=="login")
    {
        $query=mysqli_query($con,"SELECT email, password, role FROM `admin`");
                
        while ($row=mysqli_fetch_assoc($query)) 
        {
            if($obj['email'] == $row['email'] && $obj['password'] == $row['password'])
            {
                $data[]=array("status"=>200,"Message"=>"success","role"=>$row['role'],"email"=>$row['email'],"password"=>$row['password']);
            }
        }
        if(empty($data))
        {
            $data[]=array("status"=>404,"Message"=>"failed");
            return $data;
        }
        else
        {
            return $data;
        }
    }
    else if($obj['task']=="fetchdata")
    {
         if(@$obj['id'])
         {
         	@$id=$obj['id'];
         	$where="WHERE a_id=".$id;
         }
         else
         {
         	$id=0;
         	$where="";
         }
    
          $query=mysqli_query($con,"SELECT * FROM `admin` ".$where);
            
          while ($row=mysqli_fetch_assoc($query)) {
          	 $data[]=$row;
          }
          
          if(empty($data))
          {
            $data[]=array("status"=>404,"Message"=>"table empty");
            return $data;
          }
          else
          {
            return $data[]=array("status"=>200,"User"=>$data);
          }
     }
     else
     {
        $data[]=array("status"=>404,"Message"=>"invalid");
        return $data;
     }
   }

function addData(){

	global $con;
  
    $json = file_get_contents('php://input');
     
    $obj = json_decode($json,true);
     
    $CheckSQL = "SELECT * FROM `admin` WHERE email='".$obj['email']."'";
    
    $check = mysqli_fetch_array(mysqli_query($con,$CheckSQL));
    
    if(isset($check))
    {
        $data[]=array("status"=>404,"Message"=>"Email Already Exist, Please Try Other Email !!!");
    }
    
    else
    {
        $Sql_Query = "INSERT INTO `admin`( `email`, `password`,`role`) VALUES ('".$obj['email']."','".$obj['password']."','".$obj['role']."')";
         
        if(mysqli_query($con,$Sql_Query))
        {
           $data[]=array("status"=>200,"Message"=>"Success");
        }
        else
        {
            $data[]=array("status"=>404,"Message"=>"Failed");
        }
    }
    mysqli_close($con);

    return $data;
}

function updateData(){
	global $conn;

	$json = file_get_contents('php://input');
 
    $obj = json_decode($json,true);

	if(@$obj['id']){
     	@$id=$obj['id'];
       if($obj['name'] || $obj['email'] || $obj['dob'])
       {
             $query=mysqli_query($conn,"update user set name='".$obj['name']."',  email='".$obj['email']."',age='".$obj['age']."' ".$where);

         	if ($query==true) 
         	{
    		    $data[]=array("status"=>200,"Message"=>"success");
        	}
        	else
        	{
        		$data[]=array("status"=>404,"Message"=>"Failed !");
        	}
             	$where="where id=".$id;
            }
       }
    else
    {
    	$data[]=array("status"=>404,"Message"=>"Update record karneka to ID mangta re baba, 1st class ID de mai Entry Updte marta !"); 
    }
    
   return $data;
}

function removeData(){

	global $conn;
     
    $json = file_get_contents('php://input');
 
    $obj = json_decode($json,true);

     if(@$obj['id']){
     	@$id=$obj['id'];
 
        $query=mysqli_query($conn," DELETE sp_general_details.*, sp_address_details.*, sp_service_details.* 
                FROM
                ((sp_general_details
                    INNER JOIN sp_address_details ON sp_general_details.id = sp_address_details.id)
                    INNER JOIN sp_service_details ON sp_address_details.id = sp_service_details.id)
                    WHERE sp_general_details.id=".$id);
         	if ($query==true) 
         	{
    		    $data[]=array("status"=>200,"Message"=>"Entry Deleted of ID".$id);
        	}
        	else
        	{
    		    $data[]=array("status"=>404,"Message"=>"Failed !");
    	    }
     }
     else
     {
     	$data[]=array("status"=>200,"Message"=>"yeda zalay ka akha Table udawshil zhandubam id pass kr chupchap");
     }
   return $data;
}


function response($data){
	echo  json_encode($data);
}

 ?>
