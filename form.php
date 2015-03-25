<?php $urls_placeholder = 'http://home-cure.net/
http://home-cure.net/home-cure-whooping-cough/';

$css_placeholder = 'http://d27tu1smk19hj.cloudfront.net/wp-content/cache/minify/000000/M9BPLi0uyc_VLy6pzEnVMYBx0_LzShLLU4vzc1P1k4uLwXxdqAAA.css';
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
								$(".generate_css_form").show();
								var num_of_columns=10;
								var totalnumofitems = response.unused.length + response.used.length;
								var unused_items = '<h3>Unused :'+ response.unused.length + ' items(' + Math.round( (response.unused.length*100/totalnumofitems), 2)+ '%)</h3><div>' ;
								unused_items +=  '<span style="color:tomato">These unused  css items will be removed.<br>Select items you do not want removed and then press the "Generate CSS" button</span>';
								
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
					$(".generate_css_form").show();
					var $button = $("form[name='make_final_css']").find("input");
					$button.prop("disabled", true);
				var do_not_remove_items = $('input[name="unused"]:checked').map(function() {return this.value;}).get();
				$.ajax({ 
					type 		: 'POST', 
					data 		: {do_not_remove_items:do_not_remove_items},
					url 		: 'step_three.php', 
					success 	: function(response) {

						response =  JSON.parse(response); 
						if (response.success) { 
							$("#accordion-1").prepend('<h3>Modified CSS</h3><div><pre>'+  response.content  +'</pre></div>');
							$( "#accordion-1" ).accordion("refresh"); 
									
							}
						}
					});	
					event.preventDefault(); 
					});
					
					//placeholder for textarea
					var textarea_placeholder = $('#urls').val();
					$('#urls').focus(function() {if ($(this).val() == textarea_placeholder)	$(this).val("");});
					$('#urls').blur(function() {if ($(this).val() == "") $(this).val(textarea_placeholder);	});
					//css text placeholder
					var css_placeholder = $('#css_url').val();
					$('#css_url').focus(function() {if ($(this).val() == css_placeholder)	$(this).val("");});
					$('#css_url').blur(function() {if ($(this).val() == "") $(this).val(css_placeholder);	});
					
					

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
			.loading_image, .loaded_files, .throw_error, .generate_css_form {display: none; margin: 5px; width:650px;}
			table, th, td {   border: 1px solid #b5b5b5;} 
			tr:nth-child(odd){ background-color:#f5f5f5;}
			.throw_error {color:red;}1
			
		</style>
	</head>
	<body>
		<form method="post" name="step_one">
					<label>Enter up to 5 URLs(one per line). </label><br>
					<textarea name="urls" id="urls" rows="5" cols="40"><?php echo $urls_placeholder; ?></textarea><br>
					<label>URL of CSS File</label><br>
					<input type="text" name="css_url" id="css_url" value="<?php echo $css_placeholder; ?>"><br>
					<input type="submit" value="Send" /><br>
					
		</form>
		<span class="throw_error"></span><br>
		<div class="loading_image"><img src="ajax-loader.gif" /></div>
		<div class="loaded_files">
			All set to go !<br> Click <i>Go</i> to analyze the CSS fie.
					<form method="post" name="step_two">
						<input type="submit" value="Analyse" /><br>
					</form>
		</div>
		
			<div class="generate_css_form"> 
				<form method="post" name="make_final_css">
					<input type="submit" value="Generate CSS" /><br>
				</form>
			</div>
			<div id="accordion-1">
			
					
		   </div>
		   
	</body>
</html>
