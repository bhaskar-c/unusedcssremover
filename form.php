<?php $prefill = 'http://knowpapa.com/
http://knowpapa.com/num2words/';
/*http://knowpapa.com/sitemap/
http://knowpapa.com/tarot-divinations-android-app/';*/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
		<script>
			$(document).ready(function() {
				//form step 1
		    	$("form[name='step_one']").submit(function(event) { 
		    		$('.throw_error').empty(); 
		    		$('.loaded_files').hide();
		    		var $inputs = $("form[name='step_one']").find("input, textarea");
					$inputs.prop("disabled", true);
		    		var step_one = { 
		    			'urls' 	: $('textarea[name=urls]').val(),
		    			'css_url' : $('input[name=css_url]').val()
		    		};
		    		$(".loading_image").prepend("Fetching URL contents. Please wait ! ");		
					$(".loading_image").show();
		    		$.ajax({ 
		    			type 		: 'POST', 
						url 		: 'step_one.php', 
		    			data 		: step_one,
		    			success 	: function(response) {
							
		    			$(".loading_image").hide();
		    			response =  JSON.parse(response);
		    			if (response.success) { 
							$(".loaded_files").show();
		   				} else {
							console.log("no success");
							if (response.errors.urls) { 
		    					$('.throw_error').fadeIn(1000).html(response.errors.urls); 
		   					}
							}
		    			}
		    		});
		    	    event.preventDefault(); 
		    	});
		    
		    
		    //form step 2
	    	$("form[name='step_two']").submit(function(event) { 
		    		$(".loading_image").show();
		    		$(".loading_image").prepend("Analyzing CSS. Please wait ! ");	
					$.ajax({ 
		    			type 		: 'POST', 
						url 		: 'step_two.php', 
		    			success 	: function(response) {
							$(".loaded_files").hide();	
							$(".loading_image").hide();
							response =  JSON.parse(response);
							if (response.success) { 
								$("#unused_items").show();
								$("#used_items").show();
								var num_of_columns=10;
								var totalnumofitems = response.unused.length + response.used.length;
								$('#unused_items').fadeIn(1000).append( "<strong>Unused :</strong>"+ response.unused.length + " items(" + Math.round( (response.unused.length*100/totalnumofitems), 2)+ "%)<br>");
							    var unused_items_table='<table border=\'1\'><tr>';
								var unused_item_counter=1;
								$.each(response.unused, function(index, value) {
									if( (unused_item_counter%num_of_columns)==0)
										unused_items_table = unused_items_table + '<td><label><input name="unused" value="'+ index+'"type="checkbox"/>'+value+'</label></td></tr><tr>';
									else
										unused_items_table = unused_items_table + '<td><label><input name="unused" value="'+ index+'"type="checkbox"/>'+value+'</label></td>';
										unused_item_counter++;
										});
								unused_items_table=unused_items_table+'</tr></table>';
								$('#unused_items').append(unused_items_table);

								//used items table
								$('#used_items').fadeIn(1000).append( "<strong>Used :</strong>"+ response.used.length + " items(" + Math.round((response.used.length*100/totalnumofitems), 2)+ "%)<br>");
								var used_items_table='<table border=\'1\'><tr>';
								var used_item_counter=1;
								$.each(response.used, function(index, value) {
									if( (used_item_counter%num_of_columns)==0)
										used_items_table = used_items_table + '<td>'+value+'</td></tr><tr>';
									else
										used_items_table = used_items_table + '<td>'+value+'</td>';
										used_item_counter++;
										});
								used_items_table=used_items_table+'</tr></table>';
								$('#used_items').append(used_items_table);

									}
								}
							});
		    	    event.preventDefault(); 
		    	});
		    	
				//form  step 3
		    	$("form[name='make_final_css']").submit(function(event) { 
				var do_not_remove_items = $('input[name="unused"]:checked').map(function() {return this.value;}).get();
				$.ajax({ 
					type 		: 'POST', 
					data 		: {do_not_remove_items:do_not_remove_items},
					url 		: 'step_three.php', 
					success 	: function(response) {
						console.log("tokay");
						response =  JSON.parse(response);
						//$("#unused_items").hide();
						//$("#used_items").hide();
						$("#final_css").show();
						if (response.success) { 
							
							$("#final_css").fadeIn(1000).append('<pre>'+  response.content  +'</pre>');
							}
						}
					});	
					event.preventDefault(); 
					});
	});//matches dom
		</script>
		<noscript>
			<div style="border: 1px solid purple; padding: 10px">
				<span style="color:red">You must enable JavaScript for this page to work !</span>
			</div>
		</noscript>
		<style>
			.loading_image, .loaded_files, .throw_error, #unused_items, #used_items, #final_css  {
				display: none;
				margin: 5px 2px;
			}
			.throw_error, #unused_items {
				color:red;
				}

			.loading_image, #used_items {
				color: green;
				}
		</style>
	</head>
	<body>
		<form method="post" name="step_one">
					<label>Enter up to 5 URLs(one per line). </label><br>
					<textarea name="urls" id="urls" rows="5" cols="40"><?php echo $prefill; ?></textarea><br>
					<label>URL of CSS File</label><br>
					<input type="text" name="css_url" value="http://knowpapa.com/wp-content/themes/corsa/assets/css/main.css"><br>
					<input type="submit" value="Send" /><br>
					
		</form>
		<span class="throw_error"></span><br>
		<div class="loading_image"><img src="ajax-loader.gif" /></div>
		<div class="loaded_files">
			All Files Loaded<br>
			Click Next to analyze unused CSS<br>
					<form method="post" name="step_two">
						<input type="submit" value="next" /><br>
					</form>
		</div>
		<div id="unused_items">
			The following unused items will be removed from the final css.<br>
			Select items you do not want removed and then press the "Generate CSS Button"<br>
			<form method="post" name="make_final_css">
				<input type="submit" value="Generate CSS" /><br>
			</form>
		</div>
		<div id="used_items"></div>
		<div id="final_css"></div>
	</body>
</html>
