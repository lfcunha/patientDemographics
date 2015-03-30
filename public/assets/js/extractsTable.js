/**
 * Created by luiscunha on 8/21/14.
 */



function act(elem, source){
    if (!source){
    var value= elem.value;
    var name = $(elem).parent().attr("name");
    var id= $(elem).parent().attr("id");
    }
    else {
        var value=source.value;
        var name = source.name;
        var id=source.id;
    }


    $.ajax({
        type: "POST",
        url: "saveEditToDB",
        crossDomain: true,
        //data: {data:1},
        data: {value:value, col:name, id:id, table:"extracts"},
        success: function(response) {
            //console.log(JSON.stringify(response));


            if (response==1){

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

    var str='<input type="text" id="#editField" value="'+name+'" onblur="pushEditToDB(this)">';

    $(elem).html(str)
    $(elem).children().focus();$(elem).children().css('width',$(elem).width());

}

$(document).ready(function(){
if (navigator.userAgent.search("Firefox") >= 0) {
    console.log(1);

   $("#dataTables_filter").html('<div id="isolatesTable_filter" class="dataTables_filter"> <label>Quick Search:<div class="input-group"><input type="search" role="search" id="quickSearch" class="form-control input-sm" placeholder="" aria-controls="isolatesTable" ><span class="input-group-addon" onclick="$(&#39;#quickSearch&#39;).val(&#39;&#39;);eraseSearch()"> x</span></div><i class="fa fa-search fa-3" id="faAvancedSearch" onclick="quickSearch()" style="margin-left:5px"></i></label></div>');
};
});