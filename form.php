<?php $urls_placeholder = 'http://example.com
http://example.com/page/1
http://example.com/post/1
http://example.com/contact
http://example.com/category/1';

$css_placeholder = 'http://example.com/style.css';
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

				//some prelimnary validations 
				validate();
				$('#urls, #css_url, #captcha_text').keyup(validate);
				
			
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
								$(".message").html('Unused: '+response.unused.length+'items.<br> Used: '+response.used.length+' items. <br>' );	
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
								$(".message").html('Here\'s the pruned css file. <br>Backup your original css file before replacing it with this file. Things may go wrong. You have been warned !!');	
								$("#accordion-1").prepend('<h3>Modified CSS</h3><div><pre>'+  response.content  +'</pre></div>');
								$( "#accordion-1" ).accordion({active:false});
								$( "#accordion-1" ).accordion("refresh"); 
										
								}
							else {
								$(".message").hide();
								$('.throw_error').fadeIn(1000).html('sorry an unexpected error occurred ! Please notify the developer'); 
								
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
	
	function validate(){
		if ($('#urls').val().length   >   0   &&
			$('#urls').val().indexOf("example.com") < 0  &&
			are_all_valid_urls($('#urls').val()) &&
			$('#css_url').val().length  >   0   &&
			$('#css_url').val().indexOf("example.com") < 0  &&
			is_valid_url($('#css_url').val()) &&
			$('#captcha_text').val().length    >   0) {
			$(".send_button").prop("disabled", false);
		}
		else {
			$(".send_button").prop("disabled", true);
		}
	}
	
	function are_all_valid_urls(entered){
		urls_array = entered.split(/\n/);
		for(var i in urls_array) {
			if (!is_valid_url(urls_array[i])){return false;}
      		}
		return true;
	}
	
	function is_valid_url(url) {
		var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
		return regexp.test(url);
	}

	
	
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
			.loading_image, .loaded_files, .throw_error, .generate_css_form {display: none; margin: 5px 0; width:650px;}
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
					<input maxlength="5" size="5" type="text" id="captcha_text" name="captcha_text" /></span><br>
					<input class="send_button" type="submit" value="Next" />
		</form>
		<span class="throw_error"></span><br>
		<span class="message"></span>
		<span class="loading_image"><img src="ajax-loader.gif" /></span><br>
		<div class="loaded_files">
			 Click on <i>Go</i> button to to analyze the CSS file<br> 
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
		<h3>Notice/ Warning</h3>
		<ul>
			<li>Please BACKUP YOUR OLD CSS before replacing it with new CSS. I do not take any reposnsibility for broken css rules or ugly looking sites.</li>
			
		<li>
			<strong>Wrong CSS Output:</strong>The code is currently a beta release. Please report all errors on this <a href="https://github.com/quakig/unusedcssremover"> Github</a> issues page. Another reason why you should backup your original css before replacing it with the modified css.</li>
		<li><strong>Maximum 5 urls ?:</strong>
		This restriction is due to my hosting account bandwidth constraint. You can always download the code from <a href="https://github.com/quakig/unusedcssremover"> Github</a> repository and run it on your localhost for unlimited number of urls.</li>
		<li><strong>Reporting Errors/ Suggestions/ Feedback: </strong>
				Problems with your CSS file ? Found issues with the code ?  Report all bugs at <a href="https://github.com/quakig/unusedcssremover/issues"> here</a></li>
		<li><strong>Contribute to the code ?</strong> The code is licensed under the MIT License. Please contribute to the development of the code at <a href="https://github.com/quakig/unusedcssremover"> Github</a>.</li>
			
		</ul>
 
	</body>
</html>
