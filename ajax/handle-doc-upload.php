<?php 
function handle_upload_dm()
{
	global  $wpdb;
	$rows = $wpdb->get_results("select * from doc_extensions where disabled=0");
   $data_ext=array();
	foreach($rows as $obj)
	{
			array_push($data_ext,'".'.strtolower($obj->ext_name).'"');
	}
  if(isset($_FILES['file']))
  {
	  //date_default_timezone_set(get_option('timezone_string'));
		
		$uploadfiles = $_FILES['file'];	
			$picid= $_POST['picid'];	
		$fileurl="";
		$filedest="";
		$filetype="";
		if (is_array($uploadfiles)) 
		{
			
						$msg="";
						foreach ($uploadfiles['name'] as $key => $value) 
						{

							 // look only for uploded files
							if ($uploadfiles['error'][$key] == 0) 
							{
							
									$filetmp = $uploadfiles['tmp_name'][$key];
							
									//clean filename and extract extension
									$filename = $uploadfiles['name'][$key];
										$filetype = $uploadfiles['name']['type'];
										$tmp_ext=strtolower($filetype['ext']);
									
										/*if(!in_array($tmp_ext,$data_ext))
										{
											exit($picid);
											echo $tmp_ext;
												print_r($data_ext);
										}*/
							
									// get file info
									// @fixme: wp checks the file extension....
									$filetype = wp_check_filetype( basename( $filename ), null );
									$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
									$filename = $filetitle . '.' . $filetype['ext'];
									$upload_dir = wp_upload_dir();
							
									/**
									 * Check if the filename already exist in the directory and rename the
									 * file if necessary
									 */
									$i = 0;
									while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) 
									{
									  $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
									  $i++;
									}
									$filedest = $upload_dir['path'] . '/' . $filename;
									
									$fileurl= $upload_dir['url'] . '/' . $filename;
								
								
							
									/**
									 * Check write permissions
									 */
									if ( !is_writeable( $upload_dir['path'] ) ) 
									{
									  $msg.='Unable to write to directory %s. Is this directory writable by the server?';
									}
							
									/**
									 * Save temporary file to uploads dir
									 */
									if ( !@move_uploaded_file($filetmp, $filedest) )
									{
									  $msg.="Error, the file $filetmp could not moved to : $filedest ";							 
									}							
						     }//end if
						}//End for each						
			} //end if (is-array)
	
	$desc=$_POST['desc'];
	$title=$_POST['title'];
	$picid= $_POST['picid'];
	$catid=$_POST['category'];
	
	$data['category_id']=$catid;
	$data['title']=$title;
	$data['description']=$desc;	
	$data['doc_url']=$fileurl;	
	$data['doc_path']=$filedest;
	$data['doc_ext']=strtolower($filetype['ext']);
	$data['doc_type']=$filetype['type'];
	$data['post_date']=date('Y-m-d',time());
	$data['post_time']=time();
	
	//print_r($data);
	
	$format=array('%d','%s','%s','%s','%s','%s','%s','%s','%s');
				
	$rows_affected = $wpdb->insert("doc_manager",$data,$format);
	if($rows_affected>0)
		{
			echo $picid.'|';
		}		
  }//End if(@$_POST)
  
}

  
?>