<?php 
/*
 * Includes css, javascript and other thirdparty items.
 * 'DOC_MANAGER_PLUGIN_URL' is a globaly defined variable it contains plugin base url
 */
?>

<script language="JavaScript" src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo DOC_MANAGER_PLUGIN_URL; ?>css/doc-manager.css" />
<link rel="stylesheet" type="text/css" href="<?php echo DOC_MANAGER_PLUGIN_URL; ?>css/icon.css" />
<script language="JavaScript" src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>thirdparty/sort/jquery.tablesorter.min.js"></script>
<script language="javascript">

function putcenter(id)
    {
       id.css("top", ( $(window).height() -   id.height() ) / 2);
       id.css("left", ( $(window).width() -  id.width() ) / 2);
    }
</script>
<div class="wrap">
 <div  class="doc_manger_ico"></div>
    <h2 style="padding-top:20px;">DOC MANAGER</h2>
    <div class="tablenav">    
    <h2>
<a class="button add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home">Home</a>	 
<a class="button add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&ac=settings">Settings</a>	
<a class="button add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&ac=upload">Attach Documents</a>
    </h2>
  </div>
  <?php 
  	if(isset($_REQUEST['action']))
  {
	  if($_REQUEST['action']=='status'&&isset($_REQUEST['status'],$_REQUEST['docid']))
	  {
		  
		  $status=$_REQUEST['status'];
		  $docid=$_REQUEST['docid'];
		  if($status==1)
			   $status=0;
		  else
		  $status=1;
		// echo "update doc_manager set disabled=$status where doc_id=$docid";
			 $rows_affected = $wpdb->query("update doc_manager set disabled=$status where doc_id=$docid"); 
		  
	  }
	  else if($_REQUEST['action']=='delete'&&isset($_REQUEST['docid']))
	  {		  		 
		  $docid=$_REQUEST['docid'];
		  $rows=$wpdb->get_results("select doc_path from doc_manager   where doc_id=$docid ");			
		  $rs = $wpdb->query("delete from  doc_manager  where doc_id=$docid");
		  if($rs)
			{
					foreach($rows as $obj)
					{
							@unlink($obj->doc_path);
					}
			}
	  }
	  
  }
  if(@$_POST['doc_delete'])
	   {
		  
		   if(isset($_POST['del_doc']))
		   {
			
			$data=$_POST['del_doc'];
		   	$list_id=implode(",",$data);
			$rows=$wpdb->get_results("select doc_path from doc_manager   where doc_id in($list_id) ");
			$rs=$wpdb->query("delete from doc_manager where doc_id in($list_id)");
			if($rs)
			{
					foreach($rows as $obj)
					{
							@unlink($obj->doc_path);
					}
			}
		   }
			
			
	   }
	   
	   
	   //EDIT
	else if(@$_POST['edit_doc'])
  {
	
	  	  	date_default_timezone_set(get_option('timezone_string')); 
	  	
		 if(isset($_FILES['easy_file_edit']))
  		{
			 
	   //date_default_timezone_set(get_option('timezone_string')); 
		$uploadfiles = $_FILES['easy_file_edit'];	
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
		}
	
	$desc=$_POST['change_desc'];
	$ttl=$_POST['change_title'];
	$id=$_POST['docid'];
	$catid=$_POST['category'];
	$doc_ext=strtolower($filetype['ext']);
	$doc_type=$filetype['type'];
	
	global  $wpdb;
	if($fileurl!="")
	{
		
		$sql="update doc_manager set category_id='$catid',description='$desc',title='$ttl',doc_path='$filedest',doc_url='$fileurl',doc_ext='$doc_ext', doc_type='$doc_type' where doc_id=$id";
		//echo $sql;
	}
	else
	{
		$sql="update doc_manager set category_id='$catid',description='$desc',title='$ttl'where doc_id=$id";
	}
	//echo $sql;
	$rows_affected = $wpdb->query($sql);
	if($rows_affected>0)
	{
		$pageno="";
		if(isset($_REQUEST['paging']))
		$pageno='&paging='.$_REQUEST['paging'];
		$srch="";
			if(isset($_REQUEST['searchtxt']))
			$srch="&searchtxt=".$_REQUEST['searchtxt'];
		wp_redirect( get_option('siteurl').'/wp-admin/admin.php?page=doc-manager-home'.$srch.$pageno);
	}	
	else
	{
		$pageno="";
		if(isset($_REQUEST['paging']))
		$pageno='&paging='.$_REQUEST['paging'];
		$srch="";
			if(isset($_REQUEST['searchtxt']))
			$srch="&searchtxt=".$_REQUEST['searchtxt'];
		wp_redirect( get_option('siteurl').'/wp-admin/admin.php?page=doc-manager-home'.$srch.$pageno);
		//echo '<div class="updated fade below-h2"><p>Nothing Updated</p></div>'; 
	}
			
					
  }//End if(@$_POST)
	   
	   
  
  ?>
  
  
  
  <br /> 
  <?php 
  if(isset($_REQUEST['searchtxt']))
  {
	  $txt=$wpdb->escape(trim($_REQUEST['searchtxt']));
	  $rows=$wpdb->get_results("SELECT * FROM doc_manager a, doc_category b WHERE a.`category_id`=b.`category_id` AND ( lower(a.title) like lower('%$txt') or   lower(a.description) like lower('%$txt') or  lower(b.category_name) like lower('%$txt')) ");

  }
  else
  {
  	$rows=$wpdb->get_results("select * from doc_manager");
	}
	   $items=count($rows);
	   $limit="";
	   if($items > 0)
			{				
			$srch="";
			if(isset($_REQUEST['searchtxt']))
			$srch="&searchtxt=".$_REQUEST['searchtxt'];
					$p = new pagination;
					$p->items($items);
					$p->limit(8); // Limit entries per page
					$p->target(get_permalink()."admin.php?page=doc-manager-home&$srch");
					//$p->urlFriendly();
					$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
					$p->calculate(); // Calculates what to show
					$p->parameterName('paging');
					$p->nextLabel('');//removing next text
					$p->prevLabel('');//removing previous text
					$p->nextIcon('&#9658;');//Changing the next icon
					$p->prevIcon('&#9668;');//Changing the previous icon
					$p->adjacents(1); //No. of page away from the current page
					 
					if(!isset($_GET['paging'])) 
					{
						$p->page = 1;
					} else {
						$p->page = $_GET['paging'];
					}
			 
					//Query for limit paging
					$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;
					 
			}
  
  ?>
  <div id="pager" style="; text-align:right;height:auto;">
 <span style="float:left;">
 <form method="post" onsubmit="return valdSearch()">
  <input type="text" name="searchtxt" placeholder="Search Documents" id="searchtxt" value="<?php if(isset($_REQUEST['searchtxt'])) echo $_REQUEST['searchtxt'];  ?>" style="width:200px;" /> <input type="submit"  name="search" value="Search" style="padding:2px;padding-left:4px;padding-right:4px;font-size:12px;" class="np_button_style np_button" />
  </form>
  </span>
  
  
   	<?php if($items > 0) echo $p->show(); ?>
  
   </div>
     <br style="clear:both;" />
   <form method="post" >
  <table class="np_table" width="100%" id="list_all_doc">
  	<thead>
    	<th width="3%" align="center"><input type="checkbox" name="select_all" id="select_all" /></th>
        <th width="2%" align="center" >Type</th>
        <th width="15%" align="left">Title</th>
        <th width="23%" align="left">Description</th>
         <th width="7%" align="left">category</th>
        <th width="6%" align="left">Post Date</th>
        <th width="6%" align="left">Action</th>
        <th width="5%" align="left">Status</th>
        <th width="8%" align="left" >Download File</th>
    </thead>
    <tbody>
    <?php
	if(!isset($_REQUEST['searchtxt']))
	{
	$rows=$wpdb->get_results("SELECT a.*,b.category_name FROM doc_manager a, doc_category b WHERE a.category_id=b.category_id order by doc_id desc $limit");
	
	}
	else
	{
		$txt=$wpdb->escape(trim($_REQUEST['searchtxt']));
		$rows=$wpdb->get_results("SELECT * FROM doc_manager a, doc_category b WHERE a.`category_id`=b.`category_id` AND ( lower(a.title) like lower('%$txt') or   lower(a.description) like lower('%$txt') or  lower(b.category_name) like lower('%$txt')) ");
		
	}
	if(count($rows)>0)
	{
		$srch="";
			if(isset($_REQUEST['searchtxt']))
			$srch="&searchtxt=".$_REQUEST['searchtxt'];
			$pageno="";
		if(isset($_REQUEST['paging']))
		$pageno='&paging='.$_REQUEST['paging'];
	foreach($rows as $obj)
	{
				$doc_st=$obj->disabled;	
				$show_doc_st="Enabled";		
				$doc_link_class="np_enabled";
				
				if($doc_st==1)
				{
						$show_doc_st="Disabled";		
						$doc_link_class="np_disabled";
				}
	 ?>
    	<tr>
        	<td align="center"><input type="checkbox" name="del_doc[]" value="<?php echo $obj->doc_id; ?>" /></td>
            <td><div class="icon <?php echo $obj->doc_ext; ?>"></div></td>
            <td><?php echo $obj->title; ?></td>
            <td><?php echo $obj->description; ?></td>
             <td><?php echo $obj->category_name; ?></td>
            <td><?php echo date("F j, Y", strtotime($obj->post_date));  ?></td>
            <td><span class="edit"><a title="Edit this document" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&action=edit&docid=<?php echo $obj->doc_id; echo $srch.$pageno; ?>">Edit</a> | </span>
						<span class="trash"><a title="Delete This Document" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&action=delete&docid=<?php echo $obj->doc_id; echo $srch.$pageno; ?>" onclick="return confirm('Do you want to delete this Document? ');" >Delete</a></span> </td>
            <td>
            <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&action=status&docid=<?php echo $obj->doc_id; ?>&status=<?php echo $doc_st; echo $srch.$pageno;?>" class="<?php echo $doc_link_class; ?>"><?php echo $show_doc_st; ?></a>
            
            </td>
            <td><span class="edit"><a download="" title="Download Attachment" target="_new" href="<?php echo $obj->doc_url; ?>">Download</a> </span></td>
        </tr>
        <?php }}
		else {
		?>
        <tr>
        	<td colspan="8" align="center">No Data Found</td>
        </tr>
        <?php } ?>
        
    </tbody>
  </table>
  <div style="margin-top:10px;">
  	<input type="submit" value="Delete Selected" name="doc_delete" class="np_button_style np_cancel_button"  />
  </div>
  </form>
   <div class="np_modal" id="easy_upload_modal" ></div>
  
<?php
if($_REQUEST['action']=='edit'&&isset($_REQUEST['docid']))
	  {
		  
		 
		  $docid=$_REQUEST['docid'];
		  $rows=$wpdb->get_results("select * from doc_manager where doc_id=$docid");
		  foreach($rows as $obj);
		 
	  
 ?>
 <script language="javascript">
 putcenter($('#np_edit_form'));
 $('#easy_upload_modal').show();
 
 </script>
 
  <form method="post" enctype="multipart/form-data" onsubmit="return doc_validate_edit()" >
<div class="np_upload_form np_edit_form" id="np_edit_form">
	<h4>Edit Document <span onclick="hide_doc_edit()">close</span> </h4>
    <div class="upd_content" > 
   
    <input type="hidden" value="<?php echo $obj->doc_id; ?>" name="docid" />
    	<table width="100%" >
        	<tr>
            	<td valign="bottom">
                	<div class="icon <?php echo $obj->doc_ext; ?>"></div>

          		  </td>
                
            </tr>
        	<tr>
<td valign="bottom">File Type : <?php echo $obj->doc_type; ?>
	

</td>
                
            </tr>
            <tr>
            	<td>
                Category<br />
                	<select style="width:350px;float:left;" name="category" id="doc_cat">
    <option value="0"  >Select category</option>
    <?php  
		$rows1=$wpdb->get_results("select * from doc_category where disabled=0");
		foreach($rows1 as $obj1){
				
		 ?>
    	<option  <?php if($obj1->category_id==$obj->category_id)echo 'selected="selected"'; ?>  value="<?php echo $obj1->category_id; ?>"><?php echo $obj1->category_name; ?></option>
        <?php }?>
    </select>
                </td>
            </tr>
            <tr>
            	<td>Change Image<br />
                	<input type="file"  style="width:80%;margin-top:10px;" name="easy_file_edit[]" value="" id="change_doc1"  onchange="isValidateImage($(this),1)" />
                     

                   
                </td>
                
            </tr>
            <tr>
            	<td>Title<br />
	               <input type="text" name="change_title" style="width:100%;" value="<?php echo $obj->title; ?>" />
                </td>
            </tr>
            <tr>
            	<td>Description<br />
	                <textarea name="change_desc" style="resize:none; width:100%;height:120px;" id="change_desc"><?php echo $obj->description; ?></textarea>
                </td>
            </tr>
            <tr>
           	  
            </tr>
        </table>
       
    </div>
    <div class="upd_control">
    <div style="padding:12px;">
    		<input type="submit" name="edit_doc" value="Update" class="np_button_style np_button "  /> 
            	<input type="button" name="change_photo_cancel" value="Cancel" class="np_button_style np_cancel_button " onclick="hide_doc_edit()"  />
    </div>
    </div>
</div>
 </form>
<?php }?>
  
  
  
  
  
  <script language="javascript">
  
  $("#list_all_doc").tablesorter({ 
        // pass the headers argument and assing a object 
        headers: { 
            // assign the secound column (we start counting zero) 
			0: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            }, 
            1: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            } 
            , 
           6: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            } ,
			8: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            } 
            
			
        } 
    });
	
	$('#select_all').click(function(){
	 if(document.getElementById('select_all').checked)
		{
			
			//$('.chk_box').attr('checked',true);
			checkAll( true);
		}
		else
		{
		//	$('.chk_box').attr('checked',false);
		checkAll( false);
				
		}
	
	 });
	 
	 
	
function hide_doc_edit()
{
	$('#easy_upload_modal').hide();
	$('#np_edit_form').fadeOut('fast');
	url=(document.location.href);
	url=url.replace('&action=edit','');
	document.location.href=url;
	
	
} 
	 
 function checkAll( checktoggle)
{
  var checkboxes = new Array(); 
  checkboxes = document.getElementsByTagName('input');
 
  for (var i=0; i<checkboxes.length; i++)  {
    if (checkboxes[i].type == 'checkbox')   {
      checkboxes[i].checked = checktoggle;
    }
  }
}
	<?php 
   $rows = $wpdb->get_results("select * from doc_extensions where disabled=0 ");
   $data=array();
	foreach($rows as $obj)
	{
			array_push($data,'".'.$obj->ext_name.'"');
	}
?>
	
function isValidateImage(id,st) {
		filename=id.val();
		//alert(filename);
   var _validFileExtensions = [<?php echo implode($data,','); ?>];
            var sFileName =filename;
            if (sFileName.length > 0) 
            {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++)
                {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
						
						id.closest('div').find('.np_error').hide();
						//alert(filename);
                        break;
                    }
                }

                if (!blnValid) {
				
					if(st)
                  alert("Sorry, " + sFileName + " is an invalid file, \n Allowed extensions are: " + _validFileExtensions.join(", "));
				   id.closest('div').find('.np_error').show();
                    return false;
                }
            }

    return true;
}

function isValidateImageSize(file,st)
{
	max_size=<?php echo get_option("DocManagerUploadFileSize");?>;
	
	if(file.size>max_size)
	{
		
		if(st!=0)
			alert("File size is larger than allowed size");
		return false;
	}
	return true;
	
}
function doc_validate_edit()
{
	er="";
	if($('#doc_cat option:first').val()==$('#doc_cat option:selected').val())
	{
		er+="Please Select Category";
	}
	if($('#change_doc1').val()!="")
	{
		input=document.getElementById('change_doc1').files[0];
		if(!isValidateImage($('#change_doc1'),0))
		{
			er+="Invalid Document Type";
		}
		else if(!isValidateImageSize(input,0))
		{
				er+="File size is larger than allowed size";
		}
		
		
	}
	if(er!="")
			{
					alert(er);
					return false;
			}
			else
			return true;
}
function valdSearch()
{
	er="";
	if($('#searchtxt').val().trim()=='')
	{
		er+="Nothing to Search";
	}
	
	if(er!="")
			{
					alert(er);
					return false;
			}
			else
			return true;
}
  </script>