<?php 
/*
 * Includes css, javascript and other thirdparty items.
 * 'DOC_MANAGER_PLUGIN_URL' is a globaly defined variable it contains plugin base url
 */
?>

<script language="JavaScript" src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo DOC_MANAGER_PLUGIN_URL; ?>css/doc-manager.css" />
<link rel="stylesheet" type="text/css" href="<?php echo DOC_MANAGER_PLUGIN_URL; ?>css/icon.css" />
<script language="JavaScript" src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>thirdparty/pager/jquery.tablePagination.min.js"></script>

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
  <form>
  <?php
	   
	   
	   $rows = $wpdb->get_results("select * from doc_category where disabled=0");
	
	    ?>
  <table>
  	<tr>
    	<td>
        	<td>
            	Select Category<br />
                <select name="doc_cat" id="doc_cat" class="doc_select" >
                <option selected="selected">Select Category</option>
                	<?php 
			foreach ($rows as $obj) {
				?>
                <option value="<?php echo $obj->category_id; ?>"><?php echo $obj->category_name; ?></option>
                <?php }?>
                </select>
            </td>
        </td>
    </tr>
  </table>
  <div style="margin-bottom:5px; margin-top:5px;">
  <div id="pager"></div>
  	<table  class="np_table no_files" id="list_docs_upload" width="100%">
    	<thead>
        	<th align="center" width="2%"><input type="checkbox" id="select_all" /></th>
            <th align="left" width="5%">File</th>
            <th align="left" width="20%">File Name</th>
            <th align="left" width="20%">Title</th>
            <th align="left" width="35%">Description</th>
            <th align="left" width="8%">Status</th>
        </thead>
        <tbody id="preview_list">
        	
        </tbody>
    </table>
    <div id="file_list" style="display:none;">
    	<input type="file" multiple="multiple" id="easy_123" class="easy_upd_file" name="file" onchange="show_list(this)" />
        
    </div>
  </div>
  
  <input type="button" onclick="easy_show_upload()" value="Add Files" class="np_button_style np_more_button" />
   <input type="button" onclick="delete_upload()" value="Delete Files" class="np_button_style np_cancel_button no_files" />
  
  
  <input style="float:right;" type="button" onclick="upload_files()" value="Upload Documents" class="np_button_style np_button no_files" />
  </form>
  <script language="javascript">
    var options = {
              currPage : 1,              
              optionsForRows : [5,10,15],
              rowsPerPage : 5,
              firstArrow : (new Image()).src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>images/first.gif",
              prevArrow : (new Image()).src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>images/prev.gif",
              lastArrow : (new Image()).src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>images/last.gif",
              nextArrow : (new Image()).src="<?php echo DOC_MANAGER_PLUGIN_URL; ?>images/next.gif",
              topNav : true
            }
       
  $(document).ready(function(){
	
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
	 });
	 
	 
	 
	 
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

function easy_show_upload()
{
		$('.easy_upd_file:last').click();
		
}
<?php 
   $rows = $wpdb->get_results("select * from doc_extensions where disabled=0");
   $data=array();
	foreach($rows as $obj)
	{
			array_push($data,'".'.$obj->ext_name.'"');
	}
?>
function isValidateImageSize(file,st)
{
	max_size=<?php echo get_option("DocManagerUploadFileSize");?>;
	if(file.size>max_size)
	{
		alert("File size is larger than allowed size");
		return false;
	}
	return true;
	
}

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
                  alert("Invalid File, \nAllowed extensions are: " + _validFileExtensions.join(", "));
				   id.closest('div').find('.np_error').show();
                    return false;
                }
            }

    return true;
}




function show_list(input)
{
	k=-1;
	fileok=false;
	var d = new Date();
	idno=d.getTime();
	no=$('.easy_upd_file').length;
	for(i=0;i<input.files.length;i++)
	{	
	if(isValidateImageSize(input.files[i],1))
	if(isValidateImage($(input),1))
	{
		k++;
		fileok=true;          
				$('.no_files,#tablePagination').fadeIn('slow');
				//alert(k);
				var filename=input.files[i].name;
				ext = filename.substr(filename.lastIndexOf('.') + 1);
				a='<td valign="top"" ><input  type="checkbox" index="'+k+'" fid='+$(input).attr('id')+'  class="chk_box upload_st"  /></td>';			
            	b='<td><div class="icon '+ext+'" title="'+ext+'"></div></td>';
				b1='<td>'+filename+'</td>';
				b2='<td><input type="text"  style="width:100%;" id="title_'+$(input).attr('id')+'_'+k+'" placeholder="Document Title" ></td>'
                c='<td><div class="img_info"><textarea style="width:100%;" id="desc_'+$(input).attr('id')+'_'+k+'" placeholder="Document Description"></textarea></div></td>';
                //d='<span class="delete" onclick="delete_image($(this))" file="'+$(input).attr('id')+'">delete</span>';
                e='<td><div class="status" status="0">Upload Pending</div></td>';
				
				$('#preview_list').prepend('<tr class="easy_prv" id="prev_'+$(input).attr('id')+'">'+a+b+b1+b2+c+e+'</tr>');
			//	   document.getElementById('#preview_list').scrollTop = document.getElementById('#preview_list').scrollHeight;
             
				//$('#show_select_all').fadeIn('fast');
			
				
	}
	}
	if(fileok)
	{
	$('#file_list').append('<input type="file" name="files[]" multiple="multiple" id="easy_'+no+idno+'" class="easy_upd_file" onchange="show_list(this)"   />');
	
	   $("#list_docs_upload").tablePagination(options);
	}
}


var remove_list=[];
function upload_files()
{
	if($('#doc_cat option:selected').val()!=$('#doc_cat option:first').val())
	{
	image_ulpload=true;
	count=$('.easy_upd_file').length;	
	for(i=0;i<count;i++)
	{
		upload_st=true;
		picid=$('.easy_upd_file:eq('+i+')').attr('id');	
		obj=document.getElementById(picid);
		for(j=0;j<obj.files.length;j++)
		{
			flag=true;
			for(x in remove_list)	
			{
				list=remove_list[x].split('^');
				list_id=list[0];
				list_inx=list[1];
				//alert(picid+'='+list_id+'^'+list_inx+'='+j);
				if(list_id==picid && j==list_inx)
				flag=false;
			}
			if(flag)
			{	
			
			
			
				$('.easy_prv').find('.status[status=0]').addClass('load_gif');
				$('.easy_prv').find('.status[status=0]').html('Uploading');
			
			var file = document.getElementById(picid).files[j]; 
			fd = new FormData();
			fd.append("file[]", file);
			fd.append("title", $('#title_'+picid+'_'+j).val());		
			fd.append("desc", $('#desc_'+picid+'_'+j).val());
			fd.append("category",$('#doc_cat option:selected').val());
			fd.append("picid",picid);
			fd.append("action",'docupload');
			
				$.ajax({		
					url:ajaxurl,
					data:fd,
					type: 'POST',
					//async:false,
					processData: false,
					contentType: false,		
					success:function(response)
					 {
						 if(response!="")
						 {
							tmp=response.split('|');
							data=tmp[0];
							cid='prev_'+data;
							$('.easy_prv').each(function(){
								
								if($(this).attr('id')==cid)
								{
									if($(this).find('.status').attr('status')==0)
									{	$(this).find('.status').removeClass('load_gif');
										 $(this).find('.status').html('Completed');
										 $(this).find('.status').attr('status','1');
										 tt=$(this).find('input[type=text]').val();
										 dsc=$(this).find('textarea').val();
										 $(this).find('input[type=text]').parent().html(tt);
										 $(this).find('input[type=text]').remove();
										 $(this).find('textarea').parent().html(dsc);
										 $('.upload_st').remove();
									}
								}
							})
							//$('#prev_'+data).find('.status').removeClass('load_gif');
							//$('#prev_'+data).find('.status').html('Completed');
							//$('#prev_'+data).find('.delete').hide('slow');
							$('#'+data).remove();
							upload_st=false;	
						 }
					}
				});
			}
		  }
		  
		
         
	}

	}
	else
	alert("Please select document category");
}

function delete_upload()
{
	$('#preview_list input[type=checkbox]:checked').each(function(){
		
		inx=$(this).attr('index');
		fid=$(this).attr('fid');
		obj=document.getElementById(fid);
		
		remove_list.push(fid+'^'+inx);
		$(this).closest('.easy_prv').remove();
		
		});
		if($('.easy_prv').length==0)
		{
			
			$('.no_files,#tablePagination').hide();
			document.getElementById('select_all').checked=false;
		}
}


  </script>