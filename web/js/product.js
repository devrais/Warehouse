function enableFileInput()
{
   if($('#radio-update-picture').is(':checked')) 
   { 
       $('#product-file').removeAttr('disabled');
   }else{
       $('#product-file').prop('disabled','disabled');
   }
    
   // alert("Fuck yeah");
}


