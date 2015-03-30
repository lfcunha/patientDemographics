var aaa;

function act(elem){
    var value= elem.value;
    var column = $(elem).parent().attr("name");
    var id= $(elem).parent().attr("id");

    $.ajax({
        type: "post",
        url: "viablecell",
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




