
$count=1;
$(document).ready(function()
{   //Code to add additional fields
    $("#addField").click(function()
    {
      addField();
    });


   //code to remove the additional fields
   $("#removeField").click(function()
   {
     removeField();
   });

   //function to display the additional information
   $("#product").on("click", ".addInfor", function()
   {
      pid=$(this).attr('id'); 
      $.ajax({
         method:'POST',
         url:'http://localhost:8080/public/admin/products/findInfromation',
         data:{
             'id':pid,
         }
     }).done(function(data)
     {
         data=JSON.parse(data);
         displayAdditionalInformation(data);
     });
   });
});


function addField()
{
   $labelName="label"+$count;
   $valueName="value"+$count
   $div=document.createElement("div");
   $label=document.createElement("input");
   $value=document.createElement("input");
   $div.setAttribute("class","input-group");
   $div.setAttribute("id","div"+$count);
   $label.setAttribute("class"," my-3 form-control");
   $value.setAttribute("class","m-3 form-control");
   $label.setAttribute("placeholder","label");
   $value.setAttribute("placeholder","value");
   $label.setAttribute("name",$labelName);
   $value.setAttribute("name",$valueName);
   $label.setAttribute("id",$labelName);
   $value.setAttribute("id",$valueName);
   $label.setAttribute("required",true);
   $value.setAttribute("required",true);
   $form=document.getElementById("addProduct");
   $btn=document.getElementById("addField");
   $form.insertBefore($div,$btn);
   $div.appendChild($label);
   $div.appendChild($value);
   $count++;
}

/**
 * function to remove the field
 */
 function removeField()
 {
   if($count != 0)
   {
     $count--;
     console.log("remove");
     $div="div"+$count;
     console.log($div);
     $element=document.getElementById($div);
     $element.remove();
   } else {
     console.log("No add field found");
   }
 }
 

 /**
 * function to display the additional fields
 * @param {} data 
 */
function displayAdditionalInformation(data)
{
  html="";
  for (const key in data['data']) 
  {
    const value = data['data'][key];
    if(key== 'additionalFields')
    {
      console.log("hello");   
      if(value != null)
      {
        for (const key1 in value){
          const value1 = value[key1];
          html += `<tr>
                      <td>${key1}</td>
                      <td>${value1}</td>
                    </tr>`;
        }
      } else {
        html += `<p>!!!No Data Found !!!</p>`;
      }
    }
  }
 $("#addsInfor").html(html);
}
 