<?php
	if($_FILES)
    {
    	print_r($_FILES);
        die;
    }
	
?>
<!doctype html> 
<html> 
<head> 
<title>Proper Title</title> 
<style> #selectedFiles img { max-width: 200px; max-height: 200px; float: left; margin-bottom:10px; } </style> 
</head> 
<body> 
<form id="myForm" method="post"> Files: 
<input type="file" id="files" name="files[]" multiple>
<br/> 
<div id="selectedFiles"></div> 

<input type="submit"> 
</form> 


<script type="text/javascript" src="../../h5/jquery-1.11.2.min.js"></script>
<script> 
var selDiv = ""; 
var storedFiles = [];
 $(document).ready(function() {
	  $("#files").on("change", handleFileSelect); 
	  selDiv = $("#selectedFiles");
	  $("#myForm").on("submit", handleForm); 
	  $("body").on("click", ".selFile", removeFile); });
	  
	   function handleFileSelect(e) { 
		   var files = e.target.files; 
		   var filesArr = Array.prototype.slice.call(files); 
		   filesArr.forEach(function(f) {
				if(!f.type.match("image.*")) { return; } 
				storedFiles.push(f); 
				var reader = new FileReader();
				 reader.onload = function (e) {
					  var html = "<div><img src=\"" + e.target.result + "\" data-file='"+f.name+"' class='selFile' title='Click to remove'>" + f.name + "<br clear=\"left\"/></div>"; 
					  selDiv.append(html); 
				 } 
				reader.readAsDataURL(f); 
			}); 
		} 
			 
			 
			 function handleForm(e) {
				   e.preventDefault(); 
				   var data = new FormData();
				   for(var i=0, len=storedFiles.length; i<len; i++) {
					    data.append('files[]', storedFiles[i]); 
				   } 
					var xhr = new XMLHttpRequest(); 
					xhr.open('POST', '1.php', true); 
					xhr.onload = function(e) { 
						if(this.status == 200) { 
							console.log(e.currentTarget.responseText); 
							alert(e.currentTarget.responseText + ' items uploaded.'); 
						} 
					} 
					xhr.send(data);
				} 
					
					
					
					function removeFile(e) {
						  var file = $(this).data("file"); 
						  for(var i=0;i<storedFiles.length;i++) { 
						  	if(storedFiles[i].name === file) { 
							  storedFiles.splice(i,1); 
							  break; 
						  	} 
						  } 
						  $(this).parent().remove(); 
					} 
</script> 
</body> 
</html> 