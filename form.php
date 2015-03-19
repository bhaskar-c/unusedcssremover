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
		<title>Simple Ajax Form</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>

		<script>
			$(document).ready(function() {
				
		    	$("form[name='step_one']").submit(function(event) { //Trigger on form submit
		    		$('.throw_error').empty(); //Clear the messages first
		    		$('.loaded_files').hide();
		    		var $inputs = $("form[name='step_one']").find("input, textarea");
					$inputs.prop("disabled", true);
					
					
		    		var step_one = { //Fetch form data
		    			'urls' 	: $('textarea[name=urls]').val(),
		    			'css_url' : $('input[name=css_url]').val()
	    		
		    		};
		    		
		    		$(".loading_image").prepend("Fetching URL contents. Please wait ! ");		
					$(".loading_image").show();
		    		$.ajax({ //Process the form using $.ajax()
		    			type 		: 'POST', //Method type
						url 		: 'step_one.php', //Your form processing file url
		    			data 		: step_one,
		    			success 	: function(response) {
							
		    			$(".loading_image").hide();
		    			response =  JSON.parse(response);
		    			console.log(response);
		    			if (response.success) { 
							console.log("success");
							$(".loaded_files").show();
							
		   				} else {
							console.log("no success");
							if (response.errors.urls) { 
		    					$('.throw_error').fadeIn(1000).html(response.errors.urls); 
		   					}
							
							}
		    			}
		    		});
		    	    event.preventDefault(); //Prevent the default submit
		    	});
		    
		    
		    //form2
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
								console.log(response);
								$("#unused_items").show();
								$("#used_items").show();
									var totalnumofitems = response.unused.length + response.used.length;
									$('#unused_items').fadeIn(1000).append( "<strong>Unused:</strong>"+ response.unused.length + "items" + (response.unused.length*100/totalnumofitems)+ "%<br>");
									$.each(response.unused, function(index, value) {
											$('#unused_items').fadeIn(1000).append( value + ", ");
										});
									
									$('#used_items').fadeIn(1000).append( "<strong>Used:</strong>"+ response.used.length + "items" + (response.used.length*100/totalnumofitems)+ "%<br>");
									$.each(response.used, function(index, value) {
											$('#used_items').fadeIn(1000).append( value + ", ");
										});
									}
								}
							});
		    	    event.preventDefault(); //Prevent the default submit
		    	});

		    
		    
		    
		    
		    
		    
		    
	});//matches dom
		</script>
		<style>
			.loading_image, .loaded_files, .throw_error, #unused_items, #used_items  {
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
					<label>URLs</label><br>
					<textarea name="urls" id="urls" rows="5" cols="40"><?php echo $prefill; ?></textarea><br>
					<label>URL of CSS File</label><br>
					<input type="text" name="css_url" value="http://knowpapa.com/wp-content/themes/corsa/assets/css/main.css"><br>
					<input type="submit" value="Send" /><br>
					
		</form>
		<span class="throw_error"></span><br>
		<div class="loading_image"><img src="ajax-loader.gif" /></div>
		<div class="loaded_files">
			All Files Loaded<br>
			Press Analyze Button to Analyze unused CSS<br>
					<form method="post" name="step_two">
						<input type="submit" value="Analyze CSS" /><br>
					</form>
		</div>
		<div id="unused_items"></div>
		<div id="used_items"></div>
			
		

		
	</body>
</html>
