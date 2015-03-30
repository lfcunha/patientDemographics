/**
 * Created by luiscunha on 1/4/15.
 */
var aaa;




//$("#doctorsTable").children().last().after("1")




function addFamilyRow(){
    s="<tr>" +
        "<td></td>" +
        "<td><input name='doctorId' id='doctorId' type='text'></td>" +
        "</tr>";
    $("#familyTable").children().last().children().last().after(s);
    $("#addFamily").hide();
    $("#submitFamily").show();

}


function submitForm(){
    console.log("sf");
    var doctorId=$("#doctorId").val();


    $.ajax({
        type: "POST",
        url: "family",
        crossDomain: true,
        //data: {data:1},
        data: {doctorId:doctorId.toString()},
        success: function(response) {
            console.log(JSON.stringify(response));

             if (response==1){
                 window.location.href = "https://pintolab.mssm.edu/demographicsDB/family";

             }
                 /*
             //ajax call to a function to update database
             $(elem).parent().html(elem.value);
             $(elem).parent().css("border", "solid 1px #0f0");

             }
             else if (response==2) {
             //database error
             }
             else{
             //failed to pass validation
             $(elem).css("border-color", "#f00");
             }
             */

        } ,
        error: function(request, status, error) {
            console.error(error);
            console.log('error');
            // alert('Not working!');
        },
        dataType: "text json"
    });



}



function act(elem){
    var value= elem.value;
    var column = $(elem).parent().attr("name");
    var id= $(elem).parent().attr("id");

    $.ajax({
        type: "post",
        url: "RNA",
        crossDomain: true,
        data: {val:value, col:column, id:id},
        success: function(response) {
            console.log(JSON.stringify(response));
            if (response==1||response=="true" ||response==true){
                //ajax call to a function to update database
                console.log("ok - saved to db (DNA.js::pushEditToDB())");
                $(elem).parent().html(elem.value);
                $(elem).parent().css("border", "solid 1px #0f0");
            }
            else if (response==2) {
                //database error
            }
            else{
                //failed to pass validation
                $(elem).css("border-color", "#f00");
                var a='';
                for (x in response){
                    a=a+response[x];
                }
                console.log(a);
                $(elem).parent().html(a);
            }

        } ,
        error: function(request, status, error) {
            console.error(error);
            console.log('error');
            // alert('Not working!');
        },
        dataType: "text json"
    });
};


function generateEditInput(elem,name){
    var str='<input type="text" id="editField" value="'+name+'" onblur="pushEditToDB(this)">';
    $(elem).html(str)
    $(elem).children().focus();$(elem).children().css('width',$(elem).width());
}


function exportIsolates(){
    var ids= $("#submitAddExtractSampleIds").val();
}


$(document).ready(function(){
    //javascript functionality of clicking columns
    $("tbody").delegate("td.crosshair", "dblclick", function(){generateEditInput(this,$(this).html())});
    $("tbody").delegate("td.getMore", "click", function(){getMore($(this).parent().attr('id'))});
    $("tbody").delegate("td.getExtracts", "click", function(){$(this).find('.btn')[0].click()});
});




