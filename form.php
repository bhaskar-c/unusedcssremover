<?php $urls_placeholder = 'http://home-cure.net/page/5/
http://home-cure.net/bronchitis/
http://home-cure.net/ayurvedic-cure-baldness/
http://home-cure.net/home-cure-whooping-cough/
http://home-cure.net/best-home-cure-urinary-tract-infection-home-remedies-uti/';

$css_placeholder = 'http://home-cure.net/wp-content/themes/custom/style.css';
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
		    		var step_one = { 
		    			'urls' 	: $('textarea[name=urls]').val(),
		    			'css_url' : $('input[name=css_url]').val(),
		    			'captcha_text' : $('input[name=captcha_text]').val()
		    		};
		    		$(".message").html("Fetching URL contents. Please wait ! ");		
					$(".loading_image").show();
		    		$.ajax({ 
		    			type 		: 'POST', 
						url 		: 'step_one.php', 
		    			data 		: step_one,
		    			success 	: function(response) {
							$(".loading_image").hide();
							response =  JSON.parse(response);
							if (response.success) { 
								var $inputs = $("form[name='step_one']").find("input, textarea");
								$inputs.prop("disabled", true);
								$(".message").html("All files loaded !");		
								$("#captcha").hide();
								$(".loaded_files").show();
								
							} else {
								if (response.errors.msg) { 
									$('.throw_error').fadeIn(1000).html(response.errors.msg); 
									$(".message").html("");
									}
								}
		    			}
		    		});
		    	    event.preventDefault(); 
		    	});
		    
		    
		    //form step 2
	    	$("form[name='step_two']").submit(function(event) { 
		    		$(".message").html("Analyzing CSS. Please wait ! ");	
		    		$(".loading_image").show();
		    		$(".loaded_files").hide();
		    		$.ajax({ 
		    			type 		: 'POST', 
						url 		: 'step_two.php', 
		    			success 	: function(response) {
							$(".loaded_files").hide();	
							$(".loading_image").hide();
							response =  JSON.parse(response);
							if (response.success) { 
								var num_of_columns=10;
								var total_num_of_items = response.unused.length + response.used.length;
								$(".generate_css_form").show();	
								$(".message").html('Unused: '+response.unused.length+'items.<br> Used:'+response.used.length+' items <br>' );	
								var unused_items = '<h3>Unused :'+ response.unused.length + ' items(' + Math.round( (response.unused.length*100/total_num_of_items), 2)+ '%)</h3><div>' ;
								unused_items +='<span style="color:#FB0404">These unused items will be removed from the modified css file.<br>You can retain any unused css items by click selecting it.<br> Items you select here will not be removed.  <br> Once you have selected items to retain, press the "Generate CSS" button</span>';
								
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
								'<h3> Used :'+ response.used.length + ' items(' + Math.round((response.used.length*100/total_num_of_items), 2)+ '%)</h3><div>';
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
					$(".message").html("Creating modified CSS. Please wait !<br> This may take upto 2 minutes ");	
					$(".loading_image").show();
					$(".generate_css_form").hide();
					//$(".generate_css_form").show();
					//var $button = $("form[name='make_final_css']").find("input");
					//$button.prop("disabled", true);
					var do_not_remove_items = $('input[name="unused"]:checked').map(function() {return this.value;}).get();
					$.ajax({ 
						type 		: 'POST', 
						data 		: {do_not_remove_items:do_not_remove_items},
						url 		: 'step_three.php', 
						success 	: function(response) {
							$(".loading_image").hide();
							response =  JSON.parse(response); 
							if (response.success) {
								$(".message").html("Here's the pruned css file:");	
								$("#accordion-1").prepend('<h3>Modified CSS</h3><div><pre>'+  response.content  +'</pre></div>');
								$( "#accordion-1" ).accordion({active:false,});
								$( "#accordion-1" ).accordion("refresh"); 
										
								}
							else {
								$(".message").hide();
								$('.throw_error').fadeIn(1000).html("sorry there was a problem, the developers have been notified"); 
								
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
			.throw_error {color:red;}
			label {cursor: pointer;}
			
		</style>
	</head>
	<body>
		<form method="post" name="step_one">
					<label>Enter up to 5 URLs(one per line). </label><br>
					<textarea name="urls" id="urls" rows="5" cols="40"><?php echo $urls_placeholder; ?></textarea><br>
					<label>URL of CSS File</label><br>
					<input type="text" name="css_url" id="css_url" value="<?php echo $css_placeholder; ?>"><br>
					<span id="captcha"><label>Captcha</label><br><img id="captcha_image" src="create_image.php" /><br>
					<input maxlength="5" size="5" type="text" name="captcha_text" /></span><br>
					<input type="submit" value="Send" />
		</form>
		<span class="throw_error"></span><br>
		<span class="message"></span>
		<span class="loading_image"><img src="ajax-loader.gif" /></span><br>
		<div class="loaded_files">
			 Click on go button to to analyze the CSS file<br> 
				<form method="post" name="step_two">
						<input type="submit" value="Go" /><br>
				</form>
		</div>
		<div class="generate_css_form"> 
				<form method="post" name="make_final_css">
					<input type="submit" value="Generate CSS" /><br>
				</form>
		</div>
		<div id="accordion-1"></div>
		   
	</body>
</html>
