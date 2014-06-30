$(document).ready( function() 
	{
	$(function(){
	$('a.delete_file').click(function() 
		{
			if(window.confirm("Da li zaista želite da obrišete fajl?"))
			{
				$.post
				('/file-manager/FileManager/delete/',
					{ 
						'upload_id' : $(this).attr('param1') 
					}, 
					function(itemJson)
					{
						$("#div" + itemJson.id).html('Fajl je obrisan!');
						$("#div" + itemJson.id).css('color','red');
						alert('Fajl sa rednim brojem: '+itemJson.id+' je uspešno obrisan!');
					}, "json");
			}
	    });
	});
});
   
