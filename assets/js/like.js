function addLikes(id,site) {
	$('#likediv-'+id+' li').each(function(index) {
		$(this).addClass('selected');
		$('#likediv-'+id+' #rating').val((index+1));
		if(index == $('#likediv-'+id+' li').index(obj)) {
			return false;	
		}
	});
	$.ajax({
	url: "http://"+site+"/add_likes.php",
	data:'fav='+id,
	type: "GET",
	beforeSend: function(){
		$('#likediv-'+id+' .btn-likes').html("<img class='loading' src='http://"+site+"/assets/img/LoaderIcon.gif' />");
	},
	success: function(data){
	var likes = parseInt($('#likes-'+id).val());
		$('#likediv-'+id+' .btn-likes').html('<span data-toggle="" style="font-size: 10px;color: #000;">LIKED</span>');
		likes = likes+1;
	$('#likes-'+id).val(likes);
	if(likes>0) {
		$('#likediv-'+id+' .label-likes').html(likes+" Like(s)");
	} else {
		$('#likediv-'+id+' .label-likes').html('');
	}
	}
	});
}