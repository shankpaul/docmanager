<?php 
/*
 * Includes css, javascript and other thirdparty items.
 * 'DOC_MANAGER_PLUGIN_URL' is a globaly defined variable it contains plugin base url
 */
?>

<script language="JavaScript" src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo DOC_MANAGER_PLUGIN_URL; ?>css/doc-manager.css" />


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
  <br /> 
  
  <?php 
  if(@$_POST['doc_new_cat_submit'])
	{
		
		$rows_affected = $wpdb->insert("doc_category",
										array("category_name"=>$_REQUEST['doc_new_cat']),
										array('%s'));
		 if($rows_affected>0)
		 {
		 echo '<div class="updated fade below-h2"><p>Category Added</p></div>'; 
		 }
		 else
		  echo '<div class="updated fade below-h2"><p>Category Adding Failed</p></div>';
		 
	}
	else if(@$_POST['doc_new_ext_submit'])
	{
		
		$txt=str_replace('.','',$_POST['doc_new_ext']);
		$txt=preg_replace('/[^A-Za-z0-9\-]/', '', $txt);
		$cat_name=$wpdb->escape($txt);
		$rows_affected = $wpdb->insert("doc_extensions",
										array("ext_name"=>$cat_name),
										array('%s'));
		 if($rows_affected>0)
		 {
		 echo '<div class="updated fade below-h2"><p>Extension Added</p></div>'; 
		 }
		 else
		  echo '<div class="updated fade below-h2"><p>Extension Adding Failed</p></div>';
	}
	else if(@isset($_POST['doc_size']))
	{
		$int = filter_var(ini_get("upload_max_filesize"), FILTER_SANITIZE_NUMBER_INT);
		$format=strtoupper($_POST['size_fromat']);	
		$size=$_POST['size'];
		$mbsize=$size;
		if($format=='KB')
		{			
			$mbsize=$size/1024;
		}
		if($mbsize<=$int)
		{
			
				
			update_option("DocManagerFileSize",$size.'-'.$format);
			
			if($format=='MB')
			{
					$kb=$size*1024;
					$byte=$kb*1024;
			}
			else if($format=='KB')
			{				
					$byte=$size*1024;
			}
			
			update_option("DocManagerUploadFileSize",$byte);
			echo '<div class="updated fade below-h2"><p>Upload file size changed</p></div>';
		}
		else
		{
			echo '<div class="updated fade below-h2"><p>Error : Maximum file size suppoted by the server is '.ini_get("upload_max_filesize").'</p></div>'; 
			
		}
		
	}
	else if(@$_POST['np_upd_cat'])
	{
		$catid=$_POST['np_upd_catid'];
		$cat_name=$wpdb->escape($_POST['np_upd_cat_name']);
		$sql="update doc_category set category_name='$cat_name' where category_id='$catid'";
		$rs=$wpdb->query($sql);
		if($rs)
		{
			echo '<div class="updated fade below-h2"><p>Category Updated</p></div>'; 
		}
		else
			echo '<div class="updated fade below-h2"><p>Nothing Updated</p></div>'; 
		
	}
	else if(@$_POST['np_upd_ext'])
	{
		$catid=$_POST['np_upd_catid1'];
		$txt=str_replace('.','',$_POST['np_upd_cat_name1']);
		$txt=preg_replace('/[^A-Za-z0-9\-]/', '', $txt);
		$cat_name=$wpdb->escape($txt);
		$sql="update doc_extensions set ext_name='$cat_name' where ext_id='$catid'";
		
		$rs=$wpdb->query($sql);
		if($rs)
		{
			echo '<div class="updated fade below-h2"><p>Extension Updated</p></div>'; 
		}
		else
			echo '<div class="updated fade below-h2"><p>Nothing Updated</p></div>'; 
	}	
	
	else if(isset($_REQUEST['catid'],$_REQUEST['action'],$_REQUEST['status']))
	   {
		   if($_REQUEST['action']=="update")
		   {
			   $catid=$_REQUEST['catid'];
			   $status=$_REQUEST['status'];
			   if($status==1)
			   		$status=0;
			   else
			   		$status=1;
		   	   $rows_affected = $wpdb->query("update doc_category set disabled=$status where category_id=$catid"); 
		   }
	   
		if($_REQUEST['action']=="delete")
		   {
			  
			   $catid=$_REQUEST['catid'];
			   $status=$_REQUEST['status'];
			   $res=$wpdb->get_results("select count(category_id) as num from doc_manager where category_id='$catid'");
			   //echo "select count(category_id) as num from doc_manager where category_id='$catid'";
			   foreach($res as $val)
			   if($val->num==0)
			   {
				    
		   	   		$rows_affected = $wpdb->query("delete from doc_category where category_id=$catid"); 
					if($rows_affected)
					{
							 echo '<div class="updated fade below-h2"><p>Category Deleted</p></div>'; 
					}
			   }
			   else
			   {
				    echo '<div class="updated fade below-h2"><p>Category used in existing document . Plese delete all documents under same category.</p></div>';
			   }
		   
		   }
		   }
		else if(isset($_REQUEST['extid'],$_REQUEST['action'],$_REQUEST['status']))
	   {
		   
		   if($_REQUEST['action']=="update")
		   {
			   $catid=$_REQUEST['extid'];
			   $status=$_REQUEST['status'];
			   if($status==1)
			   		$status=0;
			   else
			   		$status=1;
					
					
		   	   $rows_affected = $wpdb->query("update doc_extensions set disabled=$status where ext_id=$catid"); 
		   }
	   
		if($_REQUEST['action']=="delete")
		   {
			  
			   $catid=$_REQUEST['extid'];
			   $status=$_REQUEST['status'];
			   
				    
		   	   		$rows_affected = $wpdb->query("delete from doc_extensions where ext_id=$catid"); 
					if($rows_affected)
					{
							 echo '<div class="updated fade below-h2"><p>Extension Deleted</p></div>'; 
					}
			  
		   
		   }
		   }
	
	
  ?>
  
  
  
  
  
 <form method="post" style="margin:10px;" onsubmit="return validateSize()">
     Maximum Upload Size
     <?php  
	 $size="";
	 $format="";
	// update_option("DocManagerFileSize",'2-MB');
	 $x=get_option("DocManagerFileSize");
	 //echo $x;
	 if($x!="")
	 {
	 	$tmp=preg_split('/-/',$x);
		if(is_array($tmp))
		{
			$size=$tmp[0];
			$format=$tmp[1];
		}
	 }
	 ?>
    <input type="text" name="size" id="size"  value="<?php echo $size; ?>"/>
    <span style="margin-left:5px; margin-right:5px;">
    <input type="radio" name="size_fromat" <?php if($format!="MB" || $format!="KB"){ echo 'checked="checked"'; } if($format=="KB") echo 'checked="checked"'; ?>   value="KB" /> KB </span><span style="margin-left:5px; margin-right:5px;">
    <input type="radio" name="size_fromat" value="MB" <?php if($format=="MB") echo 'checked="checked"'; ?> /> MB </span>
    <input type="submit" style="margin-left:20px;" class="np_button_style np_button" name="doc_size" value="Update" />
    <br />
     
    <span class="np_help"></span>
    </form>
    
<div style="width:40%; float:left; margin:10px; border:1px solid #E9E9E9; padding:10px;">
    	
     <form method="post" name="np_cat_add" onsubmit="return validateCat();">
     Category Name<br />
    <input type="text" name="doc_new_cat" id="np_new_cat" style="padding:5px; width:250px; margin-right:20px; " />
    <input type="submit" class="np_button np_button_style" name="doc_new_cat_submit" value="Add Category" />
 
    </form>
       <br />
       <?php
	   
	   
	   $rows = $wpdb->get_results("select * from doc_category");
	$no=0;
	    ?>
     <table width="100%" class="np_table">
        <thead>             
			<th scope="col" width="7%" align="center"  >No</th>
			<th scope="col" align="left">Name</th>
            <th scope="col" align="left" width="10%" >Edit</th>
            <th scope="col" align="left" width="12%">delete</th>
            <th scope="col" width="15%" align="left" >Status</th>         
        </thead>
		<tbody>
        <?php 
			foreach ($rows as $obj) {				
			$cat_st=$obj->disabled;	
			$show_cat_st="Enabled";		
			$cat_link_class="np_enabled";
			if($cat_st==1)
			{
				    $show_cat_st="Disabled";		
					$cat_link_class="np_disabled";
			}
		?>
        <tr>
        	<td width="5%" align="center"><?php echo ++$no; ?></td>
            <td><?php echo $obj->category_name; ?></td>
            <td ><span  class="np_edit" catid="<?php echo $obj->category_id; ?>" onclick="np_cat_inline_edit($(this))">Edit</span></td>
            <td><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&amp;ac=settings&action=delete&catid=<?php echo $obj->category_id; ?>&status=<?php echo $cat_st;?>" >Delete</a></td>
            <td><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&amp;ac=settings&action=update&catid=<?php echo $obj->category_id; ?>&status=<?php echo $cat_st;?>" class="<?php echo $cat_link_class; ?>"><?php echo $show_cat_st; ?></a></td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    <span class="np_help">Click on each status to change the category status</span>
    
    </div>
    
    
    <div style="width:40%; float:left; margin:10px; border:1px solid #E9E9E9; padding:10px;">
    	
     <form method="post" name="np_cat_add" onsubmit="return validateExt();">
   Add New File Extension <br />
    <input type="text" name="doc_new_ext" id="doc_new_ext" style="padding:5px; width:250px; margin-right:20px; " />
    <input type="submit" class="np_button np_button_style" name="doc_new_ext_submit" value="Add Extestions" />
 
    </form>
       <br />
       <?php
	   
	   
	   $rows = $wpdb->get_results("select * from doc_extensions");
	$no=0;
	    ?>
     <table width="100%" class="np_table">
        <thead>             
			<th scope="col" width="7%" align="center"  >No</th>
			<th scope="col" align="left">Name</th>
            <th scope="col" align="left" width="10%" >Edit</th>
            <th scope="col" align="left" width="12%">delete</th>
            <th scope="col" width="15%" align="left" >Status</th>         
        </thead>
		<tbody>
        <?php 
			foreach ($rows as $obj) {				
			$cat_st=$obj->disabled;	
			$show_cat_st="Enabled";		
			$cat_link_class="np_enabled";
			if($cat_st==1)
			{
				    $show_cat_st="Disabled";		
					$cat_link_class="np_disabled";
			}
		?>
        <tr>
        	<td width="5%" align="center"><?php echo ++$no; ?></td>
            <td><?php echo $obj->ext_name; ?></td>
            <td ><span  class="np_edit" catid="<?php echo $obj->ext_id; ?>" onclick="np_ext_inline_edit($(this))">Edit</span></td>
            <td><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&amp;ac=settings&action=delete&extid=<?php echo $obj->ext_id; ?>&status=<?php echo $cat_st;?>" >Delete</a></td>
            <td><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=doc-manager-home&amp;ac=settings&action=update&extid=<?php echo $obj->ext_id; ?>&status=<?php echo $cat_st;?>" class="<?php echo $cat_link_class; ?>"><?php echo $show_cat_st; ?></a></td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    <span class="np_help">Click on each status to change the extension status</span>
    
    </div>
    
    <div id="np_modal" class="np_modal"></div>
    <div class="np_window" id="np_cat_edit_window">
    	<h4>Edit Category Name</h4>
        <p>
        	<form method="post" name="np_cat_edit_form" onsubmit="return valid_edit_cat()" >
            <input type="hidden" value="" name="np_upd_catid" id="np_edit_cat_id" />
            
            <div align="center"><input id="np_edit_old_name" old="" style="width:250px; padding:5px;" type="text" value="" name="np_upd_cat_name" />
            
            <br />
              <span class="np_help">Edit category name</span>
              <br />
             <input type="submit" class="np_button np_button_style" name="np_upd_cat" value="Update Category" style="margin-top:15px;"  />

<input type="button" class="np_cancel_button np_button_style"  value="Cancel" style="margin-top:15px; margin-left:10px;" onclick="np_hide_np_window($(this))"  />
             </div>
            </form>
        </p>
    </div>
    
    
     <div class="np_window" id="np_cat_edit_window1">
    	<h4>Edit Extenstion</h4>
        <p>
        	<form method="post"  onsubmit="return valid_edit_ext()" >
            <input type="hidden" value="" name="np_upd_catid1" id="np_edit_cat_id1"  />
            
            <div align="center"><input id="np_edit_old_name1" old="" style="width:250px; padding:5px;" type="text" value="" name="np_upd_cat_name1" />
            
            <br />
              <span class="np_help">Edit file type</span>
              <br />
             <input type="submit" class="np_button np_button_style" name="np_upd_ext" value="Update Extenstion" style="margin-top:15px;"  />

<input type="button" class="np_cancel_button np_button_style"  value="Cancel" style="margin-top:15px; margin-left:10px;" onclick="np_hide_np_window1($(this))"  />
             </div>
            </form>
        </p>
    </div>
    
    
    
    <script language="javascript">
	function validateCat()
		{
			er="";
			str=$('#np_new_cat').val().trim();
			if(str=="")
			{
				er="Category name is blank";
			}
			if(er!="")
			{
					alert(er);
					return false;
			}
			else
			return true;
		}
		function validateExt()
		{
			er="";
			str=$('#doc_new_ext').val().trim();
			if(str=="")
			{
				er="Extension name is blank";
			}
			if(er!="")
			{
					alert(er);
					return false;
			}
			else
			return true;
		}
    	function validateSize()
		{
			
			size=$('#size').val();
			size=size.trim();
			er="";
			if(size=="")			
			{
				er+="Upload size  is blank\n";
			}
			else
			{
				if(isNaN(size))
				{
						er+="Invalid file size\n";
				}
				else
				{
					if(parseFloat(size)<=0)
					{
						er+="File size should be greater than zero\n";
						
					}
					
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
	function np_cat_inline_edit(id)
	{
		cat_name=id.closest('tr').find('td:eq(1)').text();
		cat_id=id.attr('catid');
		$('#np_edit_old_name').val(cat_name);
		$('#np_edit_old_name').attr('old',cat_name);
		$('#np_edit_cat_id').val(cat_id);	
		$('#np_modal').fadeIn('fast');
		$('#np_cat_edit_window').fadeIn('fast');
	}
	function np_ext_inline_edit(id)
	{
		cat_name=id.closest('tr').find('td:eq(1)').text();
		cat_id=id.attr('catid');
		$('#np_edit_old_name1').val(cat_name);
		$('#np_edit_old_name1').attr('old',cat_name);
		$('#np_edit_cat_id1').val(cat_id);	
		$('#np_modal').fadeIn('fast');
		$('#np_cat_edit_window1').fadeIn('fast');
	}
	function np_hide_np_window(id)
	{
		$('#np_modal').fadeOut('fast');
		$('#np_cat_edit_window').fadeOut('fast');
	}
	function np_hide_np_window1(id)
	{
		$('#np_modal').fadeOut('fast');
		$('#np_cat_edit_window1').fadeOut('fast');
	}
	function valid_edit_ext()
	{
		er="";
		if($('#np_edit_old_name1').val()=="")
		{
			er+="Extension Name is Blank";
		}
		if(er!="")
			{
					alert(er);
					return false;
			}
			else
			return true;
	}
	function valid_edit_cat()
	{
		er="";
		if($('#np_edit_old_name').val()=="")
		{
			er+="Category Name is Blank";
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