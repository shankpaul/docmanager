<?php 

function get_documents()
{	
global $wpdb;
	$rows=$wpdb->get_results("select a.*,b.category_name from  doc_manager a,doc_category b  where a.category_id=b.category_id and a.disabled=0 order by a.post_date desc,doc_id desc");
?>
<table id="doc_manager_list" width="100%">
	<thead>
    	<th align="center" width="7%"><div align="center">No</div></th>
         <th width="5%">Type</th>
        <th width="30%">Title</th>
        <th width="15%">Category</th>
        <th width="12%"><div align="center">Date</div></th>
        <th align="center" width="15%"><div align="center">Download File</div></th>
    </thead>
    <tbody>
    <?php 
	$no=1;
		foreach($rows as $obj)
		{
	?>
    <tr>
    	<td align="center"><div align="center"><?php echo $no++; ?></div></td>
        <td><div class="icon <?php echo $obj->doc_ext;?>"></div></td>
        <td><?php echo $obj->title;?></td>
        <td><?php echo $obj->category_name;?></td>
         <td><div align="center"><?php echo date('Y-m-d',strtotime($obj->post_date));?></div></td>
        <td align="center"><div align="center"><a download="" href="<?php echo $obj->doc_url;?>">Download</a></div></td>
    </tr>
    <?php }?>
    </tbody>

</table>

<?php }?>