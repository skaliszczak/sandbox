<?php session_start() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
<head>
	<title>OPTeam PHP Framework - Sandbox&trade;</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="../dashboard/themes/default/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<style type="text/css">
		* {	padding: 0;	margin: 0	}
		head, body	{height: 100%; width: 100%;	}
		#footer	{bottom:0px;left:0px;right:0px;height:60px;position:absolute}
		#wrapper{top:0px;left:0px;right:0px;bottom:60px;position:absolute}
		#code	{top:0px;bottom:0px;left:0px;width:49%;overflow-y:auto;position:absolute;height: 100%; border: 0 none;resize: none; font-size: 14px;border-right: 1px solid #EEEEEE;padding-right:1%}
		#output	{top:0px;bottom:0px;right:0px;width:49%;overflow-y:auto;position:absolute}
		
		#footer {
			border-top: 1px solid #CFCFCF;
			box-shadow: 0 0 61px rgba(50, 50, 50, 0.51);
			background: #000000;
			color: white;				
		}
		#footer h1	{	
			color: #00F2FF; 
			font-family: Nexa, Arial, sans-serif; 
			font-size: 2em; 
			padding: 10px; 
			font-weight: normal; 
			margin-left: 4px;
		}
		#toolbar {
			font-family: Nexa,Arial,sans-serif;
			position: absolute;
			right: 10px;
			top: 21px;			
		}
		#toolbar label {
			cursor: pointer;
			margin-left: 5px;
			margin-right: 20px;			
		}
		#toolbar #execute,
		#toolbar #cls {
			background: none repeat scroll 0 0 silver;
			border: 1px none;
			font-family: Nexa;
			padding: 3px 12px;
			cursor: pointer;
		}
		#settings {
			display:inline;
		}
		#footer #settings{
			display: none;
		}
		#footer:hover #settings{
			display: inline;
		}
		#footer #timer{
			display: inline;
		}
		#footer:hover #timer{
	
		}
		#message {
			background: none repeat scroll 0 0 #EAECED;
			border: 1px solid #B5C1C6;
			display: none;
			left: 50%;
			margin-left: -200px;
			padding: 0.5em;
			position: absolute;
			width: 400px;
			z-index: 312;
			top: 13px;
			background-color: #FCFEFF;
			font-family: Nexa, Arial, Verdana, sans-serif;
			box-shadow: 0px 3px 39px rgba(9, 9, 9, 0.35);
		}
		#currsor {
			position: absolute;
			background-color: white;
			display: none;
		}
	</style>
</head>
<body>
	<style type="text/css">
</style>
	<div id="message" class="info">Message</div>
	<div id="wrapper">
		<textarea id="code" name="code" spellcheck="false" placeholder="type php code or 'help' and press [parse] button or F9 key ..."><?php echo $_SESSION['developer-code'] ?></textarea>
		<div id="output"></div>
	</div>
	<div id="footer">
		<h1>SANDBOX&trade;</h1>
		<div id="toolbar">
			<div id="settings">
				<input type="checkbox" id="loop" title="Loop execution"/><label for="loop">Loop execution</label>
				<input type="checkbox" id="clr_loop" title="Clear after exec"/><label for="clr_loop">Clear after execution</label>
				<input type="checkbox" id="smart_exec" title="Smart execution"/><label for="smart_exec">Smart execution</label>
			</div>
			<div id="timer">
				00:00
			</div>
			<input class="button" type="button" id="execute" value="parse" title="click or press F9"/>
			<input class="button" type="button" id="cls" value="clear"/>
		</div>
	</div>

	<script type="text/javascript"> 

		var interval = null;
		var keyboardListener = null;
		var pressDelay = 0;
		var execTime = 0;
		var colorSwitching = false;
		var hue = 177;
		var errorTime = 0;
		var colorTime = 0;
		
		$("#code").keydown(function (e) {
			
			if (e.keyCode == 9) {
				
				var myValue = "\t";
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
				this.focus();
				this.selectionStart = startPos + myValue.length;
				this.selectionEnd = startPos + myValue.length;
				this.scrollTop = scrollTop;

				e.preventDefault();
			}
		});
		
		$("#code").keyup(function (e) {
			
			if (e.keyCode == 120 || 0 && (e.keyCode == 13 || e.keyCode == 59) && $('#smart_exec').attr('checked') == 'checked') {
				$("#execute").trigger("click");
			}
			pressDelay = 0;
		});
		
		$("#execute").on('click', function(event) {
			
			$("#execute").css('background-color', '#ABABAB');
			
			$.post('../developer.php', {
				code: $("#code").val(),
				autoexec: $('#smart_exec').attr('checked') == 'checked' ? 1 : 0
			}).success(function(output) {
				
				execTime = 0;
				errors = '';
				oldErrors = '';
				
				$('#message').hide().html('');
				
				if ($("#clr_loop").attr("checked")) {
					$("#cls").click();
				}
				
				$('#output').append( output );
				$('#output').scrollTop(99999);
				$("#execute").css('background-color', 'silver');
				//$('#currsor').stop(true, true).show().delay(500).fadeOut();

			}).error(function(output) {
				
				$('#output').append( '<pre>Server internal error</pre>' );
				$('#output').append( output );
			});
			
		});

		$('#loop').on('click', function() {

			if ($(this).attr('checked') == 'checked') {
				interval = setInterval('$("#execute").click()', 300);
			}
			else {
				interval = window.clearInterval(interval);
			}
		});

		$("#cls").on('click', function() {
			$('#output').html("");
		});
		
		$('#footer h1').click(function() {
			colorSwitching = !colorSwitching;
		});
		
		$('#message').click(function() {
			$('#message').hide();
		});
		
		function eventTask() {
			
			pressDelay++;
			execTime += 0.1;
			
			if (pressDelay == 9 && $('#smart_exec').attr('checked') == 'checked') {
				$("#execute").trigger("click");
			}

			if (execTime < 60) {
				$('#timer').html(Math.floor(execTime) + ' sec ago');
			}
			else {
				$('#timer').html(Math.floor(execTime / 60) + ' min ' + Math.floor(execTime) % 60 + ' sec ago');
			}
			
			if (colorSwitching) {
				colorTime += 0.1;
				hue = colorTime * 20 % 360;
				
			}
			var lightness = 100 - execTime * 10;

			if (lightness <= 50) {
				lightness = 50;
			}
			$('#footer h1').css('color', 'hsl(' + hue + ',100%, ' + lightness + '%)');
		}
				
		keyboardListener = setInterval('eventTask()', 100);
		
		var errors = '';
		
		window.onerror = function errorHandler(message, url, lineNumber) {
			errors += '<p>' + message + ' [line: ' + lineNumber + ']</p>';
			$('#message').html(errors).show();
		}
		
	</script>
</body>
</html>