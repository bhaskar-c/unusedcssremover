<?php $prefill = 'http://knowpapa.com/';
/*http://knowpapa.com/num2words/';
http://knowpapa.com/sitemap/
http://knowpapa.com/tarot-divinations-android-app/';*/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		
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
								$(".retain_unused_msg_form").show();
								var num_of_columns=10;
								var totalnumofitems = response.unused.length + response.used.length;
								var unused_items = '<h3>Unused :'+ response.unused.length + ' items(' + Math.round( (response.unused.length*100/totalnumofitems), 2)+ '%)</h3><div>' ;
								unused_items +=  'The following unused items will be removed from the final css.<br>Select items you do not want removed and then press the "Generate CSS" button';
								
							    unused_items = unused_items+'<table><tr>';
								var unused_item_counter=1;
								$.each(response.unused, function(index, value) {
									if( (unused_item_counter%num_of_columns)==0)
										unused_items = unused_items + '<td><label><input name="unused" value="'+ index+'" type="checkbox"/>'+value+'</label></td></tr><tr>';
									else
										unused_items = unused_items + '<td><label><input name="unused" value="'+ index+'" type="checkbox"/>'+value+'</label></td>';
										unused_item_counter++;
										});
								unused_items=unused_items+'</tr></table></div>';
								
								//used items table
								var used_items =
								'<h3> Used :'+ response.used.length + ' items(' + Math.round((response.used.length*100/totalnumofitems), 2)+ '%)</h3><div>';
								var used_items= used_items +'<table><tr>';
								var used_item_counter=1;
								$.each(response.used, function(index, value) {
									if( (used_item_counter%num_of_columns)==0)
										used_items = used_items + '<td>'+value+'</td></tr><tr>';
									else
										used_items = used_items + '<td>'+value+'</td>';
										used_item_counter++;
										});
								used_items=used_items+'</tr></table></div>';
								var unused_and_used = unused_items + used_items;
								$("#accordion-1").append(unused_and_used);
								$( "#accordion-1" ).accordion({heightStyle: "content", collapsible: true}); 
									}
								}
							});
		    	    event.preventDefault(); 
		    	});
		    	
				//form  step 3
		    	$("form[name='make_final_css']").submit(function(event) {  
					$(".retain_unused_msg_form").show();
				var do_not_remove_items = $('input[name="unused"]:checked').map(function() {return this.value;}).get();
				console.log(do_not_remove_items);
				$.ajax({ 
					type 		: 'POST', 
					data 		: {do_not_remove_items:do_not_remove_items},
					url 		: 'step_three.php', 
					success 	: function(response) {
						//$(".retain_unused_msg_form").hide();
						console.log(response);
						response =  JSON.parse(response); // do not remove this. this is needed atleast in this response
						//console.log(type(response));
						console.log(response);
						if (response.success) { 
							$("#accordion-1").prepend('<h3>Modified CSS</h3><div><pre>'+  response.content  +'</pre></div>');
							$( "#accordion-1" ).accordion("refresh"); 
									
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
			table {table-layout:fixed; width:650px; border-collapse: collapse;}
			 td { white-space: -o-pre-wrap; word-wrap: break-word;  white-space: pre-wrap; white-space: -moz-pre-wrap; 
				white-space: -pre-wrap; height: auto;    vertical-align: bottom;}
			.loading_image, .loaded_files, .throw_error, .retain_unused_msg_form {display: none; margin: 5px; width:650px;}
			table, th, td {   border: 1px solid #b5b5b5;} 
			tr:nth-child(odd){ background-color:#f5f5f5;}
			.throw_error {color:red;}1
			
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
						<input type="submit" value="Find Unused Items" /><br>
					</form>
		</div>
		
			<div class="retain_unused_msg_form"> 
				<form method="post" name="make_final_css">
					<input type="submit" value="Generate CSS" /><br>
				</form>
			</div>
			<div id="accordion-1">
			
					
		   </div>
		   
	</body>
</html>
