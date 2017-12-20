function addSaves(id,site) {
	$('#savediv-'+id+' li').each(function(index) {
		$(this).addClass('selected');
		$('#savediv-'+id+' #rating').val((index+1));
		if(index == $('#savediv-'+id+' li').index(obj)) {
			return false;	
		}
	});
	$.ajax({
	url: "http://"+site+"/add_saves.php",
	data:'save='+id,
	type: "GET",
	beforeSend: function(){
		$('#savediv-'+id+' .btn-saves').html("<img class='loading' src='http://"+site+"/assets/img/LoaderIcon.gif' />");
	},
	success: function(data){
	var saves = parseInt($('#saves-'+id).val());
		$('#savediv-'+id+' .btn-saves').html('<span data-toggle="" style="font-size: 10px;color: #000;">SAVED</span>');
		saves = saves+1;
	$('#saves-'+id).val(saves);
	if(saves>0) {
		$('#savediv-'+id+' .label-saves').html(saves+" save(s)");
	} else {
		$('#savediv-'+id+' .label-saves').html('');
	}
	}
	});
}