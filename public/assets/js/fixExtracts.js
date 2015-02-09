/**
 * Created by luiscunha on 8/21/14.
 */
var rows;

$(window).keydown(function(event){
    if(event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
})

function validate(val){
    console.log("fired4");
    var id="#"+val;
    var column=parseInt(val.split("_")[1])
    if (column==1) column=7;

    $.support.cors = true;

    $.ajax({
        type: "POST",
        url: "validate2",
        crossDomain: true,
        //data: {data:1},
        data: {cell:val, data:$(id).val(), row:val.split("_")[0], col:column+25},

        success: function(response) {
            console.log(JSON.stringify(response));
            if (response==1){
                //$(id).attr("disabled", "disabled");
                $(id).attr("class", "fixed btn");
                $(id).tooltip('destroy');
                //$(id).blur();

                var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";
                $(sel_fa).css("display", "none");

                var a =$(".fix").length<1;
                if(a){
                    $("#submitbutton").removeAttr('disabled');
                    $("#submitbutton").attr("class", "btn btn-primary");
                }
                console.log(a);
            }
            else {
                $(id).attr("class", "fix btn");
                $(id).tooltip('show');
                var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";
                $(sel_fa).css("display", "block");
            }

            //alert(response);
        },
        error: function(request, status, error) {
            console.error(error);
            console.log('error');
            // alert('Not working!');
        },
        dataType: "text json"
    });

}
//array to hold sample identifiers (to check for duplicates)
var sids=new Array();
$( document ).ready(function() {
    $('.notdb').tooltip({
        trigger: 'hover focus',
        html:true
    });

    $('.db').tooltip({
        trigger: 'hover focus',
        html:true
    });

    $('.dbicon').popover({
        trigger: 'click',
        html: true
    });


});

//build array of sample identifiers, to check for duplicates in the validate function






function closePopover(value){
    var id="#"+value;

    var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";

    $(sel_fa).popover("hide");
};

function redirect(path){
    window.location.replace("/databases/influenza/" + path);
};



(function(){
    if(typeof(rows)!=undefined){
        for (var i=0; i<rows; i++){
            var id="#"+i+"_"+3;
            var sidsValue=$(id).val();
            if (sids.indexOf(sidsValue)<0){
                sids.push($(id).val())
            }
        }}})();




