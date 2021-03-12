 <?php include('functions.php');?>
 <?php include('jsontest.php');?>
   <head>

         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
         <title>NSFW PTR</title>
         <link href="style/style.css" rel="stylesheet" type="text/css">

 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  <script  type='text/javascript'>
   $(document).ready(function(){    
   $(".submit").hover(       
   function() {
      $(this).attr("src","img/submithover.png");},        
   function() {
      $(this).attr("src","img/submit.png");   
 });
});
 </script>

 
   </head>
 <body>
        <!-- Start of logo div -->

           <div id="logo">
                   </div>
                 <!-- End of logo div -->

           <!-- Start of registration_boxes div -->
  
                <div id="registration_boxes">
				<div style="position:absolute; margin-top:-80px; margin-left:50px;"><?php if($_GET['do']=="copy") { char_copy();}elseif($_GET['error']) { Error("".$_GET['error']."");}else{ ?></div>
				
				
                <FORM ACTION="?do=copy" METHOD="POST">
                 <p CLASS="Username"> Character to copy </p>
                  <div class="usernameform"> <INPUT name="name" type="text" value="Character name" onFocus="this.value=''" /> </div>


<p CLASS="Username"> Server: </p>
<div class="usernameform"><select style="margin-left:5px; margin-top:5px; height:30px; width:230px; color:#000000;  -moz-border-radius: 5px; border-radius: 5px;" id="sales" name="flags">
<option value="0">Mandokir</option>
<option value="8">Sorry, solo Mandokir</option>
</select></div>
					  
                        <INPUT TYPE="image" SRC="img/submit.png" type="submit" value=" " id="button" ALT="Copy your character" CLASS="submit" STYLE="margin-left:25px;">
                    </FORM>
					
					<?php }?>
					</div>

             <!-- End of registration_boxes div -->
  </body>
 </html>